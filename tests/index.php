<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2019 51 Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY.
 *
 * This Original Work is licensed under the European Union Public Licence (EUPL) 
 * v.1.2 and is subject to its terms as set out below.
 *
 * If a copy of the EUPL was not distributed with this file, You can obtain
 * one at https://opensource.org/licenses/EUPL-1.2.
 *
 * The 'Compatible Licences' set out in the Appendix to the EUPL (as may be
 * amended by the European Commission) shall be deemed incompatible for
 * the purposes of the Work and the provisions of the compatibility
 * clause in Article 5 of the EUPL shall not apply.
 * 
 * If using the Work as, or as part of, a network application, by 
 * including the attribution notice(s) required under Article 5 of the EUPL
 * in the end user terms of the application under an appropriate heading, 
 * such notice(s) shall fulfill the requirements of that article.
 * ********************************************************************* */


require(__DIR__ . "/../vendor/autoload.php");

use fiftyone\pipeline\core\flowElement;
use fiftyone\pipeline\core\pipelineBuilder;
use fiftyone\pipeline\core\elementDataDictionary;
use fiftyone\pipeline\core\basicListEvidenceKeyFilter;
use fiftyone\pipeline\core\logger;

use PHPUnit\Framework\TestCase;

class exampleFlowElement1 extends flowElement {

    public $dataKey = "example1";
    public function processInternal($flowData){

        $data = new elementDataDictionary($this, array("integer" => 5));

        $flowData->setElementData($data);

    }

    public $properties = array(
        "integer" => array(
            "type" => "int"
        )
    );
    
    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }
    
}

class exampleFlowElement2 extends flowElement {

    public $dataKey = "example2";

    public function processInternal($flowData){

        $data = new elementDataDictionary($this, array("integer" => 7));

        $flowData->setElementData($data);

    }

    public $properties = array(
        "integer2" => array(
            "type" => "int"
        )
    );

    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }
    
}

class errorFlowData extends flowElement {

    public $dataKey = "error";

    public function processInternal($flowData){

        throw new Exception("Something went wrong");

    }

    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }
    
}

class stopFlowData extends flowElement {

    public $dataKey = "stop";

    public function processInternal($flowData){

        $flowData->stop();

    }

    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }
    
}

class memoryLogger extends logger {

    public $log = [];

    public function logInternal($log){

        if($log["message"] === "test"){

            $this->log[] = $log;

        }

    }

}

// Test pipeline builder for use with PHP unit tests
class testPipelineBuilder
{
	public $pipeline;

	public $flowElement1;

	public $flowData;

	public $logger;

	public function __construct () 
	{
		$this->logger = new memoryLogger("info");
		$this->flowElement1 = new exampleFlowElement1();
		$this->pipeline = (new pipelineBuilder())
			->add($this->flowElement1)
			->add(new errorFlowData())
			->add(new stopFlowData())
			->add(new exampleFlowElement2())
			->addLogger($this->logger)
			->build();
        $this->flowData = $this->pipeline->createFlowData();
		$this->flowData->evidence->set("header.user-agent", "test");
		$this->flowData->evidence->set("some.other-evidence", "test");
		$this->flowData->process();
    }
}

class CoreTests extends TestCase
{
	// Test logging works
    public function testLogger()
    {
		$testPipeline = new testPipelineBuilder();
		$loggerMessage = $testPipeline->logger->log[0]["message"];
		$this->assertTrue($loggerMessage === "test");
	}
	
	// Test getting evidence
	public function testEvidence()
	{
		$testPipeline = new testPipelineBuilder();
		$userAgent = $testPipeline->flowData->evidence->get("header.user-agent");
		$this->assertTrue($userAgent === "test");
	}

	// Test filtering evidence
	public function testEvidenceKeyFilter()
	{
		$testPipeline = new testPipelineBuilder();
		$nullEvidence = $testPipeline->flowData->evidence->get("header.other-evidence");
		$this->assertTrue($nullEvidence === null);
	}

	// Test Getter methods
	public function testGet()
	{
		$testPipeline = new testPipelineBuilder();
		$getValue = $testPipeline->flowData->get("example1")->get("integer");
		$this->assertTrue($getValue === 5);
	}

	public function testGetWhere()
	{
		$testPipeline = new testPipelineBuilder();
		$getValue = count($testPipeline->flowData->getWhere("type", "int"));
		$this->assertTrue($getValue === 1);
	}

	public function testGetFromElement()
	{
		$testPipeline = new testPipelineBuilder();
		$getValue = $testPipeline->flowData->getFromElement($testPipeline->flowElement1)->get("integer");
		$this->assertTrue($getValue === 5);
	}

	// Test check stop flowData works
	public function testStopFlowData()
	{
		$testPipeline = new testPipelineBuilder();
		$getValue = $testPipeline->flowData->get("example2");
		$this->assertTrue($getValue === null);
	}

	// Test errors are returned
	public function testErrors()
	{
		$testPipeline = new testPipelineBuilder();
		$getValue = $testPipeline->flowData->errors["error"];
		$this->assertTrue(isset($getValue));
	}

	// Test if adding properties at a later stage works (for cloud flowElements for example)
	public function testUpdateProperties()
	{
		$flowElement1 = new exampleFlowElement1();
		$logger = new memoryLogger("info");
		$pipeline = (new pipelineBuilder())->add($flowElement1)->add(new errorFlowData())->add(new stopFlowData())->add(new exampleFlowElement2())->addLogger($logger)->build();
		$flowElement1->properties["integer"]["testing"] = "true";		
		$flowData = $pipeline->createFlowData();		
		$flowData->evidence->set("header.user-agent", "test");
		$flowData->evidence->set("some.other-evidence", "test");		
		$flowData->process();

		$getValue = count($flowData->getWhere("testing", "true"));
		$this->assertTrue($getValue === 0);
		$flowElement1->updatePropertyList();
		$getValue = count($flowData->getWhere("testing", "true"));
		$this->assertTrue($getValue === 1);
	}
}

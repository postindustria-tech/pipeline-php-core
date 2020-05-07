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
use fiftyone\pipeline\core\aspectPropertyValue;
use fiftyone\pipeline\core\jsonBundlerElement;
use fiftyone\pipeline\core\javascriptBuilderElement;
use fiftyone\pipeline\core\sequenceElement;

use PHPUnit\Framework\TestCase;

class testEngine extends flowElement {

    public $dataKey = "test";

    public $properties = array(
        "javascript" => array(
            "type" => "javascript"
        ),
        "apvGood" => array(
            "type" => "string"
        ),
        "apvBad" => array(
            "type" => "string"
        ),
        "normal" => array(
            "type" => "boolean"
        )
    );
    
    public function processInternal($flowData){

        $contents = [];

        $contents["javascript"] = "console.log('hello world')";
        $contents["normal"] = true;

        $contents["apvGood"] = new aspectPropertyValue(null, "Value");
        $contents["apvBad"] = new aspectPropertyValue("No value");

        $data = new elementDataDictionary($this, $contents);

        $flowData->setElementData($data);

    }

}

class testPipeline {

    public function __construct(){

        $this->pipeline = (new pipelineBuilder())
        ->add(new testEngine())
        ->build();

    }

}

class JavaScriptBundlerTests extends TestCase {

    public function testJSONBundler(){

        $pipeline = (new testPipeline())->pipeline;

        $flowData = $pipeline->createFlowData();

        $flowData->process();

        $expected = array (
            'javascriptProperties' => 
            array (
              0 => 'test.javascript',
            ),
            'test' => 
            array (
              'javascript' => 'console.log(\'hello world\')',
              'apvgood' => 'Value',
              'apvbad' => NULL,
              'apvbadnullreason' => 'No value',
              'normal' => true,
            )
        );

        $this->assertEquals($flowData->jsonbundler->json, $expected);
    
    }

    // public function testJavaScriptBuilder(){

    //     $pipeline = (new testPipeline())->pipeline;

    //     $flowData = $pipeline->createFlowData();

    //     $flowData->process();

    //     $expected = file_get_contents(__DIR__ . "/jsoutput.js");

    //     $this->assertEquals($flowData->javascriptbuilder->javascript, $expected);

    // }

    public function testSequence(){

        $pipeline = (new testPipeline())->pipeline;

        $flowData = $pipeline->createFlowData();

        $flowData->evidence->set("query.session-id", "test");
        $flowData->evidence->set("query.sequence", 10);

        $flowData->process();

        $this->assertEquals($flowData->evidence->get("query.sequence"), 11);

        $this->assertEquals(count($flowData->jsonbundler->json["javascriptProperties"]), 0);

    }

}

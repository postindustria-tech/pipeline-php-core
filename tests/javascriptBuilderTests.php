<?php

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
              'apvGood' => 'Value',
              'apvBad' => NULL,
              'apvBadnullreason' => 'No value',
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

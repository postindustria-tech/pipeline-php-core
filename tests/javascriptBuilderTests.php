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

use fiftyone\pipeline\core\FlowElement;
use fiftyone\pipeline\core\PipelineBuilder;
use fiftyone\pipeline\core\ElementDataDictionary;
use fiftyone\pipeline\core\AspectPropertyValue;
use fiftyone\pipeline\core\JsonBundlerElement;
use fiftyone\pipeline\core\JavascriptBuilderElement;
use fiftyone\pipeline\core\SequenceElement;

use PHPUnit\Framework\TestCase;

class TestEngine extends FlowElement
{
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
    
    public function processInternal($FlowData)
    {
        $contents = [];

        $contents["javascript"] = "console.log('hello world')";
        $contents["normal"] = true;

        $contents["apvGood"] = new AspectPropertyValue(null, "Value");
        $contents["apvBad"] = new AspectPropertyValue("No value");

        $data = new ElementDataDictionary($this, $contents);

        $FlowData->setElementData($data);
    }
}

class TestPipeline
{
    public function __construct()
    {
        $this->Pipeline = (new PipelineBuilder())
        ->add(new TestEngine())
        ->build();
    }
}

class JavaScriptBundlerTests extends TestCase
{
    public function testJSONBundler()
    {
        $Pipeline = (new TestPipeline())->Pipeline;

        $FlowData = $Pipeline->createFlowData();

        $FlowData->process();

        $expected = array(
            'javascriptProperties' =>
            array(
              0 => 'test.javascript',
            ),
            'test' =>
            array(
              'javascript' => 'console.log(\'hello world\')',
              'apvgood' => 'Value',
              'apvbad' => null,
              'apvbadnullreason' => 'No value',
              'normal' => true,
            )
        );

        $this->assertEquals($FlowData->jsonbundler->json, $expected);
    }

    public function testSequence()
    {
        $Pipeline = (new TestPipeline())->Pipeline;

        $FlowData = $Pipeline->createFlowData();

        $FlowData->evidence->set("query.session-id", "test");
        $FlowData->evidence->set("query.sequence", 10);

        $FlowData->process();

        $this->assertEquals($FlowData->evidence->get("query.sequence"), 11);

        $this->assertEquals(count($FlowData->jsonbundler->json["javascriptProperties"]), 0);
    }
}

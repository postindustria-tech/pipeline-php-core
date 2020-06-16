<?php

namespace fiftyone\pipeline\core\tests;

use fiftyone\pipeline\core\FlowElement;
use fiftyone\pipeline\core\ElementDataDictionary;
use fiftyone\pipeline\core\BasicListEvidenceKeyFilter;

class ExampleFlowElement2 extends FlowElement
{
    public $dataKey = "example2";

    public function processInternal($flowData)
    {
        $data = new ElementDataDictionary($this, array("integer" => 7));

        $flowData->setElementData($data);
    }

    public $properties = array(
        "integer2" => array(
            "type" => "int"
        )
    );

    public function getEvidenceKeyFilter()
    {
        return new BasicListEvidenceKeyFilter(["header.user-agent"]);
    }
}

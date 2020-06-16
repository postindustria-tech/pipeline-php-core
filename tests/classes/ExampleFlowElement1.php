<?php

namespace fiftyone\pipeline\core\tests;

use fiftyone\pipeline\core\FlowElement;
use fiftyone\pipeline\core\ElementDataDictionary;
use fiftyone\pipeline\core\BasicListEvidenceKeyFilter;

class ExampleFlowElement1 extends FlowElement
{
    public $dataKey = "example1";
    public function processInternal($flowData)
    {
        $data = new ElementDataDictionary($this, array("integer" => 5));

        $flowData->setElementData($data);
    }

    public $properties = array(
        "integer" => array(
            "type" => "int"
        )
    );
    
    public function getEvidenceKeyFilter()
    {
        return new BasicListEvidenceKeyFilter(["header.user-agent"]);
    }
}

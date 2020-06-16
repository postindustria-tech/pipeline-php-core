<?php

namespace fiftyone\pipeline\core\tests;

use fiftyone\pipeline\core\FlowElement;
use fiftyone\pipeline\core\BasicListEvidenceKeyFilter;

class ErrorFlowData extends FlowElement
{
    public $dataKey = "error";

    public function processInternal($flowData)
    {
        throw new \Exception("Something went wrong");
    }

    public function getEvidenceKeyFilter()
    {
        return new BasicListEvidenceKeyFilter(["header.user-agent"]);
    }
}

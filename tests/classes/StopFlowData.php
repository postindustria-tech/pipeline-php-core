<?php

namespace fiftyone\pipeline\core\tests;

use fiftyone\pipeline\core\FlowElement;
use fiftyone\pipeline\core\BasicListEvidenceKeyFilter;

class StopFlowData extends FlowElement
{
    public $dataKey = "stop";

    public function processInternal($flowData)
    {
        $flowData->stop();
    }

    public function getEvidenceKeyFilter()
    {
        return new BasicListEvidenceKeyFilter(["header.user-agent"]);
    }
}


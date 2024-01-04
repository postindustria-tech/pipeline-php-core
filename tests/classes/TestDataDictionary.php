<?php

namespace fiftyone\pipeline\core\tests\classes;

use fiftyone\pipeline\core\ElementDataDictionary;
use fiftyone\pipeline\core\FlowElement;

class TestDataDictionary extends ElementDataDictionary
{
    public function __construct(?FlowElement $flowElement, array $contents)
    {
        $this->contents = $contents;
    }
}

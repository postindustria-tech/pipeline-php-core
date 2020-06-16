<?php

namespace fiftyone\pipeline\core\tests;

use fiftyone\pipeline\core\Logger;

class MemoryLogger extends Logger
{
    public $log = [];

    public function logInternal($log)
    {
        if ($log["message"] === "test") {
            $this->log[] = $log;
        }
    }
}
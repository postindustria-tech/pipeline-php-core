<?php

namespace fiftyone\pipeline\core\tests;

require(__DIR__ . "/ErrorFlowData.php");
require(__DIR__ . "/StopFlowData.php");
require(__DIR__ . "/MemoryLogger.php");
require(__DIR__ . "/ExampleFlowElement1.php");
require(__DIR__ . "/ExampleFlowElement2.php");

use fiftyone\pipeline\core\PipelineBuilder;

// Test Pipeline builder for use with PHP unit tests
class TestPipeline
{
    public $pipeline;

    public $flowElement1;

    public $flowData;

    public $logger;

    public function __construct($suppressException = true)
    {
        $this->logger = new MemoryLogger("info");
        $this->flowElement1 = new ExampleFlowElement1();
        $this->pipeline = (new PipelineBuilder())
            ->add($this->flowElement1)
            ->add(new ErrorFlowData())
            ->add(new StopFlowData())
            ->add(new ExampleFlowElement2())
            ->addLogger($this->logger)
            ->build();
        $this->pipeline->suppressProcessExceptions = $suppressException;
        $this->flowData = $this->pipeline->createFlowData();
        $this->flowData->evidence->set("header.user-agent", "test");
        $this->flowData->evidence->set("some.other-evidence", "test");
        $this->flowData->process();
    }
}

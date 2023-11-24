<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2023 51 Degrees Mobile Experts Limited, Davidson House,
 * Forbury Square, Reading, Berkshire, United Kingdom RG1 3EU.
 *
 * This Original Work is licensed under the European Union Public Licence
 * (EUPL) v.1.2 and is subject to its terms as set out below.
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

namespace fiftyone\pipeline\core;

/**
 * A PipelineBuilder generates a Pipeline object
 * Before construction of the Pipeline, FlowElements are added to it
 * There are also options for how JavaScript is output from the Pipeline.
 */
class PipelineBuilder
{
    public $pipelines;
    public $addJavaScriptBuilder;
    public $javascriptBuilderSettings = [];
    public $useSetHeaderProperties;
    protected $flowElements = [];
    protected $settings = [];

    public function __construct($settings = [])
    {
        // List of Pipelines the FlowElement has been added to
        $this->pipelines = [];

        $this->addJavaScriptBuilder = (bool) ($settings['addJavaScriptBuilder'] ?? true);
        $this->javascriptBuilderSettings = $settings['javascriptBuilderSettings'] ?? [];
        $this->useSetHeaderProperties = (bool) ($settings['useSetHeaderProperties'] ?? true);
    }

    /**
     * Add FlowElement to be used in Pipeline.
     *
     * @param FlowElement $flowElement
     * @return static
     */
    public function add($flowElement)
    {
        $this->flowElements[] = $flowElement;

        return $this;
    }

    /**
     * Build Pipeline once done.
     *
     * @return Pipeline
     */
    public function build()
    {
        $this->flowElements = array_merge(
            $this->flowElements,
            $this->getJavaScriptElements(),
            $this->getSetHeaderElements()
        );

        return new Pipeline($this->flowElements, $this->settings);
    }

    /**
     * Add an instance of the logger class to the Pipeline.
     *
     * @param Logger $logger
     * @return static
     */
    public function addLogger($logger)
    {
        $this->settings['logger'] = $logger;

        return $this;
    }

    /**
     * Build from a JSON configuration file
     * This JSON file should have the following structure:
     * `{
     * "PipelineOptions": {
     * "Elements": [
     *  {
     *    "BuilderName": // Name of element as in use statement,
     *    "BuildParameters": {
     *      // An object of parameters passed to the constructor
     *   }
     *  }]
     * }`.
     *
     * @param array|string $fileOrConfig Filename of the config file to load config or associative array of config values
     * @return Pipeline
     */
    public function buildFromConfig($fileOrConfig)
    {
        if (is_string($fileOrConfig)) {
            $config = json_decode(file_get_contents($fileOrConfig), true);
        } else {
            $config = $fileOrConfig;
        }

        foreach ($config['PipelineOptions']['Elements'] as $element) {
            $builder = $element['BuilderName'];

            if (isset($element['BuildParameters'])) {
                $flowElement = new $builder($element['BuildParameters']);
            } else {
                $flowElement = new $builder();
            }

            $this->flowElements[] = $flowElement;
        }

        return new Pipeline($this->flowElements, $this->settings);
    }

    /**
     * @return FlowElement[]
     */
    private function getJavaScriptElements()
    {
        $flowElements = [];

        if ($this->addJavaScriptBuilder) {
            // Add JavaScript elements
            $flowElements[] = new SequenceElement();
            $flowElements[] = new JsonBundlerElement();

            if ($this->javascriptBuilderSettings === null) {
                $flowElements[] = new JavascriptBuilderElement([]);
            } else {
                $flowElements[] = new JavascriptBuilderElement($this->javascriptBuilderSettings);
            }
        }

        return $flowElements;
    }

    /**
     * @return FlowElement[]
     */
    private function getSetHeaderElements()
    {
        $flowElements = [];

        if ($this->useSetHeaderProperties) {
            // Add SetHeader elements
            $flowElements[] = new SetHeaderElement();
        }

        return $flowElements;
    }
}

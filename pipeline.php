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

namespace fiftyone\pipeline\core;

require_once(__DIR__ . "/flowData.php");
require_once(__DIR__ . "/logger.php");

/**
* Pipeline holding a list of flowElements for processing, can create flowData that will be passed through these, collecting elementData
* Should be constructed through the PipelineBuilder class
**/
class pipeline {

    public $flowElements;
    public $flowElementsList = array();
    public $logger;
    public $metaDataStore;

    /**
     * Pipeline constructor
     * @param array list of flowElements
     * @param array settings array
    */
    public function __construct($flowElements, $settings) {

        if(!isset($settings["logger"])){

            $this->logger = new logger(null, null);

        } else {

            $this->logger = $settings["logger"];

        }

        $this->log("info", "test");

        $this->flowElements = $flowElements;

        $this->propertyDatabase = array();

        forEach($flowElements as $flowElement){

            $this->flowElementsList[$flowElement->dataKey] = $flowElement;

            $flowElement->pipelines[] = $this;

            $this->updatePropertyDatabaseForFlowElement($flowElement);

        }

    }

    /**
     * Create a flowData based on what's in the pipeline
     * @return flowData
    */
    public function createFlowData(){

        return new flowData($this, $this->flowElements);

    }

    public function log($level, $message){

        $this->logger->log($level, $message);

    }

    /**
     * Get a flowElement by its name
     * @param String name
     * @return flowElement
     */
    public function getElement($key){

        return $this->flowElementsList[$key];

    }

    /**
     * Update metadata store for a flowElement based on its list of properties
     * @param flowElement
     */
    public function updatePropertyDatabaseForFlowElement($flowElement){

        $dataKey = $flowElement->dataKey;

        // First unset any properties stored by the flowElement

        foreach ($this->propertyDatabase as $propertyValues) {

            foreach ($propertyValues as $propertyList) {

                foreach ($propertyList as $key => $info) {
                    
                    if($info["flowElement"] === $dataKey){

                        unset($propertyList[$key]);

                    }

                }
                
            }
            
        }

        $properties = $flowElement->getProperties();

        forEach($properties as $key => $property){

            forEach($property as $metaKey => $metaValue){

                $metaKey = \strtolower($metaKey);


                if (!isset($this->propertyDatabase[$metaKey])) {

                    $this->propertyDatabase[$metaKey] = array();

                }

                if(is_string($metaValue)){

                    $metaValue = \strtolower($metaValue);

                } else {

                    continue;

                }

                if (!isset($this->propertyDatabase[$metaKey][$metaValue])) {

                    $this->propertyDatabase[$metaKey][$metaValue] = array();

                }

                $property["flowElement"] = $dataKey;

                $this->propertyDatabase[$metaKey][$metaValue][$key] = $property;

            }
    
        }
        
    }

}

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

class jsonBundlerElement extends flowElement {

    public $dataKey = "jsonbundler";

    /**
     * The JSONBundler extracts all properties from a flowData and serializes them into JSON
     * @param {flowData} flowData
    */
    public function processInternal($flowData) {
    // Get every property on every flowElement
    // Storing JavaScript properties in an extra section

    $output = [
        "javascriptProperties" => []
    ];

    foreach ($flowData->pipeline->flowElements as $flowElement){

        if($flowElement->dataKey === "jsonbundler" || $flowElement->dataKey === "sequence" || $flowElement->dataKey === "javascriptbuilder"){

            continue;

        }

        // Create empty area for flowElement properties to go
        $output[$flowElement->dataKey] = [];

        $properties = $flowElement->getProperties();

        foreach($properties as $propertyKey => $property){

            $value;
            $nullReason = "Unknown";

            try {

                $valueContainer = $flowData->get($flowElement->dataKey)->get($propertyKey);
                
                // Check if value is of the aspect property value type
      
                if (is_object($valueContainer) && property_exists($valueContainer, "hasValue")) {
                  // Check if it has a value
      
                  if ($valueContainer->hasValue) {
                    $value = $valueContainer->value;
                  } else {

                    $value = null;      
                    $nullReason = $valueContainer->noValueMessage;

                  }

                } else {
                  
                    // Standard value
      
                  $value = $valueContainer;

                }

              } catch (Exception $e) {

                // Catching missing property exceptions and other errors
      
                continue;
              }
      
              $output[$flowElement->dataKey][$propertyKey] = $value;
              if($value == null){
                $output[$flowElement->dataKey][$propertyKey . "nullreason"] = $nullReason;
              }
    
              $sequence = $flowData->evidence->get("query.sequence");

              if(!$sequence || $sequence < 10) {

                // Cloud properties come back as capitalized
                // TODO change this, but for now

                if(isset($property["Type"])){

                  $property["type"] = $property["Type"];

                }
      
                if (isset($property["type"]) && strtolower($property["type"]) === "javascript"
                ) {
                  $output["javascriptProperties"][] = $flowElement->dataKey . "." . $propertyKey;
                }

              }

            }
        }

        $data = new elementDataDictionary($this, ["json" => $output]);

        $flowData->setElementData($data);

        return;

    }
}

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
 * ********************************************************************* 
**/

use fiftyone\pipeline\core\flowElement;
use fiftyone\pipeline\core\elementDataDictionary;
use fiftyone\pipeline\core\basicListEvidenceKeyFilter;

// Two simple flowElements

class exampleFlowElementA extends flowElement {

    public $dataKey = "example1";
    public function processInternal($flowData){

        $data = new elementDataDictionary($this, array("exampleProperty1" => 5));

        $flowData->setElementData($data);

    }

    public $properties = array(
        "exampleProperty1" => array(
            "type" => "int"
        )
    );

    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }

}

class exampleFlowElementB extends flowElement {

    public $dataKey = "example2";

    public function processInternal($flowData){

        $data = new elementDataDictionary($this, array("exampleProperty2" => 7));

        $flowData->setElementData($data);

    }

    public $properties = array(
        "exampleProperty2" => array(
            "type" => "int"
        )
    );

    public function getEvidenceKeyFilter(){

        return new basicListEvidenceKeyFilter(["header.user-agent"]);

    }

}


if (!class_exists("errorFlowElement")) {

// A flowElement that triggers an error

    class errorFlowElement extends flowElement {

        public $dataKey = "error";

        public function processInternal($flowData){

            throw new Exception("Something went wrong");

        }

        public function getEvidenceKeyFilter(){

            return new basicListEvidenceKeyFilter(["header.user-agent"]);

        }

    }

}


if (!class_exists("stopFlowElement")) {

    // A flowElement that stops processing

    class stopFlowElement extends flowElement {

        public $dataKey = "stop";

        public function processInternal($flowData){

            $flowData->stop();

        }

        public function getEvidenceKeyFilter(){

            return new basicListEvidenceKeyFilter(["header.user-agent"]);

        }

    }

}

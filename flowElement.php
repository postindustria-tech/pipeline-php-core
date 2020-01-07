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
require_once(__DIR__ . "/evidenceKeyFilter.php");

/**
    * flowElement can read evidence from flowData and set data on it when processed. 
    * Added to a pipeline 
*/
class flowElement {

    public function __construct(){

        // List of pipelines the flowElement has been added to
        $this->pipelines = [];

    }

    public $dataKey;
    public $properties = [];
    
    /**
     * General wrapper function that calls processInternal
     * Not called directly
    */
    public function process($flowData) {

        return $this->processInternal($flowData);

    }

    /**
     * Function for getting the flowElement's evidenceKeyFilter
     * Used by the filterEvidence method
    */
    public function getEvidenceKeyFilter(){

        return new evidenceKeyFilter();

    }

    /**
     * Filter flowData evidence using the flowElement's evidenceKeyFilter
     * @param flowData
     * @return mixed
    */
    public function filterEvidence($flowData){

        $filter = $this->getEvidenceKeyFilter();

        return $filter->filterEvidence($flowData->evidence->getAll());

    }

    public function filterEvidenceKey($key){

        $filter = $this->getEvidenceKeyFilter();

        return $filter->filterEvidenceKey($key);

    }

    /**
     * Process flowData
     * @param flowData
    */
    public function processInternal($flowData){

        return true;

    }

    public function getProperties(){

        return $this->properties;
    
    }

    public function updatePropertyList(){

        foreach($this->pipelines as $pipeline){

            $pipeline->updatePropertyDatabaseForFlowElement($this);

        }

    }

}

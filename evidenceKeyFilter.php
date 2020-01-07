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

/**
  * An evidenceKeyFilter added to a flowElement 
  * Allows a flowElement to return a subset of evidence it's interested in
*/
class evidenceKeyFilter {

    /**
    * filterevidence from an object
    * @param mixed[] evidence dicitonary contents
    * @return mixed[] filtered evidence dictionary contents
    */
    public function filterEvidence($evidenceKeyObject) {

        $filtered = array();

        forEach($evidenceKeyObject as $key => $value){

            if($this->filterEvidenceKey($key)) {

                $filtered[$key] = $value;

            }

        };

        return $filtered;

    }

    /**
    * see if a property key should be in the filtered evidence
    * @param string property name
    * @return boolean should this be filtered out or not?
    */
    public function filterEvidenceKey($key) {

        return true;

    }
    
}

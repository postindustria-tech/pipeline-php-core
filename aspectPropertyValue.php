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
* aspectPropertyValue is a wrapper for a value that comes out of an engine
* it allows you to check if a property has a meaningful value (->hasValue())
* It throws an error with a message about why the value isn't available if the
* value property is accessed and there is no value.
*  
*/
class aspectPropertyValue {

    /**
    * Constructor for aspectPropertyValue
    * @param string if there is no value, the reason for there not being one
    * @param mixed the value
    */
    public function __construct($noValueMessage = null, $value = "noValue"){

        if($value !== "noValue"){

            $this->value = $value;
            $this->noValueMessage = null;
            $this->hasValue = true;
            
        }
        
        if($noValueMessage){
            
            $this->hasValue = false;
            $this->noValueMessage = $noValueMessage;

        }

    }

    /**
    * Magic getter to access the value or throw an error with the no value message
    * @param string key
    * @return mixed value
    */
    public function __get($key){

        if($key === "value" && $this->noValueMessage){

            throw new \Exception($this->noValueMessage);
            
        }

    }

}
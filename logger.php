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
    * Logging for a pipeline 
*/
class logger {

    private $location;
    private $minLevel;
    
    private $levels = ["trace", "debug", "information", "warning", "error", "critical"];

    /**
     * Create a logger
     * @param string level ("trace", "debug", "information", "warning", "error", "critical")
     * @param array settings - customs settings for a logger
     *     
    **/
    function __construct($level, $settings = array()) {

        if(!$level){

            $level = "error";

        }

        $this->settings = $settings;
        $this->minLevel = array_search(strtolower($level), $this->levels);

    }

    /**
     * Log a message
     * @param string message level
     * @param string message
    */
    public function log($level, $message) {

        $levelIndex = array_search(strtolower($level), $this->levels);

        if($levelIndex >= $this->minLevel) {

            $log = array(
    
                "time" => date('Y-m-d H:i:s'),
                "level" => $level,
                "message" => $message
    
            );

            $this->logInternal($log);

        }

    }

    /**
     * Internal logging function overriden by specific loggers
     * @param array log 
    */
    public function logInternal($log){

        return true;

        // file_put_contents($this->settings["location"], json_encode($log).PHP_EOL , FILE_APPEND | LOCK_EX);

    }

}

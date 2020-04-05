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
  * Evidence class for storing evidence inside flowdata
*/
class evidence {

    protected $flowData;
    protected $evidence = array();

    /**
        * evidence container constructor
        * @param flowData parent flowData
    */
    public function __construct ($flowData) {

        $this->flowData = $flowData;

    }

    /**
        * Set a single piece of evidence by its element and value
        * @param string key
        * @param mixed value
    */
    public function set($key, $value) {

        $keep = false;

        foreach($this->flowData->pipeline->flowElements as $flowElement){

            if($flowElement->filterEvidenceKey($key)){

                $keep = true;

            }

        }

        if($keep){

            $this->evidence[$key] = $value;
        
        }

    }

    /**
        * Helper function to set multiple pieces of evidence from an array
        * @param mixed[]
    */
    public function setArray($array) {

        if(!is_array($array)){

            $this->flowData->setError("core", "Must pass key and value");

        }

        foreach($array as $key => $value){

            $this->set($key, $value);

        }

    }

    /**
        * Extract evidence from a web request
        * No argument version automatically reads from current request
        * @param $_SERVER
        * @param $_COOKIE
    */
    public function setFromWebRequest($server = null, $cookies = null, $query = null) {

        if(!$server){

            $server = $_SERVER;

        }

        if(!$cookies){

            $cookies = $_COOKIE;

        }

        if(!$query){

            $query = $_GET;

        }

        $evidence = array (); 
  
        foreach($server as $name => $value) { 
        
          if (substr($name, 0, 5) == 'HTTP_') {
  
              $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
  
              $key = strtolower($key);
  
              $evidence["header." . $key] = $value; 
          }

        }
  
        foreach($cookies as $key => $value){
  
          $evidence["cookie." . $key] = $value;
  
        }

        foreach($query as $key => $value){
  
            $evidence["query." . $key] = $value;
    
          }
  
        if (isset($server["SERVER_ADDR"])){
            $evidence["server.host-ip"] = $server["SERVER_ADDR"];
        }

        if (isset($server["REMOTE_ADDR"])){
            $evidence["server.client-ip"] = $server["REMOTE_ADDR"];
        }

        // Protocol

        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        // Override protocol with referer header if set

        if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"]){

            $protocol = parse_url($_SERVER["HTTP_REFERER"], PHP_URL_SCHEME);

        }

        $evidence["header.protocol"] = $protocol;
          
        $this->setArray($evidence);
  
    }


    /**
        * Get a piece of evidence by key
        * @param string key
    */
    public function get($key) {

        if(isset($this->evidence[$key])){

            return $this->evidence[$key];

        } else {

            return null;

        };

        
    }

    /**
        * Get all evidence
        * @return mixed[]
    */
    public function getAll(){

        return $this->evidence;

    }

}

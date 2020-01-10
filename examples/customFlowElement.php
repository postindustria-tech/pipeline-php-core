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

/**
@example customFlowElement.php

This example demonstrates the creation of a custom flow element. In this case 
the flowElement takes the results of a client side form collecting 
date of birth, setting this as evidence on a flowData object to calculate 
a person's starsign. The flowElement also serves additional JavaScript 
which gets a user's geolocation and saves the latitude as a cookie. 
This latitude is also then passed in to the flowData to calculate if 
a person is in the northern or southern hemispheres.

*/

include(__DIR__ . "/../vendor/autoload.php");

use fiftyone\pipeline\core\pipelineBuilder;
use fiftyone\pipeline\core\basicListEvidenceKeyFilter;
use fiftyone\pipeline\core\flowElement;
use fiftyone\pipeline\core\elementDataDictionary;

// Function to get star sign from month and day
function getStarSign($month, $day) {

    if (($month == 1 && $day <= 20) || ($month == 12 && $day >= 22)) {
        return "capricorn";
    } else if (($month == 1 && $day >= 21) || ($month == 2 && $day <= 18)) {
        return "aquarius";
    } else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
        return "pisces";
    } else if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 20)) {
        return "aries";
    } else if (($month == 4 && $day >= 21) || ($month == 5 && $day <= 20)) {
        return "taurus";
    } else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) {
        return "gemini";
    } else if (($month == 6 && $day >= 22) || ($month == 7 && $day <= 22)) {
        return "cancer";
    } else if (($month == 7 && $day >= 23) || ($month == 8 && $day <= 23)) {
        return "leo";
    } else if (($month == 8 && $day >= 24) || ($month == 9 && $day <= 23)) {
        return "virgo";
    } else if (($month == 9 && $day >= 24) || ($month == 10 && $day <= 23)) {
        return "libra";
    } else if (($month == 10 && $day >= 24) || ($month == 11 && $day <= 22)) {
        return "scorpio";
    } else if (($month == 11 && $day >= 23) || ($month == 12 && $day <= 21)) {
        return "sagittarius";
    }

};

//! [class]
//! [declaration]
class astrologyFlowElement extends flowElement {
//! [declaration]

    // datakey used to categorise data coming back from this 
    // flowElement in a pipeline
    public $dataKey = "astrology"; 

    // The processInternal function is the core working of a flowElement. 
    // It takes flowData, reads evidence and returns data.
    public function processInternal($flowData){

        $result = [];

        
        // Get the date of birth from the query string (submitted through 
        // a form on the client side)
        $dateOfBirth = $flowData->evidence->get("query.dateOfBirth");
        
        if ($dateOfBirth) {

            $dateOfBirth = explode("-", $dateOfBirth);

            $month = $dateOfBirth[1];
            $day = $dateOfBirth[2];


            $result["starSign"] = getStarSign($month, $day);

        }

        // Serve some JavaScript to the user that will be used to save 
        // a cookie with the user's latitude in it
        $result["getLatitude"] = "navigator.geolocation.getCurrentPosition(function(position) {
            document.cookie = \"latitude=\" + position.coords.latitude;
        });";

        // Get the latitude from the above cookie
        $latitude = $flowData->evidence->get("cookie.latitude");

        // Calculate the hemisphere
        if ($latitude) {

            $result["hemisphere"] = $latitude > 0 ? "Northern" : "Southern";

        }


        $data = new elementDataDictionary($this, $result);

        $flowData->setElementData($data);

    }

    public $properties = array(
        "starSign" => array(
            "type" => "string",
            "description" => "the user's starsign"
        ),
        "getLatitude" => array(
            "type" => "javascript",
            "description" => "JavaScript used to get a user's latitude"
        )
    );

    public function getEvidenceKeyFilter(){

        // A filter (in this case a basic list) stating which evidence 
        // the flowElement is interested in
        return new basicListEvidenceKeyFilter(["cookie.latitude", "query.dateOfBirth"]); 

    }
    
}

//! [class]
//! [usage]

// Make the pipeline and add the element we want to it
$pipeline = (new pipelineBuilder())->add(new astrologyFlowElement())->build();

$flowData = $pipeline->createFlowData();

// Add any information from the request (headers, cookies and additional 
// client side provided information)

$flowData->evidence->setFromWebRequest();

// Process the flowData

$flowData->process();

// Generate the HTML for the form that gets a user's starsign 

$output = "";

$output .= "<h1>Starsign</h1>";

$output .= "<form><label for='dateOfBirth'>Date of birth</label><input type='date' name='dateOfBirth' id='dateOfBirth'><input type='submit'></form>";

// Add the results if they're available


if($flowData->astrology->starSign){
    
    $output .= "<p>Your starsign is " . $flowData->astrology->starSign . "</p>";

}

if($flowData->astrology->hemisphere){

    $output .= "<p>Look at the " . $flowData->astrology->hemisphere . " hemisphere stars tonight!</p>";

}

// Get the JavaScript needed for the geolocation lookup and output it

$javaScript = $flowData->getWhere("type", "javascript");

$output .= "<script>";

foreach($javaScript as $script){

    $output .= $script;
    $output .= " ";

}

$output .= "</script>";

// Return the full output to the page

echo $output;
//! [usage]

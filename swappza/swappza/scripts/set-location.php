<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

//Default response values
$message = '';

$cityID = filter_input(INPUT_POST, 'cityID');
$provinceID = filter_input(INPUT_POST, 'provinceID');
$nationID = filter_input(INPUT_POST, 'nationID');

//Validate
if ( !(isset($cityID) && isset($provinceID) && isset($nationID)) ) {
    $message = 'cityID, provinceID, nationID were not all provided in request';
} elseif ( !(is_numeric($cityID) && is_numeric($provinceID) && is_numeric($nationID)) ) {
    $message = 'cityID, provinceID, nationID must all be numeric';
            
//Is valid
} else {

    $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
    
    if ($mysqli) {

        if (oab_setLocation($mysqli, $cityID, $provinceID, $nationID)) {
            
        } else {
            $message = 'Failed to set location error (1C)';
        }
        
        oab_endDatabaseConnection($mysqli);
    } else {
        $message = 'Failed to set location error (1B)';
    }
}

header('Content-Type: application/json');
$json = json_encode( array('message' => $message) );

echo $json;
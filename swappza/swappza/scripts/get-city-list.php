<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$cityFilter = empty($_POST['cityFilter']) ? '' : filter_input(INPUT_POST, 'cityFilter');

//Default values for returned values
$message = '';
$locations = array();

//Only fetch list if field is not empty
if (!empty($cityFilter)) {
    
    $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

    if ($mysqli) {
        $locations = oab_getLocations($mysqli, $cityFilter);
        oab_endDatabaseConnection($mysqli);
    } else {
        error_log('Failed to establish database connection in get-locations.php');
        $message = 'Failed to establish database connection';
    }
}

$json = json_encode(
    array( 'message' => $message, 'locations' => $locations)
);

header('Content-Type: application/json');
echo $json;

return false;
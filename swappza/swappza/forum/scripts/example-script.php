<?php
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Initialize values
$message = '';
$success = false;

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        //Now do other work
    } else {
        $message = 'Invalid token. Please refresh the page and try again';
    }
    
    oab_endDatabaseConnection($mysqli);
} else {
    $message = 'Failed to establish database connection';
}

header('Content-Type: application/json');
$json = json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce,
    'successful' => $successful
));

echo $json;
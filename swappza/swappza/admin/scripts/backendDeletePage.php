<?php
session_start();
require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$deletePageID = filter_input(INPUT_POST, 'deletePageID');

//Setup return values
$message = '';
$successful = false;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);


if ($mysqli) {
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        if (oabDeletePage($mysqli, $deletePageID)) {
            $successful = true;
            $message = 'Successfully deleted page';
        } else {
            $message = 'Failed to delete page';
        }
    } else {
        $message = 'Invalid or expired token, please refresh the page';
    }
} else {
    $message = 'Failed to communicate with database';
}

oab_endDatabaseConnection($mysqli);

echo json_encode(array(
    'message' => $message,
    'successful' => $successful,
    'newNonce' => $newNonce
));
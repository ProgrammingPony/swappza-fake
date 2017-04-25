<?php
session_start();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        //Get the page content
        if ($page = oabGetPage($mysqli, $pageID)) {
            $successful = true;
        } else {
            $message = 'Failed to retrieve page information or page does not exist';
        }
    } else {
            $message = 'Invalid token. Please refresh the page and try again';
    }
    
    oab_endDatabaseConnection($mysqli);
}

header('Content-Type: application/json');
$json = json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce,
    'successful' => $successful,
    'page' => $page
));

echo $json;
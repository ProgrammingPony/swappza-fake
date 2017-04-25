<?php
session_start();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$visibleTitle = filter_input(INPUT_POST, 'visibleTitle');
$linkTitle = filter_input(INPUT_POST, 'linkTitle');
$content = filter_input(INPUT_POST, 'content');
$styles = filter_input(INPUT_POST, 'styles');
$editPageID = filter_input(INPUT_POST, 'editPageID');

//Default values
$message = '';
$successful = false;

//Validate fields
if (empty($visibleTitle)) {
    $message = 'The visible title field cannot be empty';
} else if (empty($linkTitle)) {
    $message = 'Link title cannot be empty';
} else if (empty($content)) {
    $message = 'Content cannot be empty';
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        
        //Unnecessary fields left null
        $page = new oabPage($editPageID, $content, $visibleTitle, $linkTitle, null, null, null, null, $styles);

        if (oabUpdatePage($mysqli, $page)) {
            $message = 'Successfully updated page';
            $successful = true;
        } else {
            $message = 'Failed to update page';
        }
        
    } else {
        $message = 'Invalid token. Please refresh the page and try again';
    }
    
    oab_endDatabaseConnection($mysqli);
}

header('Content-Type: application/json');

echo json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce,
    'successful' => $successful
));
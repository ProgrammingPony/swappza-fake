<?php
//Not tested or used
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$uploadFieldName1 = filter_input(INPUT_POST, 'uploadFieldName1');
$uploadFieldName2 = filter_input(INPUT_POST, 'uploadFieldName2');
$uploadFieldName3 = filter_input(INPUT_POST, 'uploadFieldName3');
$commentID = filter_input(INPUT_POST, 'commentID');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Initialize values
$message = '';
$successfulUploads = 0;

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        //Ensure user is logged in
        if (is_numeric($_SESSION['u_ID'])) {
            //Setup attachments
            $uploadFieldNames = array(
                $uploadFieldName1,
                $uploadFieldName2,
                $uploadFieldName3
            );     

            //Adds all attachment information for valid attachments (size < max, size > 0, type accepted)
            $attachments = array();

            foreach ($uploadFieldNames as $fieldName) {
                $attachment = oabEFCreateAttachment($mysqli, $fieldName);

                if ($attachment) {
                    if (oabEFAssignAttachmentToComment($mysqli, $attachment->getID(), $commentID)) {
                        $successfulUploads++;
                        array_push($attachments, $attachment);
                    }
                }
            }
        } else {
            $message = 'This request was submit whilst user is not logged in.';
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
    'successfulUploads' => $successfulUploads
));

echo $json;
<?php
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$uploadFieldName1 = filter_input(INPUT_POST, 'attachmentFieldName1');
$uploadFieldName2 = filter_input(INPUT_POST, 'attachmentFieldName2');
$uploadFieldName3 = filter_input(INPUT_POST, 'attachmentFieldName3');
$postID = filter_input(INPUT_POST, 'postID');


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Initialize values
$message = '';
$successfulUploads = 0;
$successful = true;

error_log(print_r($_FILES,TRUE));

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

            $attachments = array();

            //Adds all attachment information for valid attachments (size < max, size > 0, type accepted)
            foreach ($uploadFieldNames as $fieldName) {
                //Ensure that there was no error, and the field was not empty with each file upload
                if ($_FILES[$fieldName]['error'] == 0) {
                    $attachment = oabEFCreateAttachment($mysqli, $fieldName);
                    //We are assuming new attachment will always have an unused ID here
                    if ($attachment) {
                        if (oabEFAssignAttachmentToPost($mysqli, $attachment->getID(), $postID)) {
                            $successfulUploads++;
                            array_push($attachments, $attachment);
                        }
                    }
                //Means no file was uploaded
                } elseif ($_FILES[$fieldName]['error'] == 4) {
                    //do nothing
                //Error in the upload
                } else {
                    $successful = false;
                }                
            }
            
            $message = "{$successfulUploads} files successfully uploaded";
        } else {
            $message = 'This request was submit whilst user is not logged in.';
            $successful = false;
        }
    } else {
        $message = 'Invalid token. Please refresh the page and try again';
        $successful = false;
    }
    
    oab_endDatabaseConnection($mysqli);
}

header('Content-Type: application/json');
$json = json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce,
    'successfulUploads' => $successfulUploads,
    'successful' => $successful
));

echo $json;
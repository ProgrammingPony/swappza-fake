<?php
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$title = filter_input(INPUT_POST, 'title');
$text = filter_input(INPUT_POST, 'text');
$subCategoryID = filter_input(INPUT_POST,'subCategoryID');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Initialize values
$message = '';
$successful = false;

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        //Ensure user is logged in
        if (is_numeric($_SESSION['u_ID'])) {
            //Setup new posts without attachments, that will be done in another step. Unnecessary fields are left as null
            $subCategory = new oabEFSubCategory($subCategoryID, null, null);        
            $post = new oabEFPost(null, $title, $_SESSION['u_ID'], null, null, $subCategory, $text, null);

            if ($postID = oabEFCreatePost($mysqli, $post)) {
                $successful = true;
                $message = 'Successfully created new post';
            } else {
                $message = 'Failed to create new post';
            }
        } else {
            $message = 'This request was submit whilst user is not logged in.';
        }
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
    'successful' => $successful,
    'postID' => $postID
));

echo $json;
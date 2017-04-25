<?php
//Currently only support editting title and text
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

$postID = filter_input(INPUT_POST, 'postID');
$newTitle = filter_input(INPUT_POST, 'title');
$newText = filter_input(INPUT_POST, 'text');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Initialize values
$message = '';
$success = false;

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    
    if ($newNonce) {
        //Ensure post exists
        $post = oabEFGetPost($mysqli, $postID);
        
        if ($post) {
            //Ensure request is from the person who wrote the post
            $authorID = $post->getAuthor()->getUserID();
            
            if ($_SESSION['u_ID'] == $authorID) {
                //Edit post
                $newPost = new oabEFPost($postID, $newTitle, $post->getAuthor(), $post->getDateOfPosting(), null, $post->getSubCategory(), $newText, null); //Values set to null don't matter
                if (oabEFEditPost($mysqli, $newPost, $files)) {
                    $successful = true;
                    $message = 'Post successfully editted';
                } else {
                    $message = 'Failed to edit post';
                }
                
            } else {
                $message = 'The post you are attempting to edit was not written by you';
            }
            
        } else {
            $message = 'No post exists with the provided postID';
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
    'successful' => $successful
));

echo $json;
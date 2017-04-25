<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

//Input values
$userORemail = filter_input(INPUT_POST, 'userORemail');
$password = filter_input(INPUT_POST, 'password');
$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

//Prepare response
$message = '';
$successful = false;

//Validate
if (empty($userORemail)) {
    $message = 'Email or username is required';
} else if (empty($password)) {
    $message = 'Password is required';
//is valid
} else {
    //Establish database connection
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
    $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
    
    if ($mysqli) {
        //Check for valid nonce token
        $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
        if ($newNonce) {
            //Check Valid Credentials
            if (oab_isValidCredentials($mysqli, $userORemail, $password)) {
                //Fetch basic user Data
                $userID = oab_getUserID($mysqli, $userORemail);
                
                if (is_numeric($userID)) {
                    
                    $userProfile = oab_getUserProfile($mysqli, $userID, oabUserIdType::_DEFAULT);
                    if ($userProfile) {
                        $successful = true;
                        
                        //Prepare and set session values
                        oab_setUserSession($mysqli, $userProfile);

                        //Update user's last login
                        if (!oab_updateLastLogin($mysqli, $userID)) {
                            error_log('Failed to update last time user logged in from login.php in scripts folder');
                        }                        
                    } else {
                        $message = 'Failed to retrieve user profile information';
                    }
                                        
                } else {
                    $message = 'Failed to update last login';
                }
                
            } else {
                $message = 'The provided credentials was invalid';
            }
        } else {
            $message = 'Invalid token. Please refresh the page and try again';
        }
        
        oab_endDatabaseConnection($mysqli);
    } else {
        $message = 'failed to communicate with database';
    }
}

//Return message
header('Content-Type: application/json');

if ($newNonce === false) {
    $newNonce = '';
}

$json = json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce,
    'successful' => $successful
));

echo $json;
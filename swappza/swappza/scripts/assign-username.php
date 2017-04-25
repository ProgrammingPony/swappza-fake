<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

$USERNAME_MAX_LENGTH = 20;

require "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$email = filter_input(INPUT_POST, 'email');
$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$password = filter_input(INPUT_POST, 'password');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');
$username = filter_input(INPUT_POST, 'username');

//Prepare response
$message = '';
$newToken = '';
$successful = false;

//Validate fields
if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
    $message = 'Invalid email format';
}
elseif (strlen($username) > $USERNAME_MAX_LENGTH) {
    $message = 'New username too long';
}
//Validation successful
else {
    //Start Db connection
    $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
    
    if ($mysqli) {        
        //Check if nonceToken is valid
        $newToken = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
        
        if ($newToken) {
            //Check if password email combo is valid
            if (oab_isValidCredentials($mysqli, $email, $password)) {
                //Check if new username is already taken
                if (oab_isExistingUsername($mysqli, $username)) {
                    $message = 'Username already exists';
                } else {
                    //Assign username
                    if (oab_assignUsername($mysqli, $_SESSION['u_ID'], $username)) {
                        $message = 'Username successfully assigned to your account';
                        $_SESSION['u_username'] = $username;
                        $successful = true;
                    } else {
                        $message = 'Username was not successfully assigned to your account';
                    }
                    
                }
            } else {
                $message = 'Invalid credentials were provided';
            }
        } else {
            $message = 'Invalid token, please refresh the page and try again';
        }
        
        oab_endDatabaseConnection($mysqli);
    } else {
        $message = 'Failed to communicate with database';
    }
}

echo json_encode(array('nonceToken' => $newToken, 'message' => $message, 'successful' => $successful));
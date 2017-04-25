<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

//Field names that should be used
$name = filter_input(INPUT_POST, 'name');
$username = filter_input(INPUT_POST, 'username');
$email = filter_input(INPUT_POST, 'email');
$city = filter_input(INPUT_POST, 'city');
$password = filter_input(INPUT_POST, 'password');
$dateOfBirth = filter_input(INPUT_POST, 'birthday');
$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$phone = filter_input(INPUT_POST, 'phone');
$address = filter_input(INPUT_POST, 'address');
$fileName = filter_input(INPUT_POST, 'fileName');



//Prepare Response
$message = '';
$newNonce = '';
$successful = false;

//Establish database connection
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($mysqli) {
    //FIELD VALIDATION
    if (empty($name)) {
        $message = 'The "name" field must not be left empty';
    } else if (!preg_match("/^[a-zA-Z -]*$/",$name)) {
        $message = 'Only a-z, A-Z, spaces, hyphens are accepted characters for the "name" field';
    } else if (empty($email)) {
        $message = 'Email is required';
    } else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
        $message = 'Invalid format for email';			
    } else if (empty($password)) {
        $message = 'Password is required';
    } else if (!preg_match("/^[a-zA-Z0-9!@#$%]*$/",$password)) {
        $message = 'Only letters, numbers and special characters !@#$% are permitted for the password';
    } else if (empty($dateOfBirth)) {
        $message = 'Date of Birth is required';
    } else {        
        //Ensure that email is not already in database
        if (oab_isExistingEmail($mysqli, $email)) {
            $message = 'This email is already in use'; 
        } else {
            //Ensure username not already in use
            if (oab_isExistingUsername($mysqli, $username)) {
                $message = 'This username is already in use';
            } else {
                //Check nonceToken and provide new one if valid
                $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);

                if ($newNonce) {
                    //Enter user information into users table
                    $oCity = new oabCity(null, $city);
                    $oLocation = new oabLocation($oCity, null, null, $address, null);
                    
                    $userProfile = new oabUser($username, $name, null, $email, null, $oLocation, $dateOfBirth, null, $phone, oabRole::_DEFAULT);
                    
                    $mysqli->autocommit(false);
                    
                    if (oab_createNewUser($mysqli, $userProfile)) {
                        //Enter password hash into password table
                        $userID = oab_getUserID($mysqli, $email, $username);
                        
                        if (oab_setFirstPassword($mysqli, $userID, $password)) {
                            //TODO Send verification email with code
                            $message = 'New user successfully created';
                            $successful = true;
                        } else {
                            $message = 'Failed to enter new user data into database (Error 1A)';
                        }
                        
                    } else {
                        $message = 'Failed to enter new user data into database (Error 1B)';
                    }
                    
                    $mysqli->autocommit(true);
                    
                } else {
                    $message = 'Invalid token. Please refresh the page and try again';
                }
            }
        }
    }
    
    oab_endDatabaseConnection($mysqli);
}

if ($newNonce === false) {
    $newNonce = '';
}

header('Content-Type: application/json');

$json = json_encode( array(
    'message' => $message,
    'newNonce' => $newNonce
));

echo $json;
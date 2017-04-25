<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$username = filter_input(INPUT_POST, 'username');
$address = filter_input(INPUT_POST, 'address');
$phone = preg_replace("/[^0-9]/",'', filter_input(INPUT_POST, 'phone') );
$oldPassword = filter_input(INPUT_POST, 'oldPassword');
$newPassword = filter_input(INPUT_POST, 'newPassword');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$email = filter_input(INPUT_POST, 'email');
$fileName = filter_input(INPUT_POST, 'fileName');

//Setup Response
$message = '';
$newToken = '';
$successful = false;
//Validate fields
$isValid = false;

if (strlen($email) > $OAB_MAXIMUM_EMAIL_LENGTH) {
    $message = "Email should be at most {$OAB_MAXIMUM_EMAIL_LENGTH} characters";
}
elseif (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
    $message = 'Invalid email format';
}
elseif (strlen($username) > $OAB_MAXIMUM_USERNAME_LENGTH) {
    $message = "Username should be at most {$OAB_MAXIMUM_USERNAME_LENGTH} characters long";
}
elseif (!preg_match("/^[a-zA-Z -~]*$/",$username)) {
    $message = 'Username can only contain latin alphabetical characters, spaces, tildes ~`, dashes -';
}
elseif (strlen($address) > $OAB_MAXIMUM_ADDRESS_LENGTH) {
    $message = "Address should be at most {$OAB_MAXIMUM_ADDRESS_LENGTH} characters long";
}
elseif (strlen($phone) > $OAB_MAXIMUM_PHONE_LENGTH) {
    $message = "Phone should be at mose {$OAB_MAXIMUM_PHONE_LENGTH} characters long";
}
else {
    $isValid = true;
}

//Validation successful
if ($isValid) {
    //Setup Database connection
    $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
    
    if ($mysqli) {
        //Check if token is valid
        $newToken = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
        
        if (empty($newToken)) {
            $message = 'Invalid token provided, please refresh the page';
        } else {
            //Check if old user credentials are valid
            if (oab_isValidCredentials($mysqli, $username, $oldPassword)) {
                if (!empty($username) && !oab_isUsersUsername($mysqli, $_SESSION['u_ID'], $username)) {
                    if (oab_isExistingUsername($mysqli, $username)) {
                        $message = 'Username already in use, please try another username';
                    } else {
                        if (oab_updateUsername($mysqli, $_SESSION['u_ID'], $username)) {
                            $_SESSION['u_username'] = $username;
                        } else {
                            $message = 'Failed to update username';
                        }
                    }
                }

                if (!empty($email) && !oab_isUsersEmail($mysqli, $_SESSION['u_ID'], $email)) {
                    if (oab_isExistingEmail($mysqli, $email)) {
                        $message = 'Email already in use, please try another email';
                    } else {
                        if (oab_updateEmail($mysqli, $_SESSION['u_ID'], $email)) {
                            $_SESSION['u_email'] = $email;
                        } else {
                            $message = 'Failed to update email';
                        }
                    }
                }

                if (!empty($newPassword)) {
                    error_log('here3');
                    if (!oab_updatePassword($mysqli, $_SESSION['u_ID'], $newPassword)) {
                        $message = 'Failed to update password';
                    }
                }

                if (!empty($address)) {
                    error_log('here2');
                    if (oab_updateAddress($mysqli, $_SESSION['u_ID'], $address)) {
                        $_SESSION['u_address'] = $address;
                    } else {
                        $message = 'Failed to update address';
                    }                        
                }

                if (!empty($phone)) {
                    error_log('here1');
                    if (oab_updatePhone($mysqli, $_SESSION['u_ID'], $phone)) {
                        $_SESSION['u_phone'] = $phone;
                    } else {
                        $message = 'Failed to update phone number';
                    }
                }

                $successful = true;
            } else {
                $message = 'Invalid username, password combination provided';
            }
        }
        
        oab_endDatabaseConnection($mysqli);
    } else {
        error_log('Failed to establish database connection from update-profile.php');
        $message = 'Failed to establish database connection';
    }
    
}

echo json_encode( array ('nonceToken' => $newToken, 'message' => $message, 'successful' => $successful) );
?>
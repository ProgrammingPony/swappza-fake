<?php
//Reference for recaptcha: https://codeforgeek.com/2014/12/google-recaptcha-tutorial/
session_start();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

//Input values
$email = filter_input(INPUT_POST, 'email');
$userMessage = filter_input(INPUT_POST, 'comment');
$captcha = filter_input(INPUT_POST, 'gRecaptchaResponse');

$nonceToken = filter_input(INPUT_POST, 'nonceToken');
$pageID = isset($_POST['pageID']) && is_numeric($_POST['pageID']) ? filter_input(INPUT_POST, 'pageID') : null;
$fileName = filter_input(INPUT_POST, 'fileName');

//Prepare response
$returnMessage = '';
$successful = false;

//Check if captcha valid
$server_address = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$RECAPTCHA_PRIVATE_KEY}&response={$captcha}&remoteip={$server_address}");

//Establish database connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($mysqli) {
    //Check for valid nonce token
    $newNonce = oab_verify_nonce($mysqli, $pageID, $nonceToken, $fileName);
    if ($newNonce) {
        //Validate form
        if (filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email)) {
            //Check for valid recaptcha            
            if ($response.success) {
                //TODO: Do something with email and userMessage
                //You could perhaps send it to some email, or have a database table to store the info
                error_log("Message from contact-us.php: email={$email}\nuserMessage={$userMessage}\n");
                
                $returnMessage = 'Successfully delivered comment';                
                $successful = true;
            } else {
                $returnMessage = 'Invalid recaptcha content';
            }
        } else {
            $returnMessage = 'Invalid Email Format Provided';
        }
    } else {
        $returnMessage = 'Invalid token. Please refresh the page and try again';
    }

    oab_endDatabaseConnection($mysqli);
} else {
    $returnMessage = 'Failed to communicate with database';
}

//Return message
header('Content-Type: application/json');

if ($newNonce === false) {
    $newNonce = '';
}

$json = json_encode( array(
    'message' => $returnMessage,
    'newNonce' => $newNonce,
    'successful' => $successful
));

echo $json;
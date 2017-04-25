<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$HTTPS_ACTIVE = true;  //Force users to use HTTPS, should be active when deployed
$BASE_DOMAIN_ADDRESS = "diplomatic-war.com/omarabdelbari.com/swappza"; //Domain root of the pages
$LOCAL_BASE_DOMAIN_ADDRESS = '/home/dolphinlover1/public_html/omarabdelbari.com/swappza';

$DATABASE_HOST = 'localhost';
$DATABASE_USERNAME = 'swappza_test';
$DATABASE_PASSWORD = 'apple1234';
$DATABASE_NAME = 'swappza';

$BASE_STYLESHEETS = 'base.css:drupal-project-layout.css'; //Starting from template folder
$BACKEND_BASE_STYLESHEETS = 'base.css';

$RECAPTCHA_PUBLIC_KEY = '6Lde-fUSAAAAAJ1Eoe1y7D65eqrwXcg0KNsaiOyT';
$RECAPTCHA_PRIVATE_KEY = '6Lde-fUSAAAAAAfvCM3Zqjqfy-K-_oc-87YFjHHZ';

$PAGE_NOT_FOUND_LOCATION = '404.php';

$RESPONSE_DELIMITER = '_-_';

//##########################
//Derived Variables
//##########################

//USE $PRODUCTION_DOMAIN_BASE for any absolute links from domain base
if ($HTTPS_ACTIVE) {
    //Force use of HTTPS if requested in properties
    $current_https_access = filter_input(INPUT_SERVER, "HTTPS");

    if($current_https_access !== "on") {
        header("Location: https://" . filter_input(INPUT_SERVER, "HTTP_HOST") . filter_input(INPUT_SERVER, "REQUEST_URI"));
        exit();
    }
    
    $PRODUCTION_DOMAIN_BASE = "s";
} else {
    $PRODUCTION_DOMAIN_BASE = "";
}

$PRODUCTION_DOMAIN_BASE = "http" . $PRODUCTION_DOMAIN_BASE . "://" . $BASE_DOMAIN_ADDRESS;
$ABSOLUTE_FRONTEND_SCRIPTS_LOCATION = "{$PRODUCTION_DOMAIN_BASE}/scripts";
$ABSOLUTE_LIBRARY_LOCATION = $PRODUCTION_DOMAIN_BASE . '/libraries';
$ABSOLUTE_TEMPLATE_BASE_DIR = "{$PRODUCTION_DOMAIN_BASE}/template";
$BACKEND_DOMAIN_BASE = $PRODUCTION_DOMAIN_BASE . '/admin';

$LOCAL_BACKEND_BASE_DOMAIN_ADDRESS = "{$LOCAL_BASE_DOMAIN_ADDRESS}/admin";
$ABSOLUTE_BACKEND_SCRIPTS_LOCATION = "{$LOCAL_BACKEND_BASE_DOMAIN_ADDRESS}/scripts";
$ABSOLUTE_BACKEND_TEMPLATE_DIR = "{$LOCAL_BACKEND_BASE_DOMAIN_ADDRESS}/template";

$LIBRARY_LOCATION = "{$LOCAL_BASE_DOMAIN_ADDRESS}/libraries";
$FRONTEND_SCRIPTS_LOCATION = "{$LOCAL_BASE_DOMAIN_ADDRESS}/scripts";
$TEMPLATE_BASE_DIR = "{$LOCAL_BASE_DOMAIN_ADDRESS}/template";

$FAVICON_LOCATION = "{$PRODUCTION_DOMAIN_BASE}/favicon.ico"; //relative to domain base

//###########
//SETTINGS
//###########

//Embedded forum page properties
$EMBEDDED_FORUM_IS_ACTIVE = true;
$EMBEDDED_FORUM_PAGE_NAME = 'forum'; //visible title of page
$EMBEDDED_FORUM_LOCATION = "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum"; //Using local file path
$EMBEDDED_FORUM_ATTACHMENT_LOCATION = "{$EMBEDDED_FORUM_LOCATION}/attachments";
$EMBEDDED_FORUM_VALID_ATTACHMENT_TYPES = array('gif', 'png', 'jpg', 'jpeg', 'txt', 'doc', 'pdf');

//$EMBEDDED_FORUM_ABSOLUTE_LOCATION = "{$PRODUCTION_DOMAIN_BASE}/forum"; //Using http or https 


$GOOGLE_OAUTH_ACTIVE = true;
$GOOGLE_OAUTH_LOCATION = "{$LIBRARY_LOCATION}/google-api-php-client-master";
$GOOGLE_OAUTH_CLIENT_ID = '510609424662-avukm63egr0acrh1g566bcd6408gdkoh.apps.googleusercontent.com';
$GOOGLE_OAUTH_CLIENT_SECRET = 'hE5OL80M50y1YakaV26DjOuv';
$GOOGLE_OAUTH_PUBLIC_KEY = 'AIzaSyCRVeD-SXMWSPeUZvr9Fi_w_qq0Atubs10';
$GOOGLE_OAUTH_REDIRECT_URI = "{$PRODUCTION_DOMAIN_BASE}/scripts/google-login.php";
//$GOOGLE_OAUTH_CLIENT_ID = '1090228160519-s5o8g9p4onp3r3303aolod6smtsfkc9c';

$FACEBOOK_LOGIN_ACTIVE = true;
$FACEBOOK_APP_ID = '304640829659593';
$FACEBOOK_SECRET_KEY = '93fa97ba7bb017f79ca21436e5347ee9';
$FACEBOOK_REDIRECT_URI = "{$PRODUCTION_DOMAIN_BASE}/scripts/fb-login.php";
?>
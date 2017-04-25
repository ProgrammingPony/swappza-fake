<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



session_start();
require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";

if (isset($_SESSION['u_ID'])) {
    header("Location: {$PRODUCTION_DOMAIN_BASE}");
}

//Tutorials: http://enarion.net/programming/php/google-client-api/google-client-api-php/
//http://stackoverflow.com/questions/7130648/get-user-info-via-google-api
if ($GOOGLE_OAUTH_ACTIVE) {
    require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
    require_once ("{$GOOGLE_OAUTH_LOCATION}/src/Google/autoload.php");
    
    session_start();

    $client = new Google_Client();
    $client->setClientId($GOOGLE_OAUTH_CLIENT_ID);
    $client->setClientSecret($GOOGLE_OAUTH_CLIENT_SECRET);
    $client->setRedirectUri($GOOGLE_OAUTH_REDIRECT_URI);
    $client->setDeveloperKey($GOOGLE_OAUTH_PUBLIC_KEY);
    $client->setScopes( array('openid', 'email', 'profile') );
    
   /************************************************
    If we're logging out we just need to clear our
    local access token in this case
    ************************************************/
    if (isset($_REQUEST['logout'])) {
        //unset($_SESSION['access_token']);
        session_unset();
    }
    
    /************************************************
      If we have a code back from the OAuth 2.0 flow,
      we need to exchange that with the authenticate()
      function. We store the resultant access token
      bundle in the session, and redirect to ourself.
     ************************************************/
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
        $redirect = "{$PRODUCTION_DOMAIN_BASE}/scripts/google-login.php";
        header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    /************************************************
      If we have an access token, we can make
      requests, else we generate an authentication URL.
     ************************************************/
    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    } else {
        $authUrl = $client->createAuthUrl();
    }

    /************************************************
      If we're signed in we can go ahead and retrieve
      the ID token, which is part of the bundle of
      data that is exchange in the authenticate step
      - we only need to do a network call if we have
      to retrieve the Google certificate to verify it,
      and that can be cached.
     ************************************************/
    if ($client->getAccessToken()) {
        $_SESSION['access_token'] = $client->getAccessToken();
        $token_data = $client->verifyIdToken()->getAttributes();
        
        $googleOAuth = new Google_Service_Oauth2($client);
        
        $email = $googleOAuth->userinfo->get()->email;
        $name = $googleOAuth->userinfo->get()->name;
        $gid = $googleOAuth->userinfo->get()->id;
        $birthday = $googleOAuth->userinfo->get()->birthday;        
        
        //Establish database connection
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error testing, remove from production
        $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
        
        if ($mysqli) {
            //Check if user with same google id exists
            $userProfile = oab_getUserProfile($mysqli, $gid, oabUserIdType::GOOGLE);
            
            if ($userProfile) {
                $userID = $userProfile->getUserID();
            }
            
            if (isset($userID) && is_numeric($userID)) {
                oab_setUserSession($mysqli, $userProfile);
            } else {
                //Add google id to existing user account with same email if it already exists
                if (oab_isExistingEmail($mysqli, $email)) {
                    $userID = oab_getUserID($mysqli, $email);

                    if (is_numeric($userID)) {;
                        
                        if (oab_addExternalAccount($mysqli, $userID, $gid, oabUserIdType::GOOGLE)) {
                            $message = 'Successfully merged google account with existing account that has registered the same email';
                            
                            $userProfile = oab_getUserProfile($mysqli, $userID, oabUserIdType::_DEFAULT);
                            
                            if ($userProfile) {
                                oab_setUserSession($mysqli, $userProfile);
                                
                                header("Location: {$PRODUCTION_DOMAIN_BASE}");
                            }

                        } else {
                            $message = 'Google login Failed error B';
                        }
                        
                    } else {
                        $message = 'Google login Failed error A';
                    }
                    
                //Make new user then attach google account to it
                } else {
                    $mysqli->autocommit(false);
                    $userProfile = new oabUser(null, $name, null, $email, null, null, $birthday, null, null, oabRole::_DEFAULT);
                    
                    if (oab_createNewUser($mysqli, $userProfile)) {
                        $userID = oab_getUserID($mysqli, $email);
                        if (is_numeric($userID)) {
                            if (oab_addExternalAccount($mysqli, $userID, $gid, oabUserIdType::GOOGLE)) {
                                $message = 'Successfully created new account using your Google ID';
                                
                                oab_setUserSession($mysqli, $userProfile);
                                
                                header("Location: {$PRODUCTION_DOMAIN_BASE}");
                                
                            } else {
                                $mysqli->rollback();
                                $message = 'Failed to attach google account to newly created account error C';
                            }
                        } else {
                            $message = 'Failed to retrieve userID of newly created account error D';
                        }
                    } else {
                        $mysqli->rollback();
                        $message = 'Failed to create new user for this account error E';
                    }
                    $mysqli->autocommit(true);
                }
            }
            
            oab_endDatabaseConnection($mysqli);
            
        } else {
            $message = 'Failed to communicate with database error F';
        }
    }
    
    //If authorization URL given direct user to it
    if (isset($authUrl)) {
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    }
}
?>
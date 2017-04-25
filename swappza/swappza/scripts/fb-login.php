<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";


//Facebook Login PHP API
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

if (isset($_SESSION['u_ID'])) {
    header("Location: {$PRODUCTION_DOMAIN_BASE}");
}

$message = '';

//FB Tutorial: http://www.krizna.com/general/login-with-facebook-using-php/
//GraphObject Reference: https://developers.facebook.com/docs/php/GraphObject/4.0.0
if ($FACEBOOK_LOGIN_ACTIVE) {
    require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
    require_once ("{$LIBRARY_LOCATION}/facebook-php-sdk-v4-4.0-dev/autoload.php");
    
    FacebookSession::setDefaultApplication($FACEBOOK_APP_ID, $FACEBOOK_SECRET_KEY);
    
    $helper = new FacebookRedirectLoginHelper($FACEBOOK_REDIRECT_URI);
    
    try {
        $session = $helper->getSessionFromRedirect();
    } catch (FacebookRequestException $ex) {
        $message = 'Facebook has returned error in fb-login.php';
    } catch (Exception $ex) {
        //Validation fails or other local issues
        $message = 'validation failed or local issue from fb-login.php';
    }

    if ( isset( $session ) ) {
        // Fetch user data
        $request = new FacebookRequest( $session, 'GET', '/me' );
        $response = $request->execute();
        
        $graphObject = $response->getGraphObject();
        $fbid = strval( $graphObject->getProperty('id') );
        $name = $graphObject->getProperty('name');
        $email = $graphObject->getProperty('email'); 
        $birthday = $graphObject->getProperty('birthday');

        //Establish database connection
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
        $mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
        
        if ($mysqli) {
            //Check if user with same facebook id exists
            $userProfile = oab_getUserProfile($mysqli, $fbid, oabUserIdType::FACEBOOK);
            
            if ($userProfile) {
                $userID = $userProfile->getUserID();
            }
            
            if (isset($userID) && is_numeric($userID)) {
                oab_setUserSession($mysqli, $userProfile);
            } else {
                //Add facebook id to existing user account with same email if it already exists
                if (oab_isExistingEmail($mysqli, $email)) {
                    $userID = oab_getUserID($mysqli, $email);

                    if (is_numeric($userID)) {
                        $mysqli->autocommit(false);
                        
                        if (oab_addExternalAccount($mysqli, $userID, $fbid, oabUserIdType::FACEBOOK)) {
                            $message = 'Successfully merged facebook account with existing account that has registered the same email';
                            
                            $userProfile = oab_getUserProfile($mysqli, $userID, oabUserIdType::_DEFAULT);
                            
                            if ($userProfile) {
                                oab_setUserSession($mysqli, $userProfile);
                                
                                header("Location: {$PRODUCTION_DOMAIN_BASE}");
                            }

                        } else {
                            $message = 'Facebook login Failed error B';
                        }
                        
                        $mysqli->autocommit(true);
                    } else {
                        $message = 'Facebook login Failed error A';
                    }
                    
                //Make new user then attach facebook account to it
                } else {
                    $mysqli->autocommit(false);

                    $oLocation = new oabLocation(
                            new oabCity(null, null),
                            null,
                            null,
                            null,
                            null
                    );
                    
                    $userProfile = new oabUser(null, $name, null, $email, null, $oLocation, $birthday, null, null, oabRole::_DEFAULT);

                    if (oab_createNewUser($mysqli, $userProfile)) {
                        $userID = $mysqli->insert_id;
                        
                        if (isset($userID) && is_numeric($userID)) {
                            if (oab_addExternalAccount($mysqli, $userID, $fbid, oabUserIdType::FACEBOOK)) {
                                $message = 'Successfully created new account using your facebook ID';
                                
                                oab_setUserSession($mysqli, $userProfile);
                                
                                header("Location: {$PRODUCTION_DOMAIN_BASE}");
                                
                            } else {
                                $message = 'Failed to attach facebook account to newly created account error C';
                            }
                        } else {
                            $mysqli->rollback();
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
               
    } else {
        $loginUrl = $helper->getLoginUrl();
        header("Location: ".$loginUrl);
    }
        
}

echo $message;

?>
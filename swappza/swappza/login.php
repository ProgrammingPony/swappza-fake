<?php

session_start();

//Link the following link using computer directories to the properties file, which should not be viewable except by admin
require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";

//If user is logged in but their username is not yet set redirect them to profile page
if (isset($_SESSION['u_username']) && empty($_SESSION['u_username'])) {
    header("Location: {$PRODUCTION_DOMAIN_BASE}/profile.php?id={$_SESSION['u_ID']}");
    exit();
}

//Redirect user to home page if they are already logged in
elseif (isset($_SESSION['u_ID']) && !empty($_SESSION['u_ID'])) {
    header("Location: {$PRODUCTION_DOMAIN_BASE}");
    exit();
}

require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$TEMPLATE_BASE_DIR}/template.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo "{$ABSOLUTE_TEMPLATE_BASE_DIR}/login-register-page.css";?>">
        <link rel="stylesheet" type="text/css" href="<?php echo "{$ABSOLUTE_TEMPLATE_BASE_DIR}/datepicker/jquery.datepick.css";?>">
        
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var FILE_NAME = 'login.php';
            var PAGE_ID = <?php echo (isset($_PAGE['ID']) ? $_PAGE['ID'] : 'null'); ?>;
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'login.php'); ?>';
        </script>
        
        <title>Login or Register</title>
    </head>
    <body>        
        <header class="full-width-container centered-container upper-header-background swappza-header">
            <?php oab_printUpperMenu(); ?>
        </header>
        
        <header class="full-width-container centered-container lower-header-background">            
            <?php oab_printLowerMenu(); ?>
        </header>
        
        <?php oab_printMessage(); ?>
        
        <div class="full-width-container centered-container body-background">            
            <?php oab_printContentStart(); ?>
            
            <div class="field field-name-body field-type-text-with-summary field-label-hidden">
                <div class="field-items">
                    <div class="field-item even" property="content:encoded">
                        <div class="swappza-logreg-container">
                            <div class="swappza-logreg-halfcolumn">
                                <span class="swappza-logreg-title">Login</span>
                                <br />
                                <span class="swappza-logreg-halfcolumn-container">
                                    <a href="<?php echo $ABSOLUTE_FRONTEND_SCRIPTS_LOCATION; ?>/fb-login.php">
                                        <img id="facebook-login-icon" alt="facebook-login-icon" class="swappza-logreg-sociallogin-icon" src="<?php echo $ABSOLUTE_TEMPLATE_BASE_DIR; ?>/images/facebook-login-smaller.png" /> 
                                    </a>
                                    <a href="<?php echo $ABSOLUTE_FRONTEND_SCRIPTS_LOCATION?>/google-login.php">
                                        <img alt="google-login-icon" class="swappza-logreg-sociallogin-icon" src="<?php echo $ABSOLUTE_TEMPLATE_BASE_DIR; ?>/images/google-plus-login-smaller.png" />
                                    </a>
                                </span>
                                
                                <div class="swappza-logreg-vertdiv-container">
                                    <hr />
                                    <span class="swappza-logreg-vertdiv-text">Or</span>
                                    <hr />
                                </div>
                                <br />
                                <input id="login-userORemail" placeholder="Username or Email" type="text" /><br />
                                <input id="login-password" placeholder="Password" type="password" /><br />
                                <a class="swappza-logreg-link" href="<?php echo $PRODUCTION_DOMAIN_BASE; ?>/recover.php">forgot password</a>
                            </div>

                            <div class="swappza-logreg-halfcolumn">
                                <span class="swappza-logreg-title">Signup</span>
                                <br />
                                <input id="register-name" placeholder="Name" type="text" maxlength="200"/><br />
                                <input id="register-email" placeholder="Email" type="text" maxlength="255"/><br />
                                <input id="register-username" placeholder="Username" type="text" maxlength="20"/><br />
                                <input id="register-password" placeholder="Password" type="password" maxlength="25"/><br />
                                <input id="register-city" placeholder="City" type="text" />
                                <input id="register-birthday" type="text" name="birthday" placeholder="Date of Birth"/>
                                <br />

                                <span>
                                    By registering you agree to the 
                                    <a  href="https://omarabdelbari.com/swappza/index.php?page=terms_of_use">terms and conditions</a>
                                </span>
                            </div>

                            <br/>

                            <div class="swappza-logreg-halfcolumn"><button id="login-button">Login</button></div>

                            <div class="swappza-logreg-halfcolumn"><button id="register-button">Register</button></div>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript" src="libraries/datepicker/jquery.plugin.js"></script> 
            <script type="text/javascript" src="libraries/datepicker/jquery.datepick.js"></script>
            <script>
                //Login Functionality
                function oab_authenticateUserCallback(response) {
                    oab_updateMessage(response.message);
                    
                    if (response.newNonce) {
                        oabUpdateNonceToken(response.newNonce);
                    }
                    
                    if (response.successful) {
                        window.location.replace(DOMAIN_BASE + '/index.php');
                    }
                }
                
                $('#login-button').click( function() {
                  oab_authenticateUser(
                    nonceToken,
                    $('#login-userORemail').val(),
                    $('#login-password').val(),
                    PAGE_ID,
                    FILE_NAME,
                    oab_authenticateUserCallback    
                  );
                  $('#login-password').val('');
                });

                //Registration Functionality
                function oab_createNewUserCallback(response) {
                    oab_updateMessage(response.message);
                    
                    if (response.newNonce) {
                        oabUpdateNonceToken(response.newNonce);
                    }
                    
                    if (response.successful) {
                        window.location.replace(DOMAIN_BASE + '/index.php');
                    }
                }

                $('#register-button').click( function() {
                  oab_createNewUser (
                    nonceToken, 
                    $('#register-username').val(),
                    $('#register-email').val(),
                    $('#register-password').val(),
                    $('#register-name').val(),
                    $('#register-city').val(),
                    $('#register-birthday').val(),
                    '',
                    '',
                    PAGE_ID,
                    FILE_NAME,
                    oab_createNewUserCallback
                  );
                  $('#register-password').val('');
                });

                //Datepicker
                $('#register-birthday').datepick({dateFormat: 'yyyy-mm-dd', maxDate: -1825, minDate: -36500, yearRange: '1900:+0' });
            </script>
            
            <?php oab_printContentEnd(); ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>


    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?> 
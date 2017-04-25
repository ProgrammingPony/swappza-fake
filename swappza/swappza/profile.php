<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

//Link the following link using computer directories to the properties file, which should not be viewable except by admin
require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$TEMPLATE_BASE_DIR}/template.php";

global $PRODUCTION_DOMAIN_BASE, $mysqli;
?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo "{$ABSOLUTE_TEMPLATE_BASE_DIR}/profile.css";?>">
        <link rel="stylesheet" type="text/css" href="<?php echo "{$ABSOLUTE_TEMPLATE_BASE_DIR}/datepicker/jquery.datepick.css";?>">
                        
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'profile.php'); ?>';
            var FILE_NAME = 'profile.php'; 
            var PAGE_ID = null;
        </script>
        
    </head>
    <body>
        <?php        
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $stmt = $mysqli->prepare("SELECT username,email,name,address,phone FROM Users WHERE userID=?");

            if ($stmt) {
                $stmt->bind_param('d', $_GET['id']);
                $stmt->execute();
                $stmt->bind_result($r_username, $r_email, $r_name, $r_address, $r_phone);

                if ($stmt->fetch()) {
                    $username = $r_username;
                    $email = $r_email;
                    $name = $r_name;
                    $address = $r_address;
                    $phone = $r_phone;
                } else {
                    $message = "No Such User Exists <a href='{$PRODUCTION_DOMAIN_BASE}'>Click Here</a> To Return to Home Page";
                }

                $stmt->close();
            }
        }
        ?>
        <header class="full-width-container centered-container upper-header-background swappza-header">
            <?php oab_printUpperMenu(); ?>
        </header>
        
        <header class="full-width-container centered-container lower-header-background">            
            <?php oab_printLowerMenu(); ?>
        </header>
        
        <?php oab_printMessage(); ?>
        
        <div class="full-width-container centered-container body-background">            
            <?php oab_printContentStart();
            
            if (isset($_GET['id']) && !empty($email)) {
                
                echo "
                <div class=\"swappza-profile-halfcolumn\" style=\"clear:left;\">
                    <span class=\"swappza-profile-username-text\"> {$username}</span>
                    <span class=\"swappza-profile-address-text\">{$address}</span>
                </div>";

                // TODO: Fetch number of swappzas published and completed by user here
                $publishedSwappzaCount = 0;
                $completedSwappzasCount = 0;
                        
                echo "
                <div class=\"swappza-profile-halfcolumn\" style=\"clear:right;\">
                    <span class=\"swappza-profile-address-text\">
                        {$publishedSwappzaCount} Swappza's posted
                    </span>
                    <span class=\"swappza-profile-address-text\">
                        {$completedSwappzasCount} Swappza's completed
                    </span>
                </div>

                <br/>";
            
                echo "
                <div class=\"swappza-profile-sectionheader-container\">           
                    <hr class=\"swappza-half-horizontal-line-divider\" style=\"clear:left;\">

                    <div class=\"swappza-profile-section-header-text\">{$username}'s Listings</div>

                    <hr class=\"swappza-half-horizontal-line-divider\" style=\"clear:right;\">
                </div>";
            
                //TODO: Query to retrieve listings
                
                //TODO: print out listings data in html form

            }
            
            if (isset($_SESSION['u_ID']) && isset($_GET['id']) && $_SESSION['u_ID'] ==   $_GET['id']) {               
                echo '
                <div class="swappza-profile-sectionheader-container">           
                    <hr class="swappza-half-horizontal-line-divider" style="clear:left;">

                    <div class="swappza-profile-section-header-text">My Account Settings</div>

                    <hr class="swappza-half-horizontal-line-divider" style="clear:right;">
                </div>';
                
            }
            
            if (isset($_SESSION['u_ID']) && is_numeric($_SESSION['u_ID'])  && isset($_SESSION['u_username']) && empty($_SESSION['u_username'])) {
                echo
                '<div class="swappza-profile-form-container" style="clear:both;">
                    <div class="swappza-profile-field-container">
                        <label id="username-field" for="name">Username:</label>
                        <input type="text" name="username" maxlength="20">
                        <br>
                    </div>
                    
                    <div class="swappza-profile-field-container">
                        <label id="password-field" for="password">Password:</label>
                        <input type="password" name="password"/>
                        <br>
                    </div>
                </div>

                <div class="swappza-profile-button-center-container">
                    <button id="username-submit" class="swappza-profile-form-button">Set Username</button>
                </div>';
            }
            
            if (isset($_SESSION['u_ID']) && isset($_GET['id']) && $_SESSION['u_ID'] == $_GET['id'] && isset($_SESSION['u_username']) && !empty($_SESSION['u_username'])) { 
                echo
                '<div class="swappza-profile-form-container" style="clear:left;">
                    <div class="swappza-profile-field-container">
                        <label id="name-field" for="name">Name:</label>
                        <input type="text" name="name" maxlength="255">
                        <br>
                    </div>

                    <div class="swappza-profile-field-container">
                        <label id="phone-field" for="phone">Phone:</label>
                        <input type="text" name="phone" maxlength="15">
                        <br>
                    </div>

                    <div class="swappza-profile-field-container">
                        <label id="email-field" for="email">Email:</label>
                        <input type="text" name="email" maxlength="255">
                        <br>
                    </div>

                    <div class="swappza-profile-field-container">
                        <label id="address-field" for="address">Address:</label>
                        <input type="text" name="address" maxlength="255">
                        <br>
                    </div>
                </div>

                <div class="swappza-profile-form-container" style="clear:right;">
                    <span class="swappza-profile-form-section-label">Change Password</span>

                    <div class="swappza-profile-field-container">
                        <label id="old-password-field" for="old_password">Old Pass:</label>
                        <input type="password" name="old_password"/>
                        <br>
                    </div>

                    <div class="swappza-profile-field-container">
                        <label id="new-password-field" for="new_password">New Pass:</label>
                        <input type="password" name="new_password"/>
                        <br>
                    </div>

                    <div class="swappza-profile-field-container">
                        <label for="confirm_password">Confirm Pass:</label>
                        <input id="confirm-password-field" type="password" name="confirm_password"/>
                        <br>
                    </div>
                </div>

                <div class="swappza-profile-button-center-container">
                    <button id="profile-submit" class="swappza-profile-form-button">Save Settings</button>
                </div>';
            
                echo "
                <script>
                    //Enter placeholder values for text fields (without default formatting)
                    $('name-field').val('{$_SESSION['u_username']}');
                    $('email-field').val('{$_SESSION['u_email']}');
                    $('address-field').val('{$_SESSION['u_address']}');
                </script>";
            }
            
            oab_printContentEnd();
            ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>
        
        <script src="<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js/validate-fields.js"></script>
        <script>
            oab_profileMessageElement = $('#message-container');
            
            //Validate fields and make AJAX request if valid
            /**
             * Display message in return json object (attribute is 'message'
             * @param {parsedJSON} response
             * @returns {undefined}
             */
            function profileSubmitCallback (response) {
                oab_updateMessage(response.message);
                
                if (response.nonceToken) {
                    nonceToken = response.newNonce;
                }
                
                if (response.newNonce) {
                    oabUpdateNonceToken(response.newNonce);
                }
                
                if (response.successful) {
                    location.reload();
                }
            }

            $('#profile-submit').click(function() {
                //Validate fields
                if (!oab_comparePasswords($('new-password-field').val(), $('confirm-password-field').val())) {
                    oab_updateMessage('The new and confirm password fields must match to submit');
                //Is Valid
                } else {
                    oab_updateUserProfile(
                        nonceToken,
                        $('INPUT[name="email"]').val(),
                        $('INPUT[name="name"]').val(),
                        $('INPUT[name="address"]').val(),
                        $('INPUT[name="phone"]').val(),
                        $('INPUT[name="old_password"]').val(),
                        $('INPUT[name="new_password"]').val(),
                        PAGE_ID,
                        FILE_NAME,
                        profileSubmitCallback
                    );
                }
                
                $('INPUT[name="old_password"]').val('');
                $('INPUT[name="new_password"]').val('');
                $('INPUT[name="confirm_password"').val('');
            });

            $('#username-submit').click(function (){
                oab_assignUsername (
                    nonceToken,
                    '<?php echo isset($_SESSION['u_email']) && !empty($_SESSION['u_email']) ? $_SESSION['u_email'] : ''; ?>',
                    $('INPUT[name="username"]').val(),
                    PAGE_ID,
                    FILE_NAME,
                    $('INPUT[name="password"]').val(),
                    profileSubmitCallback
                );
        
                $('INPUT[name="password"]').val('');
            });
            
            //Fill in default values for profile update form
            $('INPUT[name="name"]').val('<?php echo isset($_SESSION['u_username']) ? $_SESSION['u_username'] : ''; ?>');
            $('INPUT[name="phone"]').val('<?php echo isset($_SESSION['u_phone']) ? $_SESSION['u_phone'] : ''; ?>');
            $('INPUT[name="email"]').val('<?php echo isset($_SESSION['u_email']) ? $_SESSION['u_email'] : ''; ?>');
            $('INPUT[name="address"]').val('<?php echo isset($_SESSION['u_address']) ? $_SESSION['u_address'] : ''; ?>');
        </script>

    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?>
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
?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        <!--ADDED SCRIPTS FOR CONTACT PAGE-->
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <!--END OF ADDED SCRIPTS FOR CONTACT PAGE-->
        <!--Use this script only if you need nonce token-->
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'contact.php'); ?>';
            var FILE_NAME = 'contact.php'; 
        </script>
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
            
            <!--Content goes here-->
            <form id="contact-form" method="POST" action="javascript:void(0);">
                <label for="email">E-Mail (optional)</label>
                <br>
                <input name="email" type="text"/>
                <br><br>

                <label for="comment">Comment</label>
                <br>
                <textarea name="comment"> </textarea>
                <br><br>

                <div class="g-recaptcha" data-sitekey="<?php echo $RECAPTCHA_PUBLIC_KEY; ?>"></div>
                <br><br>            

                <button id="submit">Submit</button>
                <br><br>
            </form>        
            
            <script>
                //Ajax call to send form to php script which will use the information
                //Note: what is done with the information has not been coded
                $('#submit').click(function(){
                    $.ajax({
                    url: SCRIPT_FOLDER_PATH + '/contact-us.php',
                    type: 'POST',
                        data: {
                            email: $('input[name=email]').val(),
                            message: $('input[name=comment]').val(),
                            fileName: FILE_NAME,
                            nonceToken: nonceToken,
                            pageID: null,
                            gRecaptchaResponse: $('#g-recaptcha-response').val()
                        },
                        success: function(data) {
                            console.log(data);
                            nonceToken = data.newNonce;                   
                            oab_updateMessage(data.message);
                            
                            if (data.successful) {
                                $('#contact-form').css('display', 'none');
                            }
                        }                            
                    });
                });
                
            </script>
            <!--End Content-->
            
            <?php oab_printContentEnd(); ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>
        
    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?> 
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

//Forum Specific Code Start here
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php";

$postID = filter_input(INPUT_GET, 'postID');

$post = oabEFGetPost($mysqli, $postID);

$authorID = $post->getAuthor()->getUserID();

//If the user is not logged in or the author is not the same as the one given in the post then give them 401 error
if (!is_numeric($_SESSION['u_ID']) || ($_SESSION['u_ID'] == $authorID)) {
    header("HTTP/1.1 401 Unauthorized");
    require "{$PRODUCTION_DOMAIN_BASE}/401.php";
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        
        <!--Use this script only if you need nonce token-->
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'edit-post.php'); ?>';
            var FILE_NAME = 'edit-post.php'; 
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
            <!--Some content was posted above the html tag for this page-->
            <div id='edit-post-form'>
                <label for='title'>Title</label>
                <input type='text' id='title' name='title'>
                <br><br>
                
                <label for='text'>Text</label>
                <textarea id='text' name='text'></textarea>
                <br/><br>
                
                <button id='submit'>Save Changes</button>
            </div>
            <script>                
                $('#submit').click(function() {
                    $.post(
                        DOMAIN_BASE + '/forum/scripts/editPost.php',
                        {
                            'text': $('#text').val(),
                            'title': $('#title').val(),
                            'pageID': null,
                            'nonceToken': nonceToken,
                            'fileName': FILE_NAME,
                            'postID': <?php echo $_GET['postID']; ?>
                        },
                        function (data) {
                            oab_updateMessage(data.message);
                            nonceToken = data.newNonce;

                            if(data.successful) {
                                $('edit-post-form').hide();
                            }
                        }
                    );
                });                
            </script>
            <!--END CONTENT-->
            
            <?php oab_printContentEnd(); ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>
        
    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?> 
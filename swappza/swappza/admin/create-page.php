<?php 
session_start();

//Retrieve values from properties file
require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

require_once("{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/pre-html.php");
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/head.php"; ?>
    </head>

    <body>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/left-menu.php"; ?>

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-start.php"; ?>

        <!--Title Bar-->
        <div class="backend-content-title-bar">
            <img class="backend-content-title-bar-icon" alt="pages-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Pages.png" />

            <span class="backend-content-title-bar-text">
                Create New Page
            </span>
        </div>
        <!-- END TITLE BAR-->
        
        <!--CONTENT-->
        <div id="form">
            <label>Visible Title</label>
            <p>The title that users will see on the page. Max 100 characters</p>
            <input id="visibletitle" name="newpage-field-visibletitle" type="text" maxlength="100" />

            <label>Link Title</label>
            <p>The title that will be used in the link. Should not include any spaces or question marks. Max 100 characters</p>
            <input id="linktitle" name="newpage-field-linktitle" type="text" maxlength="100" />

            <label>Content</label>
            <textarea id="newpageFieldContent" name="newpage-field-content" rows="1" cols="1"></textarea>

            <label>Styles</label>                    
            <p>Other stylesheets to be used from the standard styles directory. Separated by colon (:). Max 255 characters.</p>
            <input id="styles" name="newpage-field-styles" type="text" maxlength="255" />

            <button id="submit-button">Create New Page</button>
        </div>
        
        <button id="return-button">Return</button>
        <!--END CONTENT-->

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
        
        <script>
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'create-page.php'); ?>';
            
            //Show content editor
            CKEDITOR.config.extraAllowedContent = 'div(*)';
            CKEDITOR.replace('newpageFieldContent');
            
            //Send request to make new page on click
            $('#return-button').click(function() {
                location.replace("<?php echo $BACKEND_DOMAIN_BASE;?>/pages.php");
            });
            
            $('#submit-button').click(function(){
                $.post(
                    "<?php echo $BACKEND_DOMAIN_BASE;?>/scripts/backendCreatePage.php",
                    {
                        'content': CKEDITOR.instances.newpageFieldContent.getData(),
                        'visibleTitle': $('#visibletitle').val(),
                        'style': $('#styles').val(),
                        'linkTitle': $('#linktitle').val(),
                        'fileName': 'create-page.php',
                        'nonceToken': nonceToken,
                        'pageID': null
                    },
                    function(data) {
                        console.debug(data);
                        oab_updateMessage(data.message);
                        
                        nonceToken = data.newNonce;
                        
                        if (data.successful) {
                            oab_backendMessageContainer.hide();
                            
                            $('#form').css('display', 'none');
                        }
                    }
                )                
            });
        </script>
        
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
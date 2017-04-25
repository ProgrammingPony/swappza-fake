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
                Edit Page
            </span>
        </div>
        <!-- END TITLE BAR-->
        
        <!--CONTENT-->

        <?php
        $pageID = filter_input(INPUT_GET, 'pageID');
        $page = oabGetPage($mysqli, $pageID);
        ?>
        
        <div id='form'>
            <label>Visible Title</label>
            <p>The title that users will see on the page. Max 100 characters</p>
            <input id="visibletitle" name="editpage-field-visibletitle" type="text" maxlength="100" value="<?php echo $page->getVisibleTitle();?>"/>

            <label>Link Title</label>
            <p>The title that will be used in the link. Should not include any spaces or question marks. Max 100 characters</p>
            <input id="linktitle" name="editpage-field-linktitle" type="text" maxlength="100" value="<?php echo $page->getLinkName();?>"/>

            <label>Content</label>
            <textarea id="editpageFieldContent" name="editpageFieldContent" rows="1" cols="1"><?php echo $page->getContent();?></textarea>

            <label>Styles</label>                    
            <p>Other stylesheets to be used from the standard styles directory. Separated by colon (:). Max 255 characters.</p>
            <input id="styles" name="editpage-field-styles" type="text" maxlength="255" value="<?php echo $page->getStyles();?>"/>

            <button id="submit-button">Update Page</button>
        </div>
        
        <button id="return-button">Return</button>
        <!--END CONTENT-->

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
        
        <script>
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'edit-page.php'); ?>';
            
            //Make return button take effect
            $('#return-button').click(function(){
                location.replace('<?php echo $BACKEND_DOMAIN_BASE; ?>/pages.php');
            });
            
            $('#submit-button').click(function(){
                $.post(
                    "<?php echo $BACKEND_DOMAIN_BASE;?>/scripts/backendUpdatePage.php",
                    {
                        'editPageID': <?php echo $pageID; ?>,
                        'content': CKEDITOR.instances.editpageFieldContent.getData(),
                        'visibleTitle': $('#visibletitle').val(),
                        'style': $('#styles').val(),
                        'linkTitle': $('#linktitle').val(),
                        'fileName': 'edit-page.php',
                        'nonceToken': nonceToken,
                        'pageID': null
                    },
                    function (data,status) {
                        console.debug(data);
                        nonceToken = data.newNonce;
                        
                        oab_updateMessage(data.message);
                        
                        if (data.successful) {
                            $('#form').css('display', 'none');
                        }
                    }
                )
            });
            
            CKEDITOR.config.extraAllowedContent = 'div(*)';
            CKEDITOR.replace('editpageFieldContent');
        </script>
        
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
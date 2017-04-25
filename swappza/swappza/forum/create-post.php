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

//Ensure user is logged in to use this page
if (!is_numeric($_SESSION['u_ID'])) {
    header("HTTP/1.1 401 Unauthorized");
    require ("{$LOCAL_BASE_DOMAIN_ADDRESS}/401.php");
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
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'create-post.php'); ?>';
            var FILE_NAME = 'create-post.php'; 
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
            
            <div id='new-topic-form'>
                <p>Step 1: Forum Details</p>
                <select id="category" required>
                    <option value="" disabled selected>Select a Category</option>

                    <?php
                    //Print All categories as options for select
                    $categories = oabEFGetAllCategories($mysqli);

                    foreach ($categories as $category) {
                        echo "<option value='{$category->getID()}'>{$category->getName()}</option>";
                    }
                    ?>
                </select>

                <select id="sub-category" style="display:none;" required>
                    <option value="" disabled selected>Select a SubCategory</option>

                </select>

                <script>
                    $('#category').change(function(){                    
                        //Update subcategory select after category selected
                        $.post(DOMAIN_BASE + '/forum/scripts/getSubCategoryByCategory.php',
                            {
                                categoryID: $('#category').val()                        
                            },
                            function(data, status){                          
                                $('#sub-category').html('');
                                $('#sub-category').prepend('<option value="" disabled selected>Select a SubCategory</option>');

                                var subCategories = data.subCategories;

                                for (i in subCategories) {

                                    $('#sub-category').append('<option value="' + subCategories[i].id + '" disable selected>' + subCategories[i].name + '</option>');
                                }

                                $('#sub-category').css('display', 'inline-block');
                            }
                        ); 
                    });
                </script>

                <br><br>

                <label for="title">Title:</label>
                <input id='title' type="text" name="title" placeholder='Enter Title Here' required>

                <br><br>

                <label for="text">text</label>
                <textarea id="text" name="text" placeholder="Enter the content here. Maximum 1056 characters." required></textarea>

                <br><br>
                
                <button id='create-post-button'>Create New Post</button>
                
                <p>You will be given an opportunity to add attachments in the next step</p>
            </div>
            <script>
                $('#create-post-button').click(function(){                    
                    //Update subcategory select after category selected
                    $.post(
                        DOMAIN_BASE + '/forum/scripts/createNewPost.php',
                        {
                                nonceToken: nonceToken,
                                fileName: FILE_NAME,
                                pageID: null,
                                title: $('#title').val(),
                                text: $('#text').val(),
                                subCategoryID: $('#sub-category').val()
                        },
                        function(data, status){                                                          
                            if (data.successful) {
                                $('#new-topic-form').hide();
                                $('#attachment-upload-form').show();
                            }
                            
                            oab_updateMessage(data.message);
                            
                            $('#post-id').val(data.postID);
                            $('#nonce-token').val(data.newNonce);
                            nonceToken = data.newNonce;
                        }
                    ); 
                });
            </script>
            
            <form method="post" id="attachment-upload-form" name="new-topic-form" action="<?php echo "{$PRODUCTION_DOMAIN_BASE}";?>/forum/scripts/uploadPostAttachments.php" enctype="multipart/form-data" style="display:none;">
                <p>Step2: Attachments</p>
                <input type="file" name="attachment1" id='hi'>
                <br>
                <input type="file" name="attachment2">
                <br>
                <input type="file" name="attachment3">
                <br>
                
                <input type="text" id="nonce-token" name="nonceToken" style="display:none">
                <input type="text" id="file-name" name="fileName" style="display:none">
                <input type="text" id="page-id" name="pageID" style="display:none">
                <input type="text" id="post-id" name="postID" style="display:none">
                
                <input type="text" name="attachmentFieldName1" style="display:none" value="attachment1">
                <input type="text" name="attachmentFieldName2" style="display:none" value="attachment2">
                <input type="text" name="attachmentFieldName3" style="display:none" value="attachment3">
                
                <input id="attachment-submit" type="submit" value="Upload Attachments">
                
                <iframe id="upload_target" name="upload_target" src="about:blank" style="display:none;width:0;height:0;border:0px solid #fff;"></iframe>                    
            </form>
            <script>
            //Fill values in hidden form elements
            $('#file-name').val(FILE_NAME);
            $('#nonce-token').val(nonceToken);
            $('#page-id').val(null);
            //PostID should be set already
            
            //Make form uplaod files without refreshing page
            $('#attachment-upload-form').attr('target', 'upload_target');
            $('#upload_target').load(function(){
                var response = frames['upload_target'].document.getElementsByTagName("body")[0].firstChild.innerHTML;
                response = jQuery.parseJSON(response);
                
                nonceToken = response.newNonce;
                
                oab_updateMessage(response.message);
                
                //Move them to the homepage for the forum if successful, currently assumes that adding attachments is successful
                if (response.successful) {
                    location.replace("<?php echo "{$PRODUCTION_DOMAIN_BASE}";?>/forum/index.php?postID=" + $('#post-id').val());
                }
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
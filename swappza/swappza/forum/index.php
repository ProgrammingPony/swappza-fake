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
?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'index.php'); ?>';
            var FILE_NAME = 'index.php'; 
        </script>
        
        <link rel="stylesheet" type="text/css" href="<?php echo $PRODUCTION_DOMAIN_BASE;?>/forum/forum-style.css" />
        
        <title>Forum</title>
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
            <?php
            //Fetch GET Parameters from link
            $categoryID = filter_input(INPUT_GET, 'categoryID');
            $postID = filter_input(INPUT_GET, 'postID');
            $subCategoryID = filter_input(INPUT_GET, 'subCategoryID');
            
            $ACCEPTED_IMAGE_FORMATS = array('JPG', 'JPEG', 'GIF', 'PNG', 'BMP'); 

            if (is_numeric($_SESSION['u_ID'])) {                    
                echo "<a href='{$PRODUCTION_DOMAIN_BASE}/forum/create-post.php'>Create New Post</a>";
            } else {
                echo '<p>In order to create a new post you have to be logged in, then the link will appear instead of this text</p>';
            }
            ?>
            
            <br>
            
            <?php
            //Post has highest priority
            if (is_numeric($postID)) {
                
                //Fetch post information
                $post = oabEFGetPost($mysqli, $postID);
                
                //If there is a post with the given ID
                if (!is_null($post) && !empty($post)) {
                    //Posting post details with no formatting showing you how to use values
                    $authorID = $post->getAuthor()->getUserID();
                    
                    if ($_SESSION['u_ID'] == $authorID) {
                        echo "<a href='{$PRODUCTION_DOMAIN_BASE}/forum/edit-post.php?postID={$postID}'>Edit this Post</a>";
                    } else {
                        echo 'If this was a post made by the logged in user, it would show an "Edit this post" link here';
                    }
                    
                    echo '<br>';
                    
                    echo 'At the highest priority we check to see if a postid is specified in the url, we then display the post on the page.';
                    echo '<br>';

                    echo "Post ID: {$post->getID()}";
                    echo '<br>';
                    echo "Author  {$post->getAuthor()->getName()}";
                    echo '<br>';
                    
                    echo 'For more information you can display about the author ctrl+f find oabUser in user-management.php';

                    echo '<br>';

                    echo "Title: {$post->getTitle()}";
                    echo '<br>';
                    echo "Text: {$post->getText()}";
                    echo '<br>';
                    echo "First Publication Date: {$post->getDateOfPosting()}" ;
                    echo '<br>';
                    echo "Most Recent Edit: {$post->getLastEditDate()}";
                    echo '<br>';

                    echo '<br>';

                    echo 'Attachments:';

                    $attachments = $post->getAttachments();

                    foreach($attachments as $attachment) {
                        $fileType = $attachment->getType();                        

                        echo '<br>';
                        
                        //If its an image show it directly
                        $fileTypeLowercase = strtolower($fileType);
                        if (in_array(strtoupper($fileType), $ACCEPTED_IMAGE_FORMATS)) {
                            echo "<img alt=\"{$attachment->getName()}\" src=\"{$PRODUCTION_DOMAIN_BASE}/forum/attachments/{$attachment->getID()}.{$fileTypeLowercase}\" style=\"display:block; max-width:100%; height:auto;\">";
                        //Otherwise they have to get redirected to the file in the browser (downloads or if viewers install they can see it)
                        } else {                            
                            echo "<a href=\"{$PRODUCTION_DOMAIN_BASE}/forum/attachments/{$attachment->getID()}.{$fileTypeLowercase}\">{$attachment->getName()}</a>";
                        }                    
                    };

                    echo '<hr>';
                    
                    //Posting first 15 comments for this post (There may not be 15 comments for the post)
                    $comments  = oabEFGetCommentsByPost($mysqli, $postID, 15, 0);

                    foreach($comments as $comment) {
                        $commenter = $comment->getAuthor();
                        echo "A comment by: {$commenter->getName()}";
                        echo '<br>';
                        echo "First Publish Date: {$comment->getDateOfPosting()}";
                        echo '<br>';
                        echo "Last Update Date: {$comment->getLastEditDate()}";
                        echo '<br>';
                        echo "Comment: {$comment->getText()}";
                        echo '<br>';
                        echo 'Attachments:';
                        echo '<br>';
                        
                        $attachments = oabEFGetAttachmentByComment($mysqli, $comment->getID());
                        
                        foreach($attachments as $attachment) {
                            $fileType = $attachment->getType();

                            echo '<br>';
                            
                            //If its an image show it directly
                            $fileTypeLowercase = strtolower($fileType);
                            if (in_array(strtoupper($fileType), $ACCEPTED_IMAGE_FORMATS)) {
                                echo "<img alt=\"{$attachment->getName()}\" src=\"{$PRODUCTION_DOMAIN_BASE}/forum/attachments/{$attachment->getID()}.{$fileTypeLowercase}\" style=\"display:block; max-width:100%; height:auto;\">";
                            //Otherwise they have to get redirected to the file in the browser (downloads or if viewers install they can see it)
                            } else {

                                echo "<a href=\"{$PRODUCTION_DOMAIN_BASE}/forum/attachments/{$attachment->getID()}.{$fileTypeLowercase}\">{$attachment->getName()}</a>";
                            }                    
                        };
                        echo '<hr>';
                    }
                    
                    //Allow the user to comment                    

                    
                    
                    
                    
                    
                //Post ID does not exist
                } else {
                    echo 'Post ID does not exist';
                }
                
            }
            
            //Topic listings under subcategory has second highest priority
            elseif (is_numeric($subCategoryID)) {
                $posts = oabEFGetPostsBySubCategory($mysqli, $subCategoryID, 0, 15); //Retrieves 15 most recent posts under this subcategory
                
                echo 'Since no postID was provided and a subcategory id was provided in the url, we will display the 15 most recent posts listed on the camp';
                echo '<br>';
                
                foreach ($posts as $post) {
                    echo "<a href='{$PRODUCTION_DOMAIN_BASE}/forum/index.php?postID={$post->getID()}'>{$post->getTitle()}</a>";
                    echo '<br>';
                    echo '<hr>';
                }
            }
            
            //Topic listings under category has third highest priority
            elseif (is_numeric($categoryID)) {
                $subCategories = oabEFGetAllSubCategoriesByCategory($mysqli, $categoryID); //Fetches all subcatagories (and their details) associated with provided category ID
                
                if (count($subCategories)>0) {
                    echo 'Since no postID was provided and a categoryID was provided in the url, we list all the subcategories that fall under the category indicated';
                    echo '<br>';
                    echo '<br>';

                    foreach($subCategories as $subCategory) {
                        echo "Name: {$subCategory->getName()}";
                        echo '<br>';
                        echo "ID={$subCategory->getID()}";
                        echo '<br>';
                        echo "Description: {$subCategory->getDescription()}";
                        echo '<br>';
                        echo "Link: <a href='{$PRODUCTION_DOMAIN_BASE}/forum/index.php?subCategoryID={$subCategory->getID()}'>{$PRODUCTION_DOMAIN_BASE}/forum/index.php?subCategoryID={$subCategory->getID()}</a>";
                        echo '<br><br>';
                    }

                    echo '<br>';
                } else {
                    echo 'No such category exists';
                }
                
            }
            
            //If none provided then show categories and their subcategories
            else {
                $categories = oabEFGetAllCategories($mysqli);
                
                foreach ($categories as $category) {
                    echo '<br>';
                    
                    echo "CategoryID={$category->getID()}<br>"
                    . "Name={$category->getName()}<br>"
                    . "Description={$category->getDescription()}<br>"
                    . "Link=<a href='{$PRODUCTION_DOMAIN_BASE}/forum/index.php?categoryID={$category->getID()}'>{$PRODUCTION_DOMAIN_BASE}/forum/index.php?categoryID={$category->getID()}</a>";
                    
                    echo '<br><br>';
                    
                    //Print subcategory details
                    $subCategories = $category->getSubCategories();
                    
                    foreach ($subCategories as $subCategory) {
                        echo '<ul>';
                        
                        echo "<li>SubCategoryID={$subCategory->getID()}</li>"
                        . "<li>Name={$subCategory->getName()}</li>"
                        . "<li>Description={$subCategory->getDescription()}</li>"
                        . "<li>Link=<a href='{$PRODUCTION_DOMAIN_BASE}/forum/index.php?subCategoryID={$subCategory->getID()}'>{$PRODUCTION_DOMAIN_BASE}/forum/index.php?subCategoryID={$subCategory->getID()}</a>";
                        
                        echo '</ul>';
                        
                        echo '<br>';
                    }
                    
                    echo '<hr>';

                }
            }
            ?>
            
            <?php if ($_SESSION['u_ID']) { ?>
            <div id="comment-form">
                <label for="comment">Comment</label>
                <textarea id="comment" name="comment" placeholder="Enter your comment here."></textarea>
                <br>
                <button id="comment-submit">Submit Comment</button>
            </div>
            
            <form method="post" id="attachment-upload-form" name="new-topic-form" novalidate="novalidate" enctype="multipart/form-data" style="display:none;">
                <p>Step2: Attachments</p>
                <input type="file" name="attachment1" id='hi'>
                <br>
                <input type="file" name="attachment2">
                <br>
                <input type="file" name="attachment3">
                <br>
                
                <input id="attachment-submit" type="submit" value="Upload Attachments">
            </form>
            
            <script>
                <!--Comment attachment Upload has not yet been added-->
                $('#comment-submit').click(function(){                    
                    //Update subcategory select after category selected
                    $.post(
                        DOMAIN_BASE + '/forum/scripts/createNewComment.php',
                        {
                                nonceToken: nonceToken,
                                fileName: FILE_NAME,
                                pageID: null,
                                text: $('#comment').val(),
                                postID: <?php echo $postID; ?>
                        },
                        function(data){                                                          
                            if (data.successful) {
                                $('#comment-form').hide();
                                $('#attachment-upload-form').show();
                            }

                            oab_updateMessage(data.message);

                            nonceToken = data.newNonce;
                            
                            console.debug(data);
                        }
                    ); 
                });
            </script>
            <?php } ?>
            <!--Content ends here-->
            
            
            <?php oab_printContentEnd(); ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>

    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?> 
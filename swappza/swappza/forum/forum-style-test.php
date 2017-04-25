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

?>

<!DOCTYPE html>
<html>
    <head>
        <?php oab_printHead('Login / Register', 'login.php'); ?>
        
        <script>
            var TEMPLATE_BASE = '<?php $PRODUCTION_DOMAIN_BASE;?>/template';
            var DOMAIN_BASE = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>';
            var JS_FOLDER_PATH = '<?php echo $PRODUCTION_DOMAIN_BASE; ?>/js';
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'forum.php'); ?>';
            var FILE_NAME = 'forum.php'; 
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
            Done Testing layout
            <!--Test basic category-subcategory page-->
            <br>
            Category and SubCategory Page (Default)
            <div class="ef-centered-container">
                <div class="ef-p1-category-container">
                    <span class="ef-p1-category-text">Category 1</span>
                    <div class="ef-p1-subcategory-container">
                        <a class="ef-p1-subcategory-text" href="#">SubCategory 1</a>
                    </div>

                    <div class="ef-p1-subcategory-container">
                        <a class="ef-p1-subcategory-text" href="#">SubCategory 1</a>
                    </div>
                </div>
            </div>
            
            <div class="ef-centered-container">
                <div class="ef-p1-category-container">
                    <span class="ef-p1-category-text">Category 2</span>
                    <div class="ef-p1-subcategory-container">
                        <a class="ef-p1-subcategory-text" href="#">SubCategory 3</a>
                    </div>
                    <div class="ef-p1-subcategory-container">
                        <a class="ef-p1-subcategory-text" href="#">SubCategory 4</a>
                    </div>
                </div>
            </div>
            
            <!--Subcategory and Forum topic page-->
            Subcategory and Forum Topic page
            <div class="ef-centered-container">
                <div class="ef-p2-subcategory-container">
                    <span class="ef-p2-breadcrumb">
                        <a href="#" class="ef-p2-breadcrumb-link">category1</a>
                        &gt
                        <a href="#" class="ef-p2-breadcrumb-link">subcategory2</a>
                    </span>
                    
                    <div class="ef-p2-forumtopic-container">
                        <a href="#" class="ef-p2-forumtopic-text">Forum Topic #1</a>
                        
                    </div>                        
                </div>
            </div>
            
            <br>
            <!--Forum Post and Comments Example-->
            Forum Post and Comment Example
            <div class="ef-centered-container">
                <div class="ef-p3-subcategory-container">
                    <span class="ef-p3-breadcrumb">
                        <a href="#" class="ef-p3-breadcrumb-link">category 1</a>
                        &gt
                        <a href="#" class="ef-p3-breadcrumb-link">subcategory 2</a>
                        &gt
                        <a href="#" class="ef-p3-breadcrumb-link">forum topic title or id</a>
                    </span>
                    
                    <div class="ef-p3-forumtopic-container">
                        <div id="ef-p3-forumtopic-header-container">
                            <span id="ef-p3-forumtopic-title" class="ef-p3-forumtopic-title-text">Forum Topic #1</span>
                            
                            <span class="ef-p3-forumtopic-header">
                                Author:
                                <a id="ef-p3-forumtopic-authorname" href="#">Name</a>
                            </span>
                            
                            <span class="ef-p3-forumtopic-header">
                                First Published:
                                <span id="ef-p3-forumtopic-firstpublished">00/00/0000</span>
                            </span>
                            
                            <span class="ef-p3-forumtopic-header">
                                Last Updated:
                                <span id="ef-p3-forumtopic-lastupdated">00/00/0000</span>
                            </span>
                        </div>
                        
                        <div id="ef-p3-forumtopic-textbody-container">
                            Text Content
                        </div>
                        
                        <div id="ef-p3-forumtopic-attachment-container">
                            <span class="ef-p3-forumtopic-header">Attachments:</span>
                            <a href="#" class="ef-p3-forumtopic-attachment-item">Attachment 1</a>
                            <a href="#" class="ef-p3-forumtopic-attachment-item">Attachment 2</a>
                            <a href="#" class="ef-p3-forumtopic-attachment-item">Attachment 3</a>
                        </div>
                    </div>                        
                </div>
            </div>
            
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

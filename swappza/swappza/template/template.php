<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";

//Establish database connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

//Determine link to display for profile depending on whether user is logged in or not
$profileIconLink = empty($_SESSION['u_ID']) ? 'login.php' : "profile.php?id={$_SESSION['u_ID']}";

/**
 * Prints the content of the head tag from the template
 * 
 * @param string $title
 * @param string/int $pageID if no page id then page name
 */
function oab_printHead ($title, $pageID) {
    global $mysqli, $FAVICON_LOCATION, $BASE_STYLESHEETS, $ABSOLUTE_TEMPLATE_BASE_DIR, $PRODUCTION_DOMAIN_BASE, $ABSOLUTE_FRONTEND_SCRIPTS_LOCATION;
    
    echo "<meta charset=\"UTF-8\">
        <meta name=\"robots\" content=\"index\"/>

        <link rel=\"icon\" href=\"{$FAVICON_LOCATION}\" type=\"image/x-icon\" />";
        
    foreach(explode(":", $BASE_STYLESHEETS) as $link) {
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$ABSOLUTE_TEMPLATE_BASE_DIR}/{$link}\">";     
    }
    
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>';
    echo '<script>'
    . "var SCRIPT_FOLDER_PATH = '{$ABSOLUTE_FRONTEND_SCRIPTS_LOCATION}';"
    . '</script>';
}

/**
 * Prints upper top  menu from the template, should be before the lower top menu
 * 
 */
function oab_printUpperMenu() {
    global $PRODUCTION_DOMAIN_BASE;
    $locationFieldPlaceholder = isset($_SESSION['u_cityName']) ? "{$_SESSION['u_cityName']}, {$_SESSION['u_provinceName']}, {$_SESSION['u_nationName']}" : 'Enter Your City';
    
    echo "
        <div class=\"one-column-container centered-container\">
        <img id=\"top-logo-1\" src=\"{$PRODUCTION_DOMAIN_BASE}/swappza-logo-2014.png\" alt=\"Swappza Logo\" class=\"top-logo top-content-item\">

        <div id=\"top-menu-1\" class=\"upper-header-menu\">
          <a id=\"top-menu-1-home\" class=\"top-menu-1-notselected top-menu-1-text\" href=\"{$PRODUCTION_DOMAIN_BASE}/index.php?page=home\">Home</a>
          |
          <a class=\"top-menu-1-notselected top-menu-1-text\" href=\"{$PRODUCTION_DOMAIN_BASE}/categories.php\">Categories</a>
          |
          <a class=\"top-menu-1-notselected top-menu-1-text\" href=\"{$PRODUCTION_DOMAIN_BASE}/forum/index.php\">Forum</a>
          |
          <a class=\"top-menu-1-notselected top-menu-1-text\" href=\"{$PRODUCTION_DOMAIN_BASE}/contact.php\">Contact Us</a>

          <span id=\"top-location-container\">
            <input id=\"top-location-field\" type=\"text\" placeholder=\"{$locationFieldPlaceholder}\" name=\"top_menu_location\">
          </span>
          
          
        </div>
    </div>";
}

/**
 * Prints the lower top menu from the template
 * 
 * @global string $PRODUCTION_DOMAIN_BASE
 * @global string $ABSOLUTE_TEMPLATE_BASE_DIR
 * @global string $profileIconLink
 */
function oab_printLowerMenu() {
    global $PRODUCTION_DOMAIN_BASE;
    global $ABSOLUTE_TEMPLATE_BASE_DIR;
    global $profileIconLink;
    
    echo "
        <div class=\"one-column-container centered-container swappza-prefeatured\">
            <div id=\"lower-header-profile-container\" class=\"prefeatured-item-container\">
                <a id=\"lower-header-profie-link\" href=\"{$PRODUCTION_DOMAIN_BASE}/{$profileIconLink}\">
                    <span>
                        <img id=\"swappza-template-profile-icon\" alt=\"profile-icon\" src=\"{$PRODUCTION_DOMAIN_BASE}/template/images/profile_picture_black.png\"/>";
                        
                        if (!empty($_SESSION['u_username'])) {
                            echo $_SESSION['u_username'];
                        } elseif (!empty($_SESSION['u_ID'])) {
                            echo 'Set your Username';
                        } else {
                            echo 'Login / Register';
                        }
    echo "
                    </span>
                </a>
            </div>

            <div id=\"lower-header-post-swap-container\" class=\"prefeatured-item-container\">
                <a href=\"{$PRODUCTION_DOMAIN_BASE}/index.php?page=post_swap\" class=\"new-ads-near-you-link\">Post a Swap!</a>
            </div>

            <div id=\"lower-header-social-icon-container\">
                <a id=\"lower-header-facebook-social-link\" href=\"#\">
                    <img id=\"lower-header-facebook-social-icon\" class=\"lower-header-social-icon\" alt=\"facebook-social-icon\" src=\"{$ABSOLUTE_TEMPLATE_BASE_DIR}/images/FaceBook-Icon.png\">
                </a>

                <a id=\"lower-header-twitter-social-link\" href=\"#\">
                    <img id=\"lower-header-twitter-social-icon\" class=\"lower-header-social-icon\" alt=\"facebook-social-icon\" src=\"{$ABSOLUTE_TEMPLATE_BASE_DIR}/images/Twitter.png\">
                </a>
            </div>

            <div class=\"prefeatured-item-container\">
                <a href=\"<{$PRODUCTION_DOMAIN_BASE}/index.php?page=swaps_near_you\" class=\"new-ads-near-you-link\">New Swaps Near You</a>
            </div>
        </div>";  
}

/**
 * Prints the message bar from the template, should be immediately after lower top menu
 */
function oab_printMessage() {
    echo "
    <div id=\"message-section\" class=\"full-width-container centered-container message-background centered-text\""; if (!empty($error_message)) echo ' style="display:block;"'; echo ">            
        <div id=\"message-container\" class=\"one-column-container centered-container\">";
    echo '
        </div>
    </div>';
}

/**
 * Prints the area right before the page content. CMS Page content should be placed right after this
 */
function oab_printContentStart() {  
    echo '
        <div class="full-width-container centered-container body-background">            
        <article class="one-column-container centered-container body-content">';
}

/**
 * Prints the bottom part of the content from the template. The page content should go right before this.
 */
function oab_printContentEnd() {
    echo '
        </article>
    </div>' ;
}

/**
 * Print the contents of the footer from the template
 * 
 * @global string $PRODUCTION_DOMAIN_BASE
 */
function oab_printFooter() {
    global $PRODUCTION_DOMAIN_BASE, $ABSOLUTE_FRONTEND_SCRIPTS_LOCATION, $BACKEND_DOMAIN_BASE;
    echo "
    <footer class=\"full-width-container centered-container footer-background\">
        <div class=\"one-column-container centered-container footer-content\">
            &copy; Swappza 2015
            - <a class=\"footer-link\" href=\"{$PRODUCTION_DOMAIN_BASE}/index.php?page=terms_of_use\">Terms of Use</a>
            - <a class=\"footer-link\" href=\"{$PRODUCTION_DOMAIN_BASE}/contact.php\">Contact Us</a>";

            //Show logout button if User is logged in
            if (!empty($_SESSION ['u_ID']) &&  $_SESSION ['u_ID']) {
                echo " - <a class=\"footer-link\" href=\"{$ABSOLUTE_FRONTEND_SCRIPTS_LOCATION}/logout.php\">Logout</a>";
            }

            //Show backend link if user is admin
            if (!empty($_SESSION ['u_role']) &&  $_SESSION ['u_role'] == "Administrator") {
                echo " - <a class=\"footer-link\" href=\"{$BACKEND_DOMAIN_BASE}\">Go To Backend</a>";
            }
    echo '
        </div>
    </footer>';
}

/**
 * Print the scripts that follow the footer before end of body
 * 
 * @global string $ABSOLUTE_TEMPLATE_BASE_DIR
 * @global string $PRODUCTION_DOMAIN_BASE
 */
function oab_printEnd () {
    global $ABSOLUTE_TEMPLATE_BASE_DIR, $PRODUCTION_DOMAIN_BASE;
    //JS Constants
    echo "
            <script>
            TEMPLATE_BASE = '{$ABSOLUTE_TEMPLATE_BASE_DIR}';
        </script>";
    
    //Scripts to run at the end
    echo "
        <script src=\"{$PRODUCTION_DOMAIN_BASE}/js/base.js\"></script>
        <script src=\"{$PRODUCTION_DOMAIN_BASE}/js/db.js\"></script>";
}

?>
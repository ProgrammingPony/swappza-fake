<?php 
session_start();

//Link the following link using computer directories to the properties file, which should not be viewable except by admin
require_once "/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php";
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$TEMPLATE_BASE_DIR}/template.php";

//Get page information to fetch page contents, otherwise assume home page'
if (empty($_GET['page'])) {
    $requested_page = 'home';
} else {
    $requested_page = filter_input(INPUT_GET, 'page');
}

$PAGE = array(
    'styles' => '',
    'visibleTitle' => '',
    'content' => '',
    'lastUpdated' => '',
    'datePublished' => '',
    'id' => '',
    'nonceToken' => '',
    'showPublishDate' => '',
    'showUpdateDate' => ''
);

$stmt = $mysqli->prepare("SELECT pageID,visibleTitle,datePublished,content,lastUpdated,styles,showUpdateDate,showPublishDate from Page WHERE linkTitle=?");
$stmt->bind_param('s', $requested_page);
$stmt->execute();
$stmt->bind_result($r_pageID, $r_visibleTitle, $r_datePublished, $r_content, $r_lastUpdated, $r_styles, $r_showUpdateDate, $r_showPublishDate);

if ($stmt) {
    if ($stmt->fetch()) {
        $PAGE['id'] = $r_pageID;
        $PAGE['visibleTitle'] = $r_visibleTitle;
        $PAGE['datePublished'] = $r_datePublished;
        $PAGE['content'] = $r_content;
        $PAGE['lastUpdated'] = $r_lastUpdated;
        $PAGE['styles'] = $r_styles;
        $PAGE['showUpdateDate'] = $r_showUpdateDate;
        $PAGE['showPublishDate'] = $r_showPublishDate;

    //Now its a 404 page if the page could not be found
    } else {
        header("HTTP/1.0 404 Not Found");
        $PAGE['visibleTitle'] = '404 - Page Not Found';
    }

    $stmt->close(); 
}

//Special page types like forums need additional code
if (!empty($PAGE['id'])) {
    switch($requested_page) {

        //Setup forum page content from get
        case $EMBEDDED_FORUM_PAGE_NAME: 
            if ($EMBEDDED_FORUM_IS_ACTIVE) {
                require("{$EMBEDDED_FORUM_LOCATION}/ef_package.php");

                //Get forum post details if forum post ID specified
                if ( isset($_GET['forumpost']) ) {
                    $forumPostData = efFetchPostDetails ($mysqli, $_GET['forumpost']);
                }

                //Get forum post summary list if forum subcategory specified
                else if ( isset($_GET['forumsubcategory']) ) {
                    $forumPostData = efFetchPostSummaries (
                            $mysqli, 
                            $_GET['forumsubcategory'], 
                            isset($_GET['forumpostoffset']) ? $_GET['forumpostoffset'] : 0, 
                            isset($_GET['forumpostquantity']) ? $_GET['forumpostquantity'] : 30
                    );
                }

                //Get list of subcategories and their parent categories for display
                else {
                    $forumCategoryData = efFetchCategories($mysqli);
                }

                //TODO
            }

            break;
    } 
}
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    
    <head>
        <?php 
            oab_printHead($PAGE['visibleTitle'], $PAGE['id']); 
            foreach(explode(':', $PAGE['styles']) as $link) {
                if ($link !== '') {
                    echo '<link rel="stylesheet" type="text/css" href="' . $PRODUCTION_DOMAIN_BASE . '/template/' . $link . '">';     
                }
            }
        ?>
    
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
            <?php 
            oab_printContentStart();
            
            //Print Page Content for CMS
            switch($PAGE['visibleTitle']) {
                case '404 - Page Not Found':
                    include("{$LOCAL_BASE_DOMAIN_ADDRESS}/404.php");
                    break;
                case '401 - Not Authorized':
                    include("{$LOCAL_BASE_DOMAIN_ADDRESS}/401.php");
                    break;
                default:
                    echo $PAGE['content'];
            }
            
            //Show Publish and Last Updated Date of page  
            if (!empty($PAGE['showPublishDate']) && $PAGE['showPublishDate'])
                echo "<p>First Published: {$PAGE['datePublished']}</p>";
            if (!empty($PAGE['showUpdateDate']) && $PAGE['showUpdateDate'])
                echo "<p>Last Updated: {$PAGE['lastUpdated']}</p>";
            
            oab_printContentEnd();
            ?>
        </div>
        
        <footer class="full-width-container centered-container footer-background">
            <?php oab_printFooter(); ?>
        </footer>
        
        <?php oab_printEnd(); ?>
    </body>   
</html>
<?php oab_endDatabaseConnection($mysqli); ?> 
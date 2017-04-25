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
                Pages
            </span>

            <span id="edit-page-link" class="backend-content-title-bar-link backend-content-title-bar-link-end to-edit-page-link" href=>
                <img class="backend-content-title-bar-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/EditPage_Icon.png" />
                edit
            </span>

            <span id="create-page-link" class="backend-content-title-bar-link to-new-page-link" href='<?php echo $BACKEND_DOMAIN_BASE; ?>/create-page.php'>
                <img class="backend-content-title-bar-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/NewPage.png" />
                new
            </span>

            <span id="delete-page-link" class="backend-content-title-bar-link delete-selected-pages-link">
                <img class="backend-content-title-bar-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Delete-Icon.png" />
                delete
            </span>
            
        </div>
        <script>
            $('#edit-page-link').click(function() {
                var pageID = $('.select-checkbox:checked').first().val(); //Assumes only one will be selected at a time
                
                if (pageID) {
                    location.replace('<?php echo $BACKEND_DOMAIN_BASE; ?>/edit-page.php?pageID=' + pageID);
                } else {
                    oab_updateMessage('You need to select a page to perform this operation');
                }
            });
            
            $('#create-page-link').click(function() {                
                location.replace('<?php echo $BACKEND_DOMAIN_BASE; ?>/create-page.php');
            });
        </script>
        
        <table border="1">
            <tr>
                <td></td>
                <td>page id</td>
                <td>visible title</td>
                <td>link title</td>
                <td>author id</td>
                <td>author name</td>
            </tr>
            
            <?php $pages = oabGetAllPages($mysqli);
                foreach ($pages as $page) {
            ?>
            <tr>
                <td><input type='checkbox' class='select-checkbox' value='<?php echo $page->getID();?>'></td>
                <td><?php echo $page->getID(); ?></td>
                <td><?php echo $page->getVisibleTitle(); ?></td>
                <td><?php echo $page->getLinkName(); ?></td>
                <td><?php echo $page->getAuthor()->getUserID() ?></td>
                <td><?php echo $page->getAuthor()->getName() ?></td>
            </tr>
            <?php } ?>
        </table>

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
        
        <script>
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'pages.php'); ?>';
            
            //Unchecks all other checkboxes when one is checked
            $.each(
                $('.select-checkbox'),
                function() {
                    $(this).change(function() {
                        $('.select-checkbox').not(this).attr('checked', false);
                    });
                }
            );

            //Delete pages on click of button
            $('#delete-page-link').click(function(){
                var pageID = $('.select-checkbox:checked').first().val(); //Assumes only one will be selected at a time
                
                $.post(
                    "<?php echo $BACKEND_DOMAIN_BASE; ?>/scripts/backendDeletePage.php",
                    {
                        'deletePageID': pageID,
                        'fileName': 'pages.php',
                        'nonceToken': nonceToken,
                        'pageID': null                            
                    },
                    function(data, success) {
                        oab_updateMessage(data.message);

                        nonceToken = data.newNonce;

                        //Reload page if successful
                        if (data.successful) {
                            console.debug(data);
                            //A trick supposed to allow the page to reload
                            location.reload(true);
                        }
                    }
                );
            })
        </script>
        
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
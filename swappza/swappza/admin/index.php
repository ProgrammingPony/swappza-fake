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
            <img class="backend-content-title-bar-icon" alt="dashboard-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Dashboard_Icon.png" />

            <span class="backend-content-title-bar-text">
                Dashboard
            </span>
        </div>

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
        
        <script>
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'index.php'); ?>';
        </script>
        
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
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
            <img class="backend-content-title-bar-icon" alt="users-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Users.png" />

            <span class="backend-content-title-bar-text">
                Users
            </span>               
        </div>
        
        <table border='1'>
            <tr>
                <td></td>
                <td>id</td>
                <td>username</td>
                <td>email</td>
                <td>name</td>
                <td>date registered</td>
                <td>role</td>
            </tr>

            <!--Script to retrieve info-->
            <?php 
            $users = oabGetAllUsers($mysqli, null, null);
            foreach ($users as $user) {
            ?>
                <tr>
                    <td><input type='checkbox' class='select-checkbox' value='<?php echo $user->getUserID(); ?>'></td>
                    <td><?php echo $user->getUserID(); ?></td>
                    <td><?php echo $user->getUsername(); ?></td>
                    <td><?php echo $user->getEmail(); ?></td>
                    <td><?php echo $user->getName(); ?></td>
                    <td><?php echo $user->getDateRegistered(); ?></td>
                    <td><?php echo oabConvertRoleToString($user->getRole()); ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
        
        <script>
            var nonceToken = '<?php echo oab_create_nonce($mysqli, null, 'users.php'); ?>';
        </script>
        
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
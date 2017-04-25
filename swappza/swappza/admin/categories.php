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
            <img class="backend-content-title-bar-icon" alt="categories-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Categories.png" />

            <span class="backend-content-title-bar-text">
                Categories
            </span>
        </div>
        
        <?php $categoryTree = oab_getSwappzaCategoryTree($mysqli); ?>

        <p>Categories</p>
        <table border="1">
            <tr>                        
                <td>id</td>
                <td>name</td>
            </tr>

            <?php foreach ($categoryTree as $category) { ?>
                <tr>
                    <td><?php echo $category->getID(); ?></td>
                    <td><?php echo $category->getName(); ?></td>
                </tr>
            <?php } ?>

        </table>
        
        <br>
        
        <p>SubCategories</p>
        <?php $subCategories = oabSwappzaGetAllSubCategories($mysqli); ?>
        <table border="1">
            <tr>
                <td>subcategory id</td>
                <td>name</td>
                <td>parent id</td>
            </tr>
            
            <?php foreach ($categoryTree as $category) { 
                    $subCategories = $category->getSubCategories();

                    foreach ($subCategories as $subCategory) {
            ?>
                        <tr>
                            <td><?php echo $subCategory->getID(); ?></td>
                            <td><?php echo $subCategory->getName(); ?></td>
                            <td><?php echo $category->getID(); ?></td>
                        </tr>
                <?php } ?>
                
            <?php } ?>
        </table>

        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/content-end.php"; ?>
        <?php require "{$ABSOLUTE_BACKEND_TEMPLATE_DIR}/concluding-scripts.php"; ?>
    </body>
</html>

<?php oab_endDatabaseConnection($mysqli); //End db connection ?>
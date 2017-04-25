<div id="backend-left-column">
    <a id="dashboard-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/index.php" class="backend-left-link-inactive default-left-link to-dashboard-link">
        <img alt="dashboard-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Dashboard_Icon.png">
        Dashboard
    </a>

    <a id="pages-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/pages.php" class="backend-left-link-inactive to-pages-link">
        <img alt="pages-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Pages.png">
        Pages
    </a>

    <a id="users-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/users.php" class="backend-left-link-inactive to-users-link">
        <img alt="users-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Users.png">
        Users
    </a>

    <a id="swaps-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/swaps.php" class="backend-left-link-inactive to-swaps-link">
        <img alt="swaps-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Swap.png">
        Swaps
    </a>

    <a id="categories-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/categories.php" class="backend-left-link-inactive to-categories-link">
        <img alt="categories-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Categories.png">
        Categories
    </a>

    <a id="locations-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/locations.php" class="backend-left-link-inactive to-locations-link">
        <img alt="locations-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Location.png">
        Locations
    </a>

    <a id="settings-link" href="<?php echo $BACKEND_DOMAIN_BASE; ?>/settings.php" class="backend-left-link-inactive to-settings-link">
        <img alt="settings-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Settings_Icon.png">
        Settings
    </a>

    <a id="frontend-link" href="<?php echo $PRODUCTION_DOMAIN_BASE; ?>/index.php" class="backend-left-link-inactive to-frontend-link">
        <img alt="frontend-icon" style="width:80px;height:80px;" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/Frontend_Icon.png">
        To Frontend
    </a>

    <span class="" style="display:block; margin-top:25px;">
        <img alt="user-icon" class="backend-left-link-icon" src="<?php echo $BACKEND_DOMAIN_BASE; ?>/images/User.png">
        <?php echo $_SESSION['u_username']; ?>
    </span>
</div>
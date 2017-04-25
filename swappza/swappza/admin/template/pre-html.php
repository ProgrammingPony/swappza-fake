<?php
if (empty($_SESSION['u_role']) || $_SESSION['u_role'] != 'Administrator') {
    header("HTTP/1.1 401 Unauthorized");
    require ("{$LOCAL_BASE_DOMAIN_ADDRESS}/401.php");
    exit;
}

//Establish database connection
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if (!is_object($mysqli)) {
    echo 'Failed to establish database connection for index.php . Page will not be displayed';
    exit();
}
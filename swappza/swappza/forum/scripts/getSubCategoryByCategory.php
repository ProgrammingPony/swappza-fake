<?php
session_start();

require_once '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
require_once "{$FRONTEND_SCRIPTS_LOCATION}/functions/autoload.php";
require_once "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/scripts/forum.php"; //This is required for forum model and functions

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
$mysqli = oab_getDatabaseConnection($DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

$categoryID = filter_input(INPUT_POST, 'categoryID');

$subCategories = array();
$successful = false;

if ($mysqli) { 
    $subCategories = oabEFGetAllSubCategoriesByCategory($mysqli, $categoryID);
    $successful = true;
    
    oab_endDatabaseConnection($mysqli);
}

header('Content-Type: application/json');
$json = json_encode( array(
    'successful' => $successful,
    'subCategories' => $subCategories
));

echo $json;
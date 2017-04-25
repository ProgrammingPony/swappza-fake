<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Return database connection given required information.
 * 
 * @param string $host
 * @param string $username
 * @param string $password
 * @param string $schema_name
 * @return mysqli if successful otherwise null returned
 */
function oab_getDatabaseConnection($host, $username, $password, $schema_name) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //For error details
    $mysqli = new mysqli($host, $username, $password, $schema_name);

    if ($mysqli->connect_errno) {
        error_log("Failed to establish connection to MySQL from frontend index.php: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
        return null;
    } else {
        return $mysqli;
    }
}

function oab_endDatabaseConnection($mysqli) {
    mysqli_close($mysqli);
}
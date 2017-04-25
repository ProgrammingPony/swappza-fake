<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
session_unset();

require '/home/dolphinlover1/public_html/omarabdelbari.com/swappza/properties.php';
header("Location: " . $PRODUCTION_DOMAIN_BASE);

?>
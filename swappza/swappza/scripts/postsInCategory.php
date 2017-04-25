<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function swappza_retrieve_posts_in_category ($con, $category_name, $isAsk) {
    if (is_null($con) || is_null($category_name) || is_null($isAsk)) {
        error_log("Attempt to use swappza_retrieve_posts_in_category function with invalid parameters.<BR>");
        return NULL;
    }
    
    
    
}

?>
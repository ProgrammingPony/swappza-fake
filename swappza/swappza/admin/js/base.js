/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Security Functionality
 * 
 */
var nonceToken = '';
var PAGE_ID = null;

function oabUpdateNonceToken(token) {
    nonceToken = token;
}

/*
 * Message element and functionality
 */
var oab_backendMessageContainer = $('#backend-message');

function oab_updateMessage(message) {
    oab_backendMessageContainer.html(message);
    oab_backendMessageContainer.show();
}

//Left Menu Buttons Click event
$(".backend-left-link-inactive").click( function() {
    //Make currently active link inactive
    $(".backend-left-link-active").removeClass("backend-left-link-active").addClass("backend-left-link-inactive");

    //Make click link active
    $(this).removeClass("backend-left-link-inactive").addClass("backend-left-link-active");

    //changeContent( $(this).attr('id') );
});

//Show dashboard as default
$(".default-left-link").trigger("click");
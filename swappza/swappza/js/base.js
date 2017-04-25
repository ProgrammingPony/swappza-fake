/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Display notifications to user as inner html of this element
var oab_messageElement = $('#message-container');
var oab_messageContainer = $('#message-section');

oab_selectedLocation = '';
oab_selectedCityID = '';
oab_selectedProvinceID = '';
oab_selectedNationID = '';

function oabUpdateNonceToken(token) {
    nonceToken = token;
}

function oab_updateMessage(message) {
    oab_messageElement.html(message);
    oab_messageContainer.change();
}

//Visual effects on template
$('#lower-header-profile-container').hover( 
    function () { $('#swappza-template-profile-icon').attr('src', TEMPLATE_BASE + '/images/profile_picture_white.png') },
    function () { $('#swappza-template-profile-icon').attr('src', TEMPLATE_BASE + '/images/profile_picture_black.png') }
);

//Lower Header Social Icon Hover Effect
$('#lower-header-facebook-social-link').hover(
    function () { $('#lower-header-facebook-social-icon').attr('src', TEMPLATE_BASE + '/images/FaceBook-Icon-hover.png') },
    function () { $('#lower-header-facebook-social-icon').attr('src', TEMPLATE_BASE + '/images/FaceBook-Icon.png') }        
);

$('#lower-header-twitter-social-link').hover(
    function () { $('#lower-header-twitter-social-icon').attr('src', TEMPLATE_BASE + '/images/Twitter-hover.png') },
    function () { $('#lower-header-twitter-social-icon').attr('src', TEMPLATE_BASE + '/images/Twitter.png') }        
);

//Top Location Box Autocomplete feature
function oab_getLocationsCallback(response) {
    //Update message if not empty
    if (response.message !== '') {
        oab_updateMessage(response.message);
    }
    
    if (response.newNonce) {
        oabUpdateNonceToken(response.newNonce);
    }

    //New autocompmlete options
    $('.location-autocomplete-option').detach();

    var i;
    for (i=0; i<response.locations.length; i++) {                
        $('#top-location-container').append(
            '<span class=\"location-autocomplete-option\"'
            + 'cityID=\"' + response.locations[i].city.id
            + '\" provinceID=\"' + response.locations[i].province.id
            + '\" nationID=\"' + response.locations[i].nation.id
            + '\" cityName=\"' + response.locations[i].city.name
            + '\" provinceName=\"' + response.locations[i].province.name
            + '\" nationName=\"' + response.locations[i].nation.name
            + '\">'
            + response.locations[i].city.name + ', '
            + response.locations[i].province.name + ', '
            + response.locations[i].nation.name
            + '</span>'     
        );
    }
       
    $('.location-autocomplete-option').hover(
        function() {
            $(this).css('background', '#cccccc');
        },
        function() {
            $(this).css('background', '#ffffff');
        }
    );
    
    $('.location-autocomplete-option').click(function() {
        oab_selectedLocation = $(this).attr('cityname') + ', ' + $(this).attr('provincename') + ', ' + $(this).attr('nationname');
        oab_selectedCityID = $(this).attr('cityid');
        oab_selectedNationID = $(this).attr('nationid');
        oab_selectedProvinceID = $(this).attr('provinceid');

        oab_setLocation(
            oab_selectedCityID,
            oab_selectedProvinceID,
            oab_selectedNationID,
            oab_setLocationCallback
        );
    });
}

$('#top-location-field').keyup(function() {
    cityFilter = $('#top-location-field').val();
    $('.location-autocomplete-option').detach();

    if ( cityFilter ) {                  
        oab_getLocations(
            cityFilter,
            oab_getLocationsCallback
        );
    }
});

$('#top-location-field').focus( function() {
    $(this).val('');
});

function oab_setLocationCallback(response) {
    if (response.message === '') {
        var topLocationField = $('#top-location-field');
        topLocationField.attr('placeholder',  oab_selectedLocation );
        topLocationField.val('');
    } else {
        oab_updateMessage(response.message);
    }
    
    if (response.newNonce) {
        oabUpdateNonceToken(response.newNonce);
    }
    
    $('.location-autocomplete-option').detach();
}
       
//If message section isnt empty, display it to the user
oab_messageContainer.change( function() {
    if ( oab_messageElement.html().trim() ) {
        oab_messageContainer.slideDown();
    } else {
        oab_messageContainer.slideUp();
    }
});

if (oab_messageElement.html().trim()) {
    oab_messageContainer.css('display','block');
}
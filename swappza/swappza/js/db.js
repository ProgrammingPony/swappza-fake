/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function oab_authenticateUser (nonceToken, userORemail, password, pageID, fileName, callback) {
    oab_updateMessage('');

    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response = jQuery.parseJSON(xmlhttp.responseText);
            
            callback(response);
        }
    };
    
    params = 'userORemail=' + userORemail + '&nonceToken=' + nonceToken + '&password=' + password + '&pageID=' + pageID + '&fileName=' + fileName;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/login.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}

function oab_createNewUser (nonceToken, username, email, password, name, city, birthday, phone, address, pageID, fileName, callback) {
    oab_updateMessage('');

    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response = jQuery.parseJSON(xmlhttp.responseText);
            if (callback !== null) {
                callback(response);
            }
        }
    };
    
    params = 'username=' + username + '&nonceToken=' + nonceToken + '&password=' + password + '&pageID=' + pageID + '&email=' + email + '&name=' + name + '&city=' + city + '&birthday=' + birthday + '&address=' + address + '&fileName=' + fileName;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/signup.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}

function oab_assignUsername (nonceToken, email, username, pageID, fileName, password, callback) {
    oab_updateMessage('');
    
    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response = jQuery.parseJSON(xmlhttp.responseText);
            
            if (callback != null) {
                callback(response);
            }
        }
    };
    
    params = 'email=' + email + '&nonceToken=' + nonceToken + '&password=' + password + '&pageID=' + pageID + '&fileName=' + fileName + '&username=' + username;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/assign-username.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}

function oab_updateUserProfile (nonceToken, email, username, address, phone, oldPassword, newPassword, pageID, fileName, callback) {
    oab_updateMessage('');
    
    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            response = jQuery.parseJSON(xmlhttp.responseText);
            if (callback) {
                callback(response);
            }
        }
    };

    params = 'username=' + username + '&address=' + address + '&phone=' + phone + '&nonceToken=' + nonceToken + '&oldPassword=' + oldPassword + '&newPassword=' + newPassword + '&pageID=' + pageID + '&email=' + email + '&fileName=' + fileName;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/update-profile.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}

function oab_getLocations(cityFilter, callback) {
    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {            
            response = jQuery.parseJSON(xmlhttp.responseText);
            
            if (callback != null) {
                callback(response);
            }
        }
    };

    params = 'cityFilter=' + cityFilter;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/get-city-list.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);    
}

function oab_setLocation(cityID, provinceID, nationID, callback) {
    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            response = jQuery.parseJSON(xmlhttp.responseText);
            
            if (callback !== null) {
                callback(response);
            }
        }
    };

    params = 'cityID=' + cityID + '&provinceID=' + provinceID + '&nationID=' + nationID;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/set-location.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}

function oab_getForumPosts(offset, quantity, subcategoryID, filter) {
    if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            response = jQuery.parseJSON(xmlhttp.responseText);
            
            if (callback !== null) {
                callback(response);
            }
        }
    };

    params = 'offset=' + offset + '&quantity=' + quantity + '&subcategoryID=' + subcategoryID + '&filter=' + filter;
    
    xmlhttp.open('POST', SCRIPT_FOLDER_PATH + '/get-forum-posts.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);    
}
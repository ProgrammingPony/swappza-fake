<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$NONCE_TOKEN_LENGTH = 24;
$NONCE_SALT_LENGTH = 32;
$NONCE_HASH_LENGTH = 128;
$NONCE_HASH_ALG = 'sha512';
$NONCE_EXPIRY_DELAY = 7200; //2 Hours

/**
 * Provides a one way hash for the nonce token, the user's id, the user's email, salt, and filename. 
 * 
 * @global string $NONCE_HASH_ALG
 * @param type $salt
 * @param type $nonce
 * @return string
 */
function oab_hashNonce($salt, $nonce, $fileName) {
    global $NONCE_HASH_ALG;
    
    $userID = isset($_SESSION['u_ID']) && is_numeric($_SESSION['u_ID']) ? $_SESSION['u_ID'] :'guest';
    $email = (isset($_SESSION['u_email']) && !empty($_SESSION['u_email']) ? $_SESSION['u_email'] : 'none' );    
    $hash = hash($NONCE_HASH_ALG, $salt . $userID . $email . $nonce . $fileName);
    
    return $hash;
}

/**
 * Creates a nonce for a user and stores it in the database, returns nonce token if successful, '' (empty string) if failed
 * 
 * @global int $NONCE_TOKEN_LENGTH
 * @global int $NONCE_SALT_LENGTH
 * @global int $NONCE_EXPIRY_DELAY
 * @param string $dbcon
 * @param int $pageID
 * @param string $fileName
 * 
 * @return string 
 */
function oab_create_nonce($dbcon, $pageID, $fileName) {
    global $NONCE_TOKEN_LENGTH, $NONCE_SALT_LENGTH, $NONCE_EXPIRY_DELAY;
        
    //create nonce
    $strong = false;
    while (!$strong) {
        $nonce =  bin2hex(openssl_random_pseudo_bytes($NONCE_TOKEN_LENGTH, $strong));
    }      

    //create salt
    $strong = false;
    
    while (!$strong) {
        $salt = openssl_random_pseudo_bytes($NONCE_SALT_LENGTH, $strong);
    }

    //create hash
    $hash = oab_hashNonce($salt, $nonce, $fileName);
  
    //Enter into database
    //Assumption here that its so unlikely that the hash will clash with an existing one
    $expiry = date("F d Y H:i:s", time() + $NONCE_EXPIRY_DELAY);
    $successful = false;
    
    $userID = empty($_SESSION['u_ID']) ? null : $_SESSION['u_ID'];

    $stmt = $dbcon->prepare("INSERT INTO Nonces (userID,pageID,nonceHash,expiry,salt,fileName) VALUES (?,?,?,?,?,?)");
    
    if ($stmt) {
        $stmt->bind_param('ddssss', $userID, $pageID, $hash, $expiry, $salt, $fileName);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
           $successful = true;
        }
        
        $stmt->close();
    }
    
    if ($successful) {        
        return $nonce;
    } else {
        return '';
    }
}

/**
 * Returns a new nonce if nonce was verified and false otherwise
 * 
 * @param mysqli $dbcon
 * @param int $pageID
 * @param string $nonce
 * @param string $fileName
 * @return new string with nonce if valid nonce was provided, false returned otherwise
 */
function oab_verify_nonce ($dbcon, $pageID, $nonce, $fileName) {
    $valid = false;

    $validUserID = isset($_SESSION['u_ID']) && is_numeric($_SESSION['u_ID']);
    $validPageID = isset($pageID) && is_numeric($pageID);
    
    $currentTime = date("F d Y H:i:s", time());
    
    //Retrieve content from database userID is null for guests
    if ($validUserID && $validPageID) {
        $stmt = $dbcon->prepare("SELECT nonceID,nonceHash,salt FROM Nonces WHERE userID=? AND pageID=? AND fileName=? AND expiry>?");
    } elseif ($validPageID) {
        $stmt = $dbcon->prepare("SELECT nonceID,nonceHash,salt FROM Nonces WHERE userID IS NULL AND pageID=? AND fileName=? AND expiry>?");
    } elseif ($validUserID) {
        $stmt = $dbcon->prepare("SELECT nonceID,nonceHash,salt FROM Nonces WHERE userID=? AND pageID IS NULL AND fileName=? AND expiry>?");
    } else {
        $stmt = $dbcon->prepare("SELECT nonceID,nonceHash,salt FROM Nonces WHERE userID IS NULL AND pageID IS NULL AND fileName=? AND expiry>?");
    }

    if ($stmt) {
        if ($validUserID && $validPageID) {
            $stmt->bind_param('ddss', $_SESSION['u_ID'], $pageID, $fileName, $currentTime);
        } elseif ($validPageID) {
            $stmt->bind_param('dss', $pageID, $fileName, $currentTime);
        } elseif ($validUserID) {
            $stmt->bind_param('dss', $_SESSION['u_ID'], $fileName, $currentTime);
        } else {
            $stmt->bind_param('ss', $fileName, $currentTime);
        }
        
        $stmt->execute();
        $stmt->bind_result($nonceID, $nonceHash, $salt);

        while ($stmt->fetch()) {
            //If not expired then check if its valid
            if (strcmp($nonceHash, oab_hashNonce($salt, $nonce, $fileName)) == 0) {
                $valid = true;
            }
        } 
        $stmt->close();
    }
    
    //Delete token and if successful then return new nonce token
    if ($valid) {
        if (oab_delete_nonce($dbcon, $nonceID)) {
            return oab_create_nonce($dbcon, $pageID, $fileName);
        } else {
            error_log("Failed to delete nonce token");
        }  
    }
    
    return false;
}

/**
 * Return true if nonce was found and deleted corresponding to provided id
 * 
 * @param mysqli $dbcon
 * @param int $nonceID
 * @return boolean
 */
function oab_delete_nonce ($dbcon, $nonceID) {
    $successful = false;
    
    $stmt = $dbcon->prepare("DELETE FROM Nonces WHERE nonceID=?");

    if ($stmt) {
        $stmt->bind_param('d', $nonceID);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            $successful = true;
        }
    }
    
    return $successful;   
}

?>
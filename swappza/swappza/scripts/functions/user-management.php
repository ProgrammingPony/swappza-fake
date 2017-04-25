<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$OAB_MAXIMUM_USERNAME_LENGTH = 20;
$OAB_MAXIMUM_EMAIL_LENGTH = 255;
$OAB_MAXIMUM_NAME_LENGTH = 255;
$OAB_MAXIMUM_PHONE_LENGTH = 15;
$OAB_MAXIMUM_ADDRESS_LENGTH = 255;

/**
 * Enumerated Type to help specify login type
 */
abstract class oabUserIdType {
    const _DEFAULT = 'Default';
    const GOOGLE = 'Google';
    const FACEBOOK = 'Facebook';
}

/**
 * Enumerated type defining the basic roles in the system
 */
abstract class oabRole {
    const ADMINISTRATOR = 'Administrator';
    const COMMONER = 'Commoner';
    const _DEFAULT = 'Commoner';
    
    //TODO: Think of a more scalable way to add module roles like forum for the future
}

/**
 * Represents user as stored in database. Once constructor is used fields may not change.
 */
class oabUser implements JsonSerializable {
    private $username;
    private $name;
    private $userID;
    private $email;
    private $dateRegistered;
    private $location; //oabUser object
    private $birthDate;
    private $lastOnline;
    private $phone;
    private $role;
    
    /**
     * Creates an immutable location object.
     * 
     * @param string $username
     * @param string $name
     * @param long $userID
     * @param string $email
     * @param datetime $dateRegistered
     * @param oabLocation $location
     * @param datetime $birthDate
     * @param datetime $lastOnline
     * @param string $phone
     * @param oabRole $role
     */
    public function oabUser($username, $name, $userID, $email, $dateRegistered, $location, $birthDate, $lastOnline, $phone, $role) {
        $this->username = $username;
        $this->name = $name;
        $this->userID = $userID;
        $this->email = $email;
        $this->dateRegistered = $dateRegistered;
        $this->location = $location;
        $this->birthDate = $birthDate;
        $this->lastOnline = $lastOnline;
        $this->phone = $phone;
        $this->role = $role;
    }
    
    /**
     * Return date of birth in MySQL datetime format
     * 
     * @return datetime
     */
    public function getDateOfBirth() {
        return $this->birthDate;
    }
    
    /**
     * Returns date user was first registered in MySQL datetime format
     * 
     * @return datetime
     */
    public function getDateRegistered() {
        return $this->dateRegistered;
    }
    
    /**
     * Returns email of the user
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * Returns datetime user last logged in in MySQL datetime format
     * 
     * @return datetime
     */
    public function getLastLogin() {
        return $this->lastOnline;
    }
    
    /**
     * Returns the user's location
     * 
     * @return oabLocation
     */
    public function getLocation() {
        return $this->location;
    }
    
    /**
     * Returns full name of user
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Returns phone number of user as a string with only numbers
     * 
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    
    /**
     * Return role of user
     * 
     * @return string
     */
    public function getRole() {
        return $this->role;
    }
    
    /**
     * Returns id of user
     * 
     * @return long
     */
    public function getUserID() {
        return $this->userID;
    }
    
    /**
     * Return username of user
     * 
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

/**
 * Return true if credentials provided match database, false otherwise
 * DEVELOPER NOTE: This can be made more faster by just letting database query do all the work 
 * @param mysqli $dbcon
 * @param string $userORemail
 * @param string $password
 * @return boolean
 */
function oab_isValidCredentials ($dbcon, $userORemail, $password) {
    $validCredentials = true;
    
    $stmt = $dbcon->prepare("SELECT passwordHash FROM Users JOIN Password2015 ON Password2015.userID = Users.userID WHERE username=? OR email=?");
    
    if ($stmt) {
        $stmt->bind_param('ss', $userORemail, $userORemail);
        $stmt->execute();
        $stmt->bind_result($passwordHash);

        if ($stmt->fetch()) {
            //check hash
            if (!password_verify($password, $passwordHash)) {
                $validCredentials = false;
            }
        //This means username was not found in database    
        } else {
            $validCredentials = false;
        }
        
        $stmt->close();
    }
    
    return $validCredentials;
}

/**
 * Returns true if username exists and false otherwise
 * 
 * @param mysqli $dbcon
 * @param string $username
 * @return boolean
 */
function oab_isExistingUsername ($dbcon, $username) {
    $usernameExists = false;
    
    $stmt = $dbcon->prepare("SELECT username FROM Users WHERE username=?");
    
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($r_username);
        
        if ($stmt->fetch()) {
            $usernameExists = true;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement from oab_isExistingUsername in user-management.php');
    }
    
    return $usernameExists;
}

/**
 * Update the username of the user which has the specified userID. Returns true if operation successful, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param long $userID
 * @param string $username
 * @return boolean
 */
function oab_updateUsername ($dbcon, $userID, $username) {
    $isSuccessful = true;
    
    $stmt = $dbcon->prepare("UPDATE Users SET username=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $username, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows != 1) {
            $isSuccessful = false;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement for oab_updateUsername in user-management.php');
        $isSuccessful = false;
    }
    
    return $isSuccessful;
}

/**
 * Update the address of the user which has the specified userID. Returns true if operation successful, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param long $userID
 * @param string $address
 * @return boolean
 */
function oab_updateAddress ($dbcon, $userID, $address) {
    $isSuccessful = true;
    
    $stmt = $dbcon->prepare("UPDATE Users SET address=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $address, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows != 1) {
            $isSuccessful = false;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement for oab_updateAddress in user-management.php');
        $isSuccessful = false;
    }
    
    return $isSuccessful;
}

/**
 * Update the phone of the user which has the specified userID. Returns true if operation successful, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param long $userID
 * @param string $phone
 * @return boolean
 */
function oab_updatePhone ($dbcon, $userID, $phone) {
    $isSuccessful = true;
    
    $stmt = $dbcon->prepare("UPDATE Users SET phone=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $phone, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows != 1) {
            $isSuccessful = false;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement for oab_updatePhone in user-management.php');
        $isSuccessful = false;
    }
    
    return $isSuccessful;
}

/**
 * Update the password of the user which has the specified userID. Returns true if operation successful, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param long $userID
 * @param string $password
 * @return boolean
 */
function oab_updatePassword ($dbcon, $userID, $password) {
    $isSuccessful = true;
    
    $passwordHash = oab_hashPassword($password);
    
    $stmt = $dbcon->prepare("UPDATE Password2015 SET passwordHash=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $passwordHash, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows != 1) {
            $isSuccessful = false;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement for oab_updatePassword in user-management.php');
        $isSuccessful = false;
    }
    
    return $isSuccessful;
}

/**
 * Update the email of the user which has the specified userID. Returns true if operation successful, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param long $userID
 * @param string $email
 * @return boolean
 */
function oab_updateEmail ($dbcon, $userID, $email) {
    $isSuccessful = true;
    
    $stmt = $dbcon->prepare("UPDATE Users SET email=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $email, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows != 1) {
            $isSuccessful = false;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement for oab_updateEmail in user-management.php');
        $isSuccessful = false;
    }
    
    return $isSuccessful;
}

/**
 * Returns the hash of the specified password
 * 
 * @param string $password
 * @return string
 */
function oab_hashPassword ($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Returns user profile information if user exists and null otherwise.
 * 
 *  
 * @param mysqli $dbcon
 * @param int $userID
 * @param oabUserIdType $idType
 * @return oabUser
 */
function oab_getUserProfile ($dbcon, $userID, $idType) {
    $successful = false;
    
    switch($idType) {
        case oabUserIdType::_DEFAULT:
            $stmt = $dbcon->prepare("SELECT username,name,email,dateRegistered,city,birthDate,lastOnline,phone,role"
                    . " FROM Users"
                    . " WHERE userID=?");
            $user = null;

            if ($stmt) {
                $stmt->bind_param('d', $userID);
                $stmt->execute();
                $stmt->bind_result($username, $name, $email, $dateRegistered, $city, $birthDate, $lastOnline, $phone, $role);

                if ($stmt->fetch()) {
                    $city = new oabCity(null, $city);
                    $province = new oabProvince(null, null);
                    $nation = new oabNation(null, null);
                    $location = new oabLocation($city, $province, $nation, null, null);
                    $user = new oabUser($username, $name, $userID, $email, $dateRegistered, $location, $birthDate, $lastOnline, $phone, $role);
                    $successful = true;
                }

                $stmt->close();
            }
            break;
            
        case oabUserIdType::FACEBOOK:
            $stmt = $dbcon->prepare("SELECT Users.userID,username,name,email,dateRegistered,city,birthDate,lastOnline,phone,role"
                . " FROM FacebookUsers"
                . " JOIN Users ON Users.userID=FacebookUsers.userID"
                . " WHERE FacebookUsers.facebookID=?");

            if ($stmt) {
                
                $stmt->bind_param('s', $userID);
                $stmt->execute();
                $stmt->bind_result($uuserID, $username, $name, $email, $dateRegistered, $cityName, $birthDate, $lastOnline, $phone, $role);

                if ($stmt->fetch()) {
                    //Location is not done in full this is necessary implementation for this project
                    $city = new oabCity(null, $cityName);
                    $location = new oabLocation($city, null, null, null, null);
                    $user = new oabUser($username, $name, $uuserID, $email, $dateRegistered, $location, $birthDate, $lastOnline, $phone, $role);
                    
                    $successful = true;
                }

                $stmt->close();
            }
            break;
        
        case oabUserIdType::GOOGLE:
            $stmt = $dbcon->prepare("SELECT Users.userID,username,name,email,dateRegistered,city,birthDate,lastOnline,phone,role"
                . " FROM GoogleUsers"
                . " JOIN Users ON Users.userID=GoogleUsers.userID"
                . " WHERE googleID=?");
            
            if ($stmt) {
                $stmt->bind_param('s', $userID);
                $stmt->execute();
                $stmt->bind_result($uuserID, $username, $name, $email, $dateRegistered, $location, $birthDate, $lastOnline, $phone, $role);

                if ($stmt->fetch()) {
                    $user = new oabUser($username, $name, $uuserID, $email, $dateRegistered, $location, $birthDate, $lastOnline, $phone, $role);
                    $successful = true;
                }

                $stmt->close();
            }
            
            break;
    }
    
    if ($successful) {
        return $user;
    } else { 
        return false;
    }
}

/**
 * Creates new user. Return true if successful. False otherwise.
 * 
 * @param mysqli $dbcon
 * @param oabUser $userProfile
 * 
 * @return boolean
 */
function oab_createNewUser ($dbcon, $userProfile) {
    if (!is_a($userProfile, 'oabUser')) {
        return false;
    }
    
    $successful = false;
    
    //Get Values
    $email = $userProfile->getEmail();
    $email = isset($email) && !empty($email) ? $email : null;
    
    $name = $userProfile->getName();
    $name = isset($name) && !empty($name) ? $name : null;
    
    $username = $userProfile->getUsername();
    $username = isset($username) && !empty($username) ? $username : null;
    
    //In the future may change to City ID but thats not practical right now
    $cityName = $userProfile->getLocation()->getCity()->getName();
    $cityName = isset($cityID) && !empty($cityID) ? $cityID : null;
    
    $DOB = $userProfile->getDateOfBirth();
    $DOB = isset($DOB) && !empty($DOB) ? $DOB : null;
    
    $phone = $userProfile->getPhone();
    $phone = isset($phone) && !empty($phone) ? $phone : null;
    
    $address = $userProfile->getLocation()->getAddress();
    $address = isset($address) && !empty($address) ? $address : null;
    
    $role = $userProfile->getRole();
    $role = isset($role) && !empty($role) ? $role : oabRole::_DEFAULT;
    
    //Insert Into Database
    $stmt = $dbcon->prepare("INSERT INTO Users (email, name, username, city, birthDate, phone, address, role, dateRegistered) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    
    if ($stmt) {
        $stmt->bind_param(
                'sssdssss',
                $email,
                $name,
                $username,
                $cityName,
                $DOB,
                $phone,
                $address,
                $role
        );
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            $successful = true;
        }
        
        $stmt->close();
    }
    
    return $successful;
}

/**
 * Return false only if email is found not correspond to any user in the base.
 * Otherwise returns true.
 * 
 * @param mysqli $dbcon
 * @param string $email
 * 
 * @return boolean
 */
function oab_isExistingEmail($dbcon, $email) {
    $isDuplicate = true;
            
    $stmt = $dbcon->prepare("SELECT email from Users WHERE email=?");
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($r_email);

        if (!$stmt->fetch()) {
            $isDuplicate = false;
        }
        
        $stmt->close();
    }
    
    return $isDuplicate;
}

/**
 * Returns userID corresponding to provided email or username if exists and returns false otherwise
 * 
 * @param mysqli $dbcon
 * @param string $userOrEmail
 * 
 * @return int $userID
 */
function oab_getUserID($dbcon, $userOrEmail) {
    $successful = false;
    
    $stmt = $dbcon->prepare("SELECT userID FROM Users WHERE email=? OR username=?");
    
    if ($stmt) {
        $stmt->bind_param('ss', $userOrEmail, $userOrEmail);        
        $stmt->execute();
        $stmt->bind_result($r_userID);
        
        //WARNING: this assumes that email and username is correctly formatted otherwise multiple results may exist
        if ($stmt->fetch()) {
            $userID = $r_userID;
            $successful = true;
        }
        
        $stmt->close();
    }   
    
    if ($successful) {
        return $userID;
    } else {
        return false;
    }
}

function oab_setFirstPassword($dbcon, $userID, $password) {
    $isSuccessful = true;
    
    $passwordHash = oab_hashPassword($password);
    
    $stmt = $dbcon->prepare("INSERT INTO Password2015 (passwordHash,userID) VALUES (?,?)");
    
    if ($stmt) {
        $stmt->bind_param('sd', $passwordHash, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            $isSuccessful = true;
        }
        
        $stmt->close();
    }
    
    return $isSuccessful;
}

/**
 * Sets the last login of the user as the current time. Returns true if successful
 * and false otherwise.
 * 
 * @param mysqli $dbcon
 * @param int $userID
 *
 * @return boolean
 */
function oab_updateLastLogin($dbcon, $userID) {
    $successful = false;
    
    $stmt = $dbcon->prepare("UPDATE Users SET lastOnline=CURRENT_TIMESTAMP WHERE userID=?");

    if ($stmt) {
        $stmt->bind_param('d', $userID);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            $successful = true;
        } 

        $stmt->close();
    }

    return $successful;
    
}

/**
 * Stores external account information (Facebook, Google, etc) associated with
 *  an existing user account. Returns true if successful and false otherwise.
 * 
 * @param mysqli $dbcon
 * @param int $userID
 * @param string $externalID
 * @param oabUserIdType $idType
 * 
 * @return boolean
 */
function oab_addExternalAccount($dbcon, $userID, $externalID, $idType) {
    $successful = false;
    
    switch($idType) {
        case oabUserIdType::FACEBOOK:
            $stmt = $dbcon->prepare("INSERT INTO FacebookUsers (userID,facebookID)"
                    . " VALUES (?,?)");

            if ($stmt) {
                $stmt->bind_param('ds', $userID, $externalID);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $successful = true;
                }

                $stmt->close();
            }
            break;
        
        case oabUserIdType::GOOGLE:
            $stmt = $dbcon->prepare("INSERT INTO GoogleUsers (userID,googleID)"
                    . " VALUES (?,?)");

            if ($stmt) {
                $stmt->bind_param('ds', $userID, $externalID);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $successful = true;
                }

                $stmt->close();
            }
            break;
        
        //List of invalid options
        case oabUserIdType::_DEFAULT:
            throw new Exception('Cannot use default idType for oab_addExternalAccount from user-management.php');
            break;
    }
    
    return $successful;
}

/**
 * Set user session variables given user profile and updates last login time
 * 
 * @param \oabUser $userProfile
 */
function oab_setUserSession($dbcon, $userProfile) {
    $userID = $userProfile->getUserID();
    
    $_SESSION['u_ID'] = $userID;
    $_SESSION['u_email'] = $userProfile->getEmail();
    $_SESSION['u_username'] = $userProfile->getUsername();
    $_SESSION['u_name'] = $userProfile->getName();
    $_SESSION['u_role'] = $userProfile->getRole();
    $_SESSION['u_DOB'] = $userProfile->getDateOfBirth();
    $_SESSION['u_dateOfRegistration'] = $userProfile->getDateRegistered();
    $_SESSION['u_lastLogin'] = $userProfile->getLastLogin();
    $_SESSION['u_address'] = $userProfile->getLocation()->getAddress(); //TODO
    
    oab_updateLastLogin($dbcon, $userID);
}

/**
 * Assigns a user's first username using their email
 * 
 * @param mysqli $dbcon
 * @param string $username
 * @param string email
 * 
 * @return boolean
 */
function oab_assignUsername($dbcon, $userID, $username) {
    $successful = false;
    
    $stmt = $dbcon->prepare("UPDATE Users SET username=? WHERE userID=?");
    
    if ($stmt) {
        $stmt->bind_param('sd', $username, $userID);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1) {
            $successful = true;
        }
    }
    
    return $successful;
}

/**
 * Returns true if email corresponding to user's id matches the one provided
 * and returns false if an error occurs or emails do not match.
 * 
 * @param mysqli $dbcon
 * @param int $userID
 * @param string $email
 * 
 * @return boolean
 */
function oab_isUsersEmail($dbcon, $userID, $email) {
    $isUsersEmail = false;
    
    $stmt = $dbcon->prepare("SELECT userID FROM Users WHERE userID=? && email=?");
    
    if ($stmt) {
        $stmt->bind_param('ds', $userID, $email);
        $stmt->execute();
        $stmt->bind_result($r_userID);
        
        if ($stmt->fetch()) {
            $isUsersEmail = true;
        }
    }
    
    return $isUsersEmail;
}

/**
 * Returns true if username corresponding to user's id matches the one provided
 * and returns false if an error occurs or usernames do not match.
 * 
 * @param mysqli $dbcon
 * @param int $userID
 * @param string $username
 * 
 * @return boolean
 */
function oab_isUsersUsername($dbcon, $userID, $username) {
    $isUsersEmail = false;
    
    $stmt = $dbcon->prepare("SELECT userID FROM Users WHERE userID=? && username=?");
    
    if ($stmt) {
        $stmt->bind_param('ds', $userID, $username);
        $stmt->execute();
        $stmt->bind_result($r_userID);
        
        if ($stmt->fetch()) {
            $isUsersEmail = true;
        }
        
        $stmt->close();
    }
    
    return $isUsersEmail;
}

/**
 * Retrieves all registered users in the database if successful, otherwise false is returned.
 * Filters have not been applied yet.
 * @param mysqli $dbcon
 * @return boolean|oabUser[]
 * @throws Exception
 */
function oabGetAllUsers($dbcon, $nameFilter, $idFilter) {
    $users = array();
    
    try{
        $stmt = $dbcon->prepare("SELECT userID, username, email, dateRegistered, name, city, birthDate, lastOnline, phone, address, role"
                . " FROM Users"
                . " WHERE ?");
        
        if ($stmt) {
            $one = 1;
            $stmt->bind_param('d', $one);
            $stmt->execute();
            $stmt->bind_result($userID, $username, $email, $dateRegistered, $name, $city, $birthDate, $lastOnline, $phone, $address, $role);
            
            while ($stmt->fetch()) {
                array_push(
                    $users,
                    new oabUser($username, $name, $userID, $email, $dateRegistered, $city, $birthDate, $lastOnline, $phone, $role)
                );
            }
            
            $stmt->close();
            
        } else {
            throw new Exception('Failed to prepare statement');
        }
        
        return $users;
        
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}


function oabConvertRoleToString ($role) {
    switch($role) {
        case oabRole::_DEFAULT: case oabRole::COMMONER:
            return "Commoner";
            break;
        case oabRole::ADMINISTRATOR:
            return "Administrator";
            break;
    }
}
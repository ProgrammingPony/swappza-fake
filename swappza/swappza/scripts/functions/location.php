<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Enumerated type
 * REGION = 0;
 * NATION = 1;
 * PROVINCE = 2;
 * CITY = 3;
 */
abstract class LocationType {
    const REGION = 0;
    const NATION = 1;
    const PROVINCE = 2;
    const CITY = 3;
}

class oabRegion implements JsonSerializable {
    private $id;
    private $cities;
    private $provinces;
    private $nations;
    
    /**
     * Creates immutable oabRegion object. Only one of $cities,
     * $provinces or $nations should be not null. This design 
     * is meant for flexibility when setting up regions.
     * 
     * By default database design assumes regions consists of cities.
     * 
     * @param int $regionID
     * @param oabCity[] $cities
     * @param oabProvince[] $provinces
     * @param oabNation[] $nations
     */
    function oabRegion($regionID, $cities, $provinces, $nations) {
        $this->id = $regionID;
        $this->cities = $cities;
        $this->nations = $nations;
        $this->provinces = $provinces;
    }
    
    /**
     * 
     * @return int
     */
    public function getID() {
        return $this->id;
    }
    
    /**
     * 
     * @return oabCity[]
     */
    public function getCities() {
        return $this->cities;
    }
    
    /**
     * 
     * @return oabProvince[]
     */
    public function getProvinces() {
        return $this->provinces;
    }
    
    /**
     * 
     * @return oabNation[]
     */
    public function getNations() {
        return $this->nations;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabNation implements JsonSerializable {
    private $id;
    private $name;
    
    /**
     * Create immutable oabNation object
     * 
     * @param int $nationID
     * @param string $nationName
     */
    function oabNation ($nationID, $nationName) {
        $this->id = $nationID;
        $this->name = $nationName;
    }
    
    /**
     * 
     * @return int
     */
    public function getID() {
        return $this->id;
    }
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabProvince implements JsonSerializable {
    private $id;
    private $name;
    
    /**
     * Creates province object with specified information, may not be modified after
     * 
     * @param int $provinceID
     * @param string $provinceName
     */
    function oabProvince($provinceID, $provinceName) {
        $this->id = $provinceID;
        $this->name = $provinceName;
    }
    
    /**
     * Returns province ID
     * 
     * @return int
     */
    public function getID() {
        return $this->id;
    }
    
    /**
     * Returns name of province
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabCity implements JsonSerializable{
    private $id;
    private $name;
    
    /**
     * Creates immutable oabCity object
     * 
     * @param int $cityID
     * @param string $cityName
     */
    function oabCity($cityID, $cityName) {
        $this->id = $cityID;
        $this->name = $cityName;
    }
    
    /**
     * 
     * @return int
     */
    public function getID() {
        return $this->id;
    }
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabLocation implements JsonSerializable {
    private $city;
    private $province;
    private $nation;
    private $address = '';
    private $postalCode = '';
    
    /**
     * Creates an oabLocation object given information which cannot be modified after
     * 
     * @param oabCity $city
     * @param oabProvince $province
     * @param oabNation $nation
     * @param string $address
     */
    function oabLocation($city, $province, $nation, $address, $postalCode) {
        $this->city = $city;
        $this->province = $province;
        $this->nation = $nation;
        $this->address = $address;
        $this->postalCode = $postalCode;
    }
    
    /**
     * 
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * 
     * @return oabCity
     */
    public function getCity() {
        return $this->city;
    } 
    
    /**
     * 
     * @return string
     */
    public function getPostalCode() {
        return $this->postalCode;
    }
    
    /**
     * 
     * @return oabProvince
     */
    public function getProvince() {
        return $this->province;
    }
    
    /**
     * 
     * @return oabNation
     */
    public function getNation() {
        return $this->nation;
    }
    
    /**
     * Used as part of the JsonSerializable interface in order for oabLocation
     * objects to be properly encoded by json_encode
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

/**
 * Returns array of all locations listed in database if successful and empty array otherwise.
 * $cityFilter is a string which matches the beginning of all cities found in the database
 * 
 * @param mysqli $dbcon
 * @param string $cityFilter
 * @return oabLocation[]
 */
function oab_getLocations ($dbcon, $cityFilter) {
    $locations = array();
    
    $stmt = $dbcon->prepare("SELECT City.cityID,City.name,Province.provinceID,Province.name,Nation.stateID,Nation.name"
            . " FROM City"
            . " JOIN isACityOf ON City.cityID = isACityOf.city"
            . " JOIN Province ON Province.provinceID = isACityOf.province"
            . " JOIN isAProvinceOf ON Province.provinceID = isAProvinceOf.province"
            . " JOIN Nation ON Nation.stateID = isAProvinceOf.state"
            . " WHERE City.name LIKE ?"
            . " ORDER BY City.name"
            . " LIMIT 5"
            );
    
    if ($stmt) {
        $cityFilter = "{$cityFilter}%";
        $stmt->bind_param('s', $cityFilter);
        $stmt->execute();
        $stmt->bind_result($cityID, $cityName, $provinceID, $provinceName, $nationID, $nationName);
        
        while($stmt->fetch()) {
            $city = new oabCity($cityID, $cityName);
            $province = new oabProvince($provinceID, $provinceName);
            $nation = new oabNation($nationID, $nationName);
            
            array_push(
                $locations,
                new oabLocation($city, $province, $nation, '', '')
            );
        }
        
        $stmt->close();
    }
   
    return $locations;
}

/**
 * Return province information in oabProvince object given province ID.
 * If the operation was not successful or no provinceID was registered then returns province name as empty string
 * 
 * @param mysqli $dbcon
 * @param int $provinceID
 * @return oabProvince
 */
function oab_getProvince($dbcon, $provinceID) {
    $provinceName = '';
    
    $stmt = $dbcon->prepare("SELECT name FROM Province WHERE provinceID=?");

    if ($stmt) {
        $stmt->bind_param('d', $provinceID);
        $stmt->execute();
        $stmt->bind_result($r_provinceName);
        
        if ($stmt->fetch()) {
            $provinceName = $r_provinceName;
        }
        
        $stmt->close();
    }
    
    return new oabProvince($provinceID, $provinceName);
}

/**
 * Returns oabCity object with all city details. City name will be empty
 * string if no city with matching id was found
 * 
 * @param mysqli $dbcon
 * @param int $cityID
 * @return \oabCity
 */
function oab_getCity($dbcon, $cityID) {
    $stmt = $dbcon->prepare("SELECT name FROM City WHERE cityID=?");
    $cityName = ''; //Default value
    
    if ($stmt) {
        $stmt->bind_param('d', $cityID);
        $stmt->execute();
        $stmt->bind_result($r_cityName);
        
        if ($stmt->fetch()) {
            $cityName = $r_cityName;
        }
        
        $stmt->close();
    }
    
    return new oabCity($cityID, $cityName);
}

/**
 * Returns oabNation object with all nation info with the nation that has a matching id.
 * If no id exists in the database then the nation name will be an empty string.
 * 
 * @param mysqli $dbcon
 * @param int $nationID
 * 
 * @return \oabNation
 */
function oab_getNation($dbcon, $nationID) {
    $stmt = $dbcon->prepare("SELECT name FROM Nation WHERE stateID=?");
    $nationName = '';
    
    if ($stmt) {
        $stmt->bind_param('d', $nationID);
        $stmt->execute();
        $stmt->bind_result($r_nationName);
        
        if ($stmt->fetch()) {
            $nationName = $r_nationName;
        }
        
        $stmt->close();
    }
    
    return new oabNation($nationID, $nationName);
}


/**
 * Returns oabLocation info with all possible information given a province, city, or nation id
 * 
 * @param type $dbcon
 * @param LocationType $idType
 * @param int $id
 * 
 * @return \oabLocation
 */

/*Not Needed right now
function oab_getLocation($dbcon, $idType, $id) {
    
    switch($idType) {
        case LocationType::CITY:
            //TODO fetch city,province,nation info from id
            break;
        case LocationType::NATION:
            $province = null; $city=null;
            //TODO fetch nation info from id
            break;
        case LocationType::PROVINCE:
            $city = null;
            //TODO fetch nation,province info from id            
            break;
    }
    
    return new oabLocation($city, $province, $nation, '', '');
}
*/

/**
 * Sets users current session to reflect their interest in location.
 * Return true if operation successful and false otherwise.
 * 
 * @param mysqli $dbcon
 * @param int $cityID
 * @param int $provinceID
 * @param int $nationID
 * @return boolean
 */
function oab_setLocation($dbcon, $cityID, $provinceID, $nationID) {
    $successful = true;

    $cityName = oab_getCity($dbcon, $cityID)->getName();    
    $provinceName = oab_getProvince($dbcon, $provinceID)->getName();
    $nationName = oab_getNation($dbcon, $nationID)->getName();

    if ($cityName === '' || $provinceName === '' || $nationName === '') {
        $successful = false;
    }
    
    if ($successful) {
        $_SESSION['u_cityID'] = $cityID;
        $_SESSION['u_cityName'] = $cityName;
        $_SESSION['u_provinceID'] = $provinceID;
        $_SESSION['u_provinceName'] = $provinceName;
        $_SESSION['u_nationID'] = $nationID;
        $_SESSION['u_nationName'] = $nationName;
    }
    
    return $successful;
}
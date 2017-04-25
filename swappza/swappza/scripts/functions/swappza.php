<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class SwappzaCategory implements JsonSerializable {
    private $id;
    private $name;
    private $description;
    private $subCategories;
    
    /**
     * 
     * @param int $id
     * @param string $name
     * @param string $description
     * @param SwappzaSubCategory[] $subCategories
     */
    function SwappzaCategory($id, $name, $description, $subCategories) {
        $this->subCategories = $subCategories;
        $this->description = $description;
        $this->name = $name;
        $this->id = $id;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getSubCategories() {
        return $this->subCategories;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

class SwappzaSubCategory implements JsonSerializable {
    private $id;
    private $name;
    private $description;
    
    function SwappzaSubCategory($id, $name, $description) {
        $this->description = $description;
        $this->name = $name;
        $this->id = $id;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

/**
 * Retrieves an array of SwappzaCategories with all the subcategories attached for each one.
 * @param mysqli $dbcon
 * @return SwappzaCategory[]
 */
function oab_getSwappzaCategoryTree($dbcon) {
    $categories = array();
    $categoriesFinal = array();
    
    //Fetch Categories
    $stmt = $dbcon->prepare("SELECT categoryID,name"
            . " FROM SwappzaCategory WHERE ?");
    
    if ($stmt) {
        $one = 1;
        $stmt->bind_param('d', $one);
        $stmt->execute();
        $stmt->bind_result($categoryID, $name);

        while ($stmt->fetch()) {
            array_push($categories, new SwappzaCategory($categoryID, $name, "", array()));
        }
        
        $stmt->close();
        
        //Fetch Subcategory for each category
        foreach($categories as $category) {
            $stmt = $dbcon->prepare("SELECT SwappzaSubCategory.subcategoryID, SwappzaSubCategory.name"
                . " FROM SwappzaSubCategoryOf"
                . " JOIN SwappzaSubCategory ON SwappzaSubCategoryOf.subcategoryID=SwappzaSubCategory.subcategoryID"
                . " WHERE SwappzaSubCategoryOf.categoryID=?");
            
            if ($stmt) {
                $id = $category->getID();
                $stmt->bind_param('d', $id);
                $stmt->execute();
                $stmt->bind_result($subcategoryID, $subcategoryName);
                
                $subCategories = array();
                
                while($stmt->fetch()) {
                    array_push($subCategories, new SwappzaSubCategory($subcategoryID, $subcategoryName, ''));
                }
                
                array_push($categoriesFinal,
                        new SwappzaCategory(
                            $category->getID(),
                            $category->getName(),
                            $category->getDescription(),
                            $subCategories
                        ));
                
                $stmt->close();
            } else {
                error_log("failed to prepare second statement from oab_getSwappzaCategoryTree in swappza.php");
            }
        }        
        
    } else {
        error_log("failed to prepare first statement from oab_getSwappzaCategoryTree in swappza.php");
    }
    
    return $categoriesFinal;    
}

/**
 * Returns swappza subcategories if succcessful, false otherwise
 * @param mysqli $dbcon
 * @return boolean|SwappzaSubCategory[]
 */
function oabSwappzaGetAllSubCategories ($dbcon) {
    $subCategories = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT subcategoryID,name"
                . " FROM SwappzaSubCategory"
                . " WHERE ?");
                
        
        if ($stmt) {
            $one = 1;
            
            $stmt->bind_param('d', $one);
            $stmt->execute();
            $stmt->bind_result($id, $name);
            
            while ($stmt->fetch()) {
                array_push(
                    $subCategories, 
                    new SwappzaSubCategory($id, $name, null) // Description set to null because this isnt in database
                );
            }
            
            $stmt->close();
            
        } else {
            throw new Exception("Failed to prepare statement");
        }
        
        return $subCategories;
    } catch (Exception $ex) {
        oabSwappzaLogError($ex);
        return false;
    }
    
}

function oabSwappzaLogError($ex) {
    error_log("oabEFGetCommentsByPost() from {$ex->getFile()}:{$ex->getLine()}: {$ex->getMessage()}\n{$ex->getTraceAsString()}");
}
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class oabPage implements JsonSerializable {
    private $id;
    private $content;
    private $visibleTitle;
    private $linkName;
    private $author;
    private $datePublished;
    private $lastUpdated;
    private $revisionCount;
    private $styles;
    
    /**
     * Create immutable oabPage object
     * 
     * @param int $id
     * @param string $content
     * @param string $visibleTitle
     * @param string $linkName
     * @param oabUser $author
     * @param datetime $datePublished
     * @param datetime $lastUpdated
     * @param int $revisionCount
     * @param string $styles
     */
    function oabPage($id, $content, $visibleTitle, $linkName, $author, $datePublished, $lastUpdated, $revisionCount, $styles) {
        $this->id = $id;
        $this->content = $content;
        $this->visibleTitle = $visibleTitle;
        $this->linkName = $linkName;
        $this->author = $author;
        $this->datePublished = $datePublished;
        $this->lastUpdated = $lastUpdated;
        $this->revisionCount = $revisionCount;
        $this->styles = $styles;
    }
    
    public function getID() {
        return $this->id;
    }
    
    /**
     * 
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * 
     * @return string
     */
    public function getVisibleTitle() {
        return $this->visibleTitle;
    }
    
    /**
     * 
     * @return string
     */
    public function getLinkName() {
        return $this->linkName;
    }
    
    /**
     * 
     * @return \oabUser
     */
    public function getAuthor() {
        return $this->author;
    }
    
    /**
     * 
     * @return datetime
     */
    public function getDatePublished() {
        return $this->datePublished;
    }
    
    /**
     * 
     * @return datetime
     */
    public function getLastUpdated() {
        return $this->lastUpdated;
    }
    
    /**
     * 
     * @return int
     */
    public function getRevisionCount() {
        return $this->revisionCount;
    }
    
    /**
     * 
     * @return string
     */
    public function getStyles() {
        return $this->styles;
    }
    
    /**
     * Serialize object
     * 
     * @return json
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}

/**
 * 
 * @param mysqli $dbcon
 * @param int $pageID
 */
function oab_getPage($dbcon, $pageID) {
    $successful = false;
    
    $stmt = $dbcon->prepare("SELECT visibleTitle,linkTitle,content,styles,author,datePublished,lastUpdated,revisions"
            . " FROM Page WHERE pageID=?");
    
    if ($stmt) {
        $stmt->bind_param('d', $pageID);
        $stmt->execute();
        $stmt->bind_result($visibleTitle, $linkTitle, $content, $styles, $authorID, $datePublished, $lastUpdated, $revisionCount);

        if ($stmt->fetch()) {
            $author = new oabUser(null, null, $authorID, null, null, null, null, null, null, null);
            $page = new oabPage($pageID, $content, $visibleTitle, $linkTitle, $author, $datePublished, $lastUpdated, $revisionCount, $styles);
            $successful = true;
        }
        
        $stmt->close();
    }
    
    return $successful;
}

/**
 * Attempts to insert a new page into the database, returns the id of the post, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param oabPage $page
 * 
 * @return boolean|oabPage
 */
function oabCreatePage($dbcon, $page) {
    try {    
        $stmt = $dbcon->prepare("INSERT INTO Page (visibleTitle,linkTitle,content,styles,author,lastUpdated,datePublished)"
                . " VALUES (?,?,?,?,?,NOW(),NOW())");

        $visibleTitle = $page->getVisibleTitle();
        $linkName = $page->getLinkName();
        $content = $page->getContent();
        $styles = $page->getStyles();
        
        if ($stmt) {
            $stmt->bind_param('ssbsd',
                $visibleTitle,
                $linkName,
                $content,
                $styles,
                $_SESSION['u_ID']
            );

            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to create new post');
            }
            
            echo'ere';
            
            $pageID = $stmt->insert_id;
            
            $stmt->close();
            
            return $pageID;
        } else {
            throw new Exception('Failed to prepare message');
        }
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}

/**
 * Removes page from the database. If all pages were removed successfully true is returned,
 * false is returned otherwise.
 * 
 * DELEVEOPER NOTE: look at possibility of doing multiple pages
 * 
 * @param mysqli $dbcon
 * @param int $pageID
 * 
 * @return boolean
 */
function oabDeletePage($dbcon, $pageID) {
    try {
        $stmt = $dbcon->prepare("DELETE FROM Page WHERE pageID=?");

        if ($stmt) {
            $stmt->bind_param('d', $pageID);
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to delete page');
            }
            
            $stmt->close();
            return true;
            
        } else {
            throw new Exception('Failed to prepaer statement');
        }        
        
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}

/**
 * Returns true if page successfully updated, false otherwise.
 * 
 * @param mysqli $dbcon
 * @param oabPage $page
 * @return boolean
 */
function oabUpdatePage($dbcon, $page) {
    try {
        $stmt = $dbcon->prepare("UPDATE Page"
            . " SET visibleTitle=?, linkTitle=?, styles=?, content=?, lastUpdated=NOW()"
            . " WHERE pageID=?");
        
        if ($stmt) {
            $pageID = $page->getID();
            $visibleTitle = $page->getVisibleTitle();
            $linkName = $page->getLinkName();
            $content = $page->getContent();
            $styles = $page->getStyles();
            
            $stmt->bind_param(
                    'sssbd',
                    $visibleTitle,
                    $linkName,
                    $styles,
                    $content,
                    $pageID            
            );
            
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to update page');
            }
            
            $stmt->close();
            return true;
            
        } else {
            throw new Exception('Failed to prepare statement');
        }
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}

/**
 * Return array with pages if successful, false otherwise.
 * @param mysqli $dbcon
 * @return boolean|oabPage[]
 * @throws Exception
 */
function oabGetAllPages ($dbcon) {
    $pages = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT pageID, visibleTitle, linkTitle, author, datePublished, lastUpdated, revisions, styles, content"
                . " FROM Page"
                . " JOIN Users ON Users.userID=Page.author"
                . " WHERE ?");
        
        if ($stmt) {
            $one = 1;
            $stmt->bind_param('d', $one);
            $stmt->execute();
            $stmt->bind_result($id, $visibleTitle, $linkTitle, $authorID, $datePublished, $lastUpdated, $revisions, $styles, $content);
            $stmt->store_result();
            
            while($stmt->fetch()) {
                $author = oab_getUserProfile($dbcon, $authorID, oabUserIdType::_DEFAULT);
                
                array_push(
                    $pages,
                    new oabPage($id, $content, $visibleTitle, $linkTitle, $author, $datePublished, $lastUpdated, $revisions, $styles)
                );
            }
            
            $stmt->close();
            return $pages;
        } else {
            throw new Exception('Failed to prepare statement');
        }
        
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}

function oabLogError($ex) {
    error_log("oabEFGetCommentsByPost() from {$ex->getFile()}:{$ex->getLine()}: {$ex->getMessage()}\n{$ex->getTraceAsString()}");
}

/**
 * 
 * @param mysqli $dbcon
 * @param integer $pageID
 */
function oabGetPage($dbcon, $pageID) {
    try{
        $stmt = $dbcon->prepare("SELECT pageID, visibleTitle, linkTitle, author, datePublished, lastUpdated, revisions, styles, content"
                . " FROM Page"
                . " JOIN Users ON Users.userID=Page.author"
                . " WHERE pageID=?");
        
        if ($stmt) {
            $stmt->bind_param('d', $pageID);
            $stmt->execute();
            $stmt->bind_result($id, $visibleTitle, $linkTitle, $authorID, $datePublished, $lastUpdated, $revisions, $styles, $content);
            $stmt->store_result();
            
            if($stmt->fetch()) {
                $author = oab_getUserProfile($dbcon, $authorID, oabUserIdType::_DEFAULT);                
                $page = new oabPage($pageID, $content, $visibleTitle, $linkTitle, $author, $datePublished, $lastUpdated, $revisions, $styles);
            } else {
                throw new Exception('Failed to retrieve page information');
            }
            
            $stmt->close();
            return $page;
            
        } else {
            throw new Exception('Failed to prepare statement');
        }
        
    } catch (Exception $ex) {
        oabLogError($ex);
        return false;
    }
}
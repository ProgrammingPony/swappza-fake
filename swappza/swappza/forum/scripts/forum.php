<?php
class oabEFSubCategory implements JsonSerializable {
    private $id;
    private $name;
    private $description;
    
    /**
     * 
     * @param int $id
     * @param string $name
     * @param string $description
     */
    function oabEFSubCategory($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
    
    function getDescription() {
        return $this->description;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getName() {
        return $this->name;
    }
    
    function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabEFCategory implements JsonSerializable {
    private $id;
    private $name;
    private $description;
    private $subCategories;
    
    /**
     * 
     * @param int $id
     * @param string $name
     * @param string $description
     * @param oabEFSubCategory $subCategories
     */
    function oabEFCategory($id, $name, $description, $subCategories) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->subCategories = $subCategories;
    }
    
    function getDescription() {
        return $this->description;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getName() {
        return $this->name;
    }
    
    function getSubCategories() {
        return $this->subCategories;
    }
    
    function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabEFPost implements JsonSerializable {
    private $id;
    private $title;
    private $author;
    private $datePosted;
    private $lastEditted;
    private $subCategory;
    private $text;
    private $attachments;
    
    /**
     * 
     * @param int $id
     * @param string $title
     * @param oabUser $author
     * @param datetime $datePosted
     * @param datetime $lastEditted
     * @param oabEFSubCategory $subCategory
     * @param string $text
     * @param oabEFAttachment[] $attachments
     */
    function oabEFPost($id, $title, $author, $datePosted, $lastEditted, $subCategory, $text, $attachments) {
        $this->id = $id;
        $this->author = $author;
        $this->datePosted = $datePosted;
        $this->lastEditted = $lastEditted;
        $this->text = $text;
        $this->attachments = $attachments;
        $this->title = $title;
        $this->subCategory = $subCategory;
    }
    
    function getAttachments() {
        return $this->attachments;
    }
    
    function getAuthor() {
        return $this->author;
    }
    
    function getDateOfPosting() {
        return $this->datePosted;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getLastEditDate() {
        return $this->lastEditted;
    }
    
    function getSubCategory() {
        return $this->subCategory;
    }
    
    function getText() {
        return $this->text;
    }
    
    function getTitle() {
        return $this->title;
    } 
    
    function jsonSerialize() {
        return get_object_vars($this);
    }
}

class oabEFComment implements JsonSerializable {
    private $id;
    private $author;
    private $datePosted;
    private $lastEditted;
    private $text;
    private $attachments;
    
    /**
     * 
     * @param int $id
     * @param oabUser $author
     * @param datetime $datePosted
     * @param datetime $lastEditted
     * @param string $text
     * @param oabEFAttachment $attachments
     */
    function oabEFComment($id, $author, $datePosted, $lastEditted, $text, $attachments) {
        $this->id = $id;
        $this->author = $author;
        $this->datePosted = $datePosted;
        $this->lastEditted = $lastEditted;
        $this->text = $text;
        $this->attachments = $attachments;
    }
    
    function getAttachments() {
        return $this->attachments;
    }
    
    function getAuthor() {
        return $this->author;
    }
    
    function getDateOfPosting() {
        return $this->datePosted;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getLastEditDate() {
        return $this->lastEditted;
    }
    
    function getText() {
        return $this->text;
    }
    
    function jsonSerialize() {
        return get_object_vars($this);
    }
}

/**
 * Defines valid file types for EFAttachments
 */
abstract class oabEFAttachmentType {
    const BITMAP = 'bit';
    const GIF = 'gif';
    const JPG = 'jpg';
    const JPEG = 'jpeg';    
    const PNG = 'png';
    const PDF = 'pdf';
    const RICH_TEXT_FORMAT = 'rtf'; 
    const TEXT = 'txt';
    const WORD = 'doc';
    const WORDX = 'docx';
}

class oabEFAttachment implements JsonSerializable {
    private $id;
    private $name;
    private $type;
    private $size;
    
    /**
     * 
     * @param int $id
     * @param string $name
     * @param oabEFAttachmentType $type
     * @param int $size
     */
    function oabEFAttachment($id, $name, $type, $size) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getName() {
        return $this->name;
    }
    
    function getSize() {
        return $this->size;
    }
    
    function getType() {
        return $this->type;
    }
    
    function jsonSerialize() {
        return get_object_vars($this);
    }    
}

function oabEFGetSubCategoryDetails($dbcon, $id) {
    $successful = false;
    
    $stmt = $dbcon->prepare("SELECT name,description"
            . " FROM ForumSubCategories"
            . " WHERE subCategoryID=?");
    
    if ($stmt) {
        $stmt->bind_param('d', $id);
        $stmt->execute();
        $stmt->bind_result($r_name, $r_desc);
        
        if ($stmt->fetch()) {
            $name = $r_name;
            $desc = $r_desc;
            $successful = true;
        }
        
        $stmt->close();
    } else {
        error_log("Failed to prepare statement from oabEFGetSubCategoryDetails");
    }
    
    if ($successful) {
        return new oabEFSubCategory($id, $name, $desc);
    } else {
        return false;
    }
}

/**
 * Returns the attachment associated with a provided attachment ID, false if not successful
 * @param type $dbcon
 * @param type $attachmentID
 * @return boolean|\oabEFAttachment
 */
function oabEFGetAttachment($dbcon, $attachmentID) {
    $successful = false;
    
    $stmt = $dbcon->prepare("SELECT name,type,size"
            . " FROM ForumAttachments"
            . " WHERE attachmentID=?");
    
    if ($stmt) {
        $stmt->bind_param('d', $attachmentID);
        $stmt->execute();
        $stmt->bind_result($r_name, $r_type, $r_size);
        
        if ($stmt->fetch()) {
            $name = $r_name;
            $type = $r_type;
            $size = $r_size;
            $successful = true;
        }
        
        $stmt->close();
    } else {
        error_log('Failed to prepare statement from oabEFGetAttachment');
    }
    
    if ($successful) {
        return new oabEFAttachment($attachmentID, $name, $type, $size);
    } else {
        return false;
    }
}

/**
 * Returns all attachments associated with a post ID or false if not successful
 * @param mysqli $dbcon
 * @param int $postID
 * @return boolean|\oabEFAttachment[]
 */
function oabEFGetAttachmentByPost($dbcon, $postID) {
    $successful = false;
    
    $attachments = array();
    
    $stmt = $dbcon->prepare("SELECT ForumPostAttachments.attachmentID, name,type,size"
            . " FROM ForumPostAttachments"
            . " JOIN ForumAttachments on ForumPostAttachments.attachmentID = ForumAttachments.attachmentID"
            . " WHERE postID=?");
    
    if ($stmt) {
        $stmt->bind_param('d', $postID);
        $stmt->execute();
        $stmt->bind_result($attachmentID, $name, $type, $size);
        
        while ($stmt->fetch()) {
            array_push($attachments, new oabEFAttachment($attachmentID, $name, $type, $size));
        }
        
        $successful = true;
        $stmt->close();
    } else {
        error_log('Failed to prepare statement from oabEFGetAttachmentByPost');
    }
    
    if ($successful) {
        return $attachments;
    } else {
        return false;
    }   
}

/**
 * Returns all the associated attachments with the provided comment ID or false if not succesfful
 * @param mysqli $dbcon
 * @param int $commentID
 * @return boolean|\oabEFAttachment[]
 */
function oabEFGetAttachmentByComment($dbcon, $commentID) {
    $successful = false;
    
    $attachments = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT ForumCommentAttachments.attachmentID, name,type,size"
                . " FROM ForumCommentAttachments"
                . " JOIN ForumAttachments on ForumCommentAttachments.attachmentID = ForumAttachments.attachmentID"
                . " WHERE commentID=?");

        if ($stmt) {
            $stmt->bind_param('d', $commentID);
            $stmt->execute();
            $stmt->bind_result($attachmentID, $name, $type, $size);

            while ($stmt->fetch()) {
                array_push($attachments, new oabEFAttachment($attachmentID, $name, $type, $size));

            }

            $successful = true;
            $stmt->close();
        } else {
            error_log('Failed to prepare statement from oabEFGetAttachmentByComment');
        }
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    if ($successful) {
        return $attachments;
    } else {
        return false;
    } 
}

/**
 * Returns the details of a post with the specified $id or an empty array if no such postID exists.
 * 
 * @param mysqli $dbcon
 * @param int $id
 * @return \oabEFPost
 */
function oabEFGetPost($dbcon, $id) {
    $successful = false;
    
    //Fetch table info
    $stmt = $dbcon->prepare("SELECT title, posterID, datePosted, lastEditted, subCategoryID, text"
            . " FROM ForumPosts"
            . " WHERE postID=?");
    
    if ($stmt) {        
        $stmt->bind_param('d', $id);
        $stmt->execute();
        $stmt->bind_result($r_title, $r_posterID, $r_datePosted, $r_lastEditted, $r_subCategoryID, $r_text);
        
        if ($stmt->fetch()) {
            $title = $r_title;
            $posterID = $r_posterID;
            $datePosted = $r_datePosted;
            $lastEditted = $r_lastEditted;
            $subCategoryID = $r_subCategoryID;
            $text = $r_text;
            $successful = true;
        } else {
            $stmt->close();
            return false;
        }
        
        $stmt->close();
    }
    
    //Get subcategory information from ID
    $subCategory = oabEFGetSubCategoryDetails($dbcon, $subCategoryID);
    
    //Get Poster Information
    $author = oab_getUserProfile($dbcon, $posterID, oabUserIdType::_DEFAULT);
    
    //Get Attachments
    $attachments = oabEFGetAttachmentByPost($dbcon, $id);
    
    if ($successful) {
        return new oabEFPost($id, $title, $author, $datePosted, $lastEditted, $subCategory, $text, $attachments);
    } else {
        return false;
    }
}

/**
 * Returns comment details if successful, false otherwise
 * @param mysqli $dbcon
 * @param int $id
 * @return boolean|\oabEFComment
 */
function oabEFGetComment($dbcon, $id) {
    $successful = false;
    
    //Fetch table info
    $stmt = $dbcon->prepare("SELECT commenterID, datePosted, lastEditted, text"
            . " FROM ForumComments"
            . " WHERE postID=?");
    
    if ($stmt) {
        $stmt->bind_param('d', $id);
        $stmt->execute();
        $stmt->bind_result($r_commenterID, $r_datePosted, $r_lastEditted, $r_text);
        
        if ($stmt->fetch()) {
            $commenterID = $r_commenterID;
            $datePosted = $r_datePosted;
            $lastEditted = $r_lastEditted;
            $text = $r_text;
            $successful = true;
        }
        
        $stmt->close();
    }
    
    //Get subcategory information from ID
    $subCategory = oabEFGetSubCategoryDetails($dbcon, $subCategoryID);
    
    //Get Poster Information
    $author = oab_getUserProfile($dbcon, $commenterID, oabUserIdType::_DEFAULT);
    
    //Get Attachments
    $attachments = oabEFGetAttachmentByComment($dbcon, $id);
    
    if ($successful) {
        return new oabEFComment($id, $author, $datePosted, $lastEditted, $text, $attachments);
    } else { 
        return false;
    }
}

/**
 * Records new post into database, returns the post id if successful, false otherwise
 * @param mysqli $dbcon
 * @param oabEFPost $post
 * @return boolean|int
 */
function oabEFCreatePost($dbcon, $post) {   
    try{        
        //Add entry into ForumPosts Table
        $stmt = $dbcon->prepare("INSERT INTO ForumPosts (title, datePosted, lastEditted, posterID, subCategoryID, text)"
                . " VALUES (?, NOW(), NOW(), ?, ?, ?)");
        
        if ($stmt) {
            $title = $post->getTitle();
            $subCategoryID = $post->getSubCategory()->getID();
            $text = $post->getText();            
            
            $stmt->bind_param('sdds', $title, $_SESSION['u_ID'], $subCategoryID, $text);
            $stmt->execute();
            
            $postID = $stmt->insert_id;
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert entry into ForumPosts');
            } 
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement for entry into ForumPosts');
        }
        
        return $postID;
        
    } catch (Exception $ex) {
        oabEFLogError($ex);        
        return false;
    }
}

/**
 * Creates a new comment and returns its comment id if successful, false otherwise.
 * @param mysqli $dbcon
 * @param oabEFComment $comment
 * @param int $postID
 * @return boolean|int
 */
function oabEFCreateComment($dbcon, $comment, $postID) {
    try {
        $dbcon->autocommit(false);
        
        $stmt = $dbcon->prepare("INSERT INTO ForumComments (datePosted, lastEditted, commenterID, text)"
                . " VALUES (NOW(), NOW(), ?, ?)");
        
        if ($stmt) {
            $commenterID = $comment->getAuthor()->getUserID();
            $text = $comment->getText();
            
            $stmt->bind_param('ds', $_SESSION['u_ID'], $text);
            $stmt->execute();
            
            $commentID = $stmt->insert_id;
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert into ForumComments');
            }
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare insert statement into ForumComments');
        }
        
        $stmt = $dbcon->prepare("INSERT INTO ForumCommentFor (commentID, postID) "
                . "VALUES (?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('dd', $commentID, $postID);
            $stmt->execute();
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert into ForumCommentFor');              
            }
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare insert statement into ForumCommentFor');
        }
        
        $dbcon->commit();
        $dbcon->autocommit(true);
        return true;
        
    } catch (Exception $ex) {
        oabEFLogError($ex);
        
        $dbcon->rollback();
        $dbcon->autocommit(true);        
        return false;
    } 
}

/**
 * Removes all existance of a post correstponding to provided post id from the database. Returns true if successful, false otherwise.
 * @param mysqli $dbcon
 * @param int $id
 * @return boolean
 */
function oabEFDeletePost($dbcon, $id) {
    $successful = false;
    $dbcon->autocommit(false);   
    
    try {
        //Delete from main attachment table
        //All dependant tables will be deleted automatically ON DELETE = Cascade
        $stmt = $dbcon->prepare("DELETE FROM ForumPosts WHERE postID=?");

        if ($stmt) {
            $stmt->bind_param('d', $id);
            $stmt->execute();
            $stmt->close();
        }
        
        $dbcon->commit();
    } catch (mysqli_sql_exception $e) {
        oabEFLogError($ex);
        
        $dbcon->rollback();
        $dbcon->autocommit(true);
        
        return false;
    }
    
    $dbcon->autocommit(true);
    return true;
}

/**
 * Removes all existance of a comment correstponding to provided comment id from the database. Returns true if successful, false otherwise.
 * @param mysqli $dbcon
 * @param int $id
 * @return boolean
 */
function oabEFDeleteComment($dbcon, $id) {
    $successful = false;
    
    try {
        //Deleting only from main table, assuming all dependancies are set to ON DELETE=CASCADE
        //Might leave attachment files left over even if attachments removed from database.
        $stmt = $dbcon->prepare("DELETE FROM ForumComments WHERE commentID=?");
        
        if ($stmt) {
            $stmt->bind_param('d', $id);
            $stmt->execute();
            
            $stmt->close();
        }
    } catch (mysqli_sql_exception $e) {
        oabEFLogError($ex);
        return false;
    }
    
    return true;
}

/**
 * Modifies the content of an existing post with the provided post ID with new information. Any fields left empty in the object will be ignored.
 * @param mysqli $dbcon
 * @param oabEFPost $newPost
 * @param files[] $files
 * @return boolean
 */
function oabEFEditPost($dbcon, $newPost, $files) {   
    try {
        $dbcon->autocommit(false);
        
        //Update Main Table
        $stmt = $dbcon->prepare("UPDATE ForumPosts"
                . " SET title=?, lastEditted=CURRENT_TIMESTAMP, text=?"
                . " WHERE postID=?");
        
        if ($stmt) {
            $title = $newPost->getTitle();
            $text = $newPost->getText();
            $postID = $newPost->getID();
            
            $stmt->bind_param('ssd', $title, $text, $postID);
            $stmt->execute();
            
            $stmt->close();
        } else {
            throw new Exception ('Failed to prepare mysql statement (A)');
        }
        
        //Update Attachments Table and files
        $attachments = $newPost->getAttachments();
        $newAttachmentIDs = array();
        
        foreach($attachments as $attachment) {
            $attachmentID = oabEFCreateAttachment($dbcon, $attachment);
            array_push($newAttachmentIDs, $attachmentID);
        }
        
        foreach($newAttachmentIDs as $attachmentID) {
            $stmt = $dbcon->prepare("INSERT INTO ForumPostAttachments"
                    . "(postID, attachmentID)"
                    . "VALUES (?,?)");
            
            if ($stmt) {
                $stmt->bind_param('dd', $postID, $attachmentID);
                $stmt->execute();                
                $stmt->close();
            } else {
                throw new Exception('Failed to prepare mysql statement (B)');
            }
        }
        
        //Commit and return normal database configuration
        $dbcon->commit();
        $dbcon->autocommit(true);
        
    } catch (mysqli_sql_exception $ex) {
        oabEFLogError($ex);
        
        $dbcon->rollback();
        $dbcon->autocommit(true);
        
        return false;
    }
    
    return true;
}

/**
 * Modifies the content of an existing comment with the specified id found in $newComment. Currently does not support updating attachments
 * @param mysqli $dbcon
 * @param oabEFPost $newComment
 * @return boolean
 */
function oabEFEditComment($dbcon, $newComment) {
    try {
        $stmt = $dbcon->prepare("UPDATE ForumComments"
                . " SET lastEditted=NOW(), text=?"
                . " WHERE commentID=?");
        
        if ($stmt) {
            $text = $newComment->getText();
            $commentID = $newComment->getID();
            
            $stmt->bind_param('sd', $text, $commentID);
            $stmt->execute();
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to update Comment in table ForumComments');
            }
        } else {
            throw new Exception('Failed to prepare statement to update comment to ForumComments');
        }
        
        return true;
        
    } catch (Exception $ex) {
        oabEFLogError($ex);
        return false;
    }
}

/**
 * Returns the attachment of the new added attachment if successful, false otherwise. Will return false if file size is either larger than the maximum or is 0 or lower. False is also returned if the file type is invalid. Field name is the name of the upload field.
 * @param mysqli $dbcon
 * @param string $fieldName
 * @return boolean|oabEFAttachment
 */
function oabEFCreateAttachment($dbcon, $fieldName) {
    global $LOCAL_BASE_DOMAIN_ADDRESS;
    
    try {
        //Obtain information about file and assert accepted file type and size
        //NOTE: php.ini specifies maximum file upload size if the one specified here is higher then it won't work
        $fileSize = $_FILES[$fieldName]['size'];
        $fileName = $_FILES[$fieldName]['name'];
        $fileType = strtoupper(pathinfo($fileName )['extension']); //basename removes 'image/' from 'image/jpeg' since this is mime type
        
        //Ensure file is not above maximum size and that its file type we accept
        $maximum_file_size = intval(ini_get('upload_max_filesize')); //This is a basic solution it may not work depneding on php settings
        if (($fileSize > $maximum_file_size) ||
                !in_array( $fileType, oabEFGetAcceptedAttachmentTypes($dbcon))
                ) {
            return false;
        }          
        
        //Insert information in Database
        $dbcon->autocommit(false); //Turn it off in case error when uploading file
        $stmt = $dbcon->prepare("INSERT INTO ForumAttachments"
                . " (name,type,size)"
                . " VALUES (?, ?, ?)");
        
        if ($stmt) {            
            $stmt->bind_param('ssd', $fileName, $fileType, $fileSize);
            $stmt->execute();
            
            $attachmentID = $stmt->insert_id;
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert row into Forum Attachments');
            }
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement for insert into Forum Attachmetns');
        }
        
        //Upload file - can only reach here if insert successful        
        move_uploaded_file(
                $_FILES[$fieldName]['tmp_name'],
                "{$LOCAL_BASE_DOMAIN_ADDRESS}/forum/attachments/{$attachmentID}.{$fileTypeLowerCase}"
        );
        
        $dbcon->commit();
        $dbcon->autocommit(true);
        
        $attachment = new oabEFAttachment($attachmentID, $fileName, $fileType, $fileSize);
        return $attachment;
        
    } catch (mysqli_sql_exception $e) {
        oabEFLogError($e);
        
        $dbcon->rollback();
        $dbcon->autocommit(true);
        
        return false;
    }
}

/**
 * Permanently deletes an attachment and all its associations. Returns true if successful false otherwise.
 * 
 * @param mysqli $dbcon
 * @param int $id
 * @return boolean
 */
function oabEFDeleteAttachment($dbcon, $id) {
    try {
        $dbcon->autocommit(false);
        
        //All dependant foreign keys expected to be set to cascade on delete
        $stmt = $dbcon->prepare("DELETE FROM ForumPostAttachments WHERE attachmentID=?");
        
        if ($stmt) {
            $stmt->bind_param('d', $id);
            $stmt->execute();
            $stmt->close();
        }
        
        $stmt = $dbcon->prepare("DELETE FROM ForumCommentAttachments WHERE attachmentID=?");
        
        if ($stmt) {
            $stmt->bind_param('d', $id);
            $stmt->execute();
            $stmt->close();
        }

        //Remove from Attachments table
        $stmt = $dbcon->prepare("DELETE FROM ForumAttachments WHERE attachmentID=?");

        if ($stmt) {
            $stmt->bind_param('d', $id);
            $stmt->execute();

            if ($stmt->affected_rows>0) {
                $successCounts++;
            }

            $stmt->close();
        }
        
        $dbcon->commit();
        
    } catch(mysqli_sql_exception $e) {
        oabEFLogError($e);
        $dbcon->rollback();
        $dbcon->autocommit(true);
        return false;
    }
    
    $dbcon->autocommit(true);
    return true;
}

/**
 * Return all comments associated with a post specified by the post id, from the given start index until after an offset of consecutive posts are retrieved
 * @param myqsli $dbcon
 * @param int $postID
 * @param int $startIndex
 * @param int $offset
 * @return oabEFComment[]
 */
function oabEFGetCommentsByPost($dbcon, $postID, $startIndex, $offset) {
    $finalComments = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT ForumCommentFor.commentID, datePosted,lastEditted,commenterID,text"
                . " FROM ForumCommentFor"
                . " JOIN ForumComments ON ForumComments.commentID = ForumCommentFor.commentID"
                . " WHERE postID=?"
                . " ORDER BY datePosted"
                . " LIMIT ?"
                . " OFFSET ?");

        if ($stmt) {
            $stmt->bind_param('ddd', $postID, $startIndex, $offset);
            $stmt->execute();
            $stmt->bind_result($commentID, $datePosted, $lastEditted, $commenterID, $text);
            $stmt->store_result();
            
            while ($stmt->fetch()) {
                $author = oab_getUserProfile($dbcon, $commenterID, oabUserIdType::_DEFAULT);
                $attachments = oabEFGetAttachmentByComment($dbcon, $commentID);
                $comment = new oabEFComment($commentID, $author, $datePosted, $lastEditted, $text, $attachments);
                
                array_push($finalComments, $comment);
            }
            
            $stmt->close();
        }
                
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    return $finalComments;
}

/**
 * Logs an error using php for the exception $ex
 * @param Exception $ex
 */
function oabEFLogError($ex) {
    error_log("oabEFGetCommentsByPost() from {$ex->getFile()}:{$ex->getLine()}: {$ex->getMessage()}\n{$ex->getTraceAsString()}");
}

/**
 * Find and returns an array of oabEFSubCategory items consisting of all subcategories associated with the provided categoryID
 * @param mysqli $dbcon
 * @param int $categoryID
 * @return oabEFSubCategory[]
 */
function oabEFGetAllSubCategoriesByCategory($dbcon, $categoryID) {
    $subCategories = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT ForumSubCategoryOf.subCategoryID, name, description"
                . " FROM ForumSubCategoryOf"
                . " JOIN ForumSubCategories ON ForumSubCategoryOf.subCategoryID=ForumSubCategories.subCategoryID"
                . " WHERE categoryID=?"
                . " ORDER BY name");
        
        if ($stmt) {
            $stmt->bind_param('d', $categoryID);
            $stmt->execute();
            $stmt->bind_result($subCategoryID, $name, $description);
            
            while ($stmt->fetch()){
                $subCategory = new oabEFSubCategory($subCategoryID, $name, $description);
                array_push($subCategories, $subCategory);
            }
            
            $stmt->close();
        }
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    return $subCategories;
}

/**
 * Returns all the categories for the forum as an array of oabEFCategory objects
 * @param mysqli $dbcon
 * @return oabEFCategory[]
 */
function oabEFGetAllCategories($dbcon) {
    $categories = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT categoryID, name, description"
                . " FROM ForumCategories"
                . " WHERE ?");
        
        if ($stmt) {
            $one = 1;
            $stmt->bind_param('d', $one);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($categoryID, $catName, $catDescription);
            
            while ($stmt->fetch()) {
                $subCategories = oabEFGetAllSubCategoriesByCategory($dbcon, $categoryID);
                $category = new oabEFCategory($categoryID, $catName, $catDescription, $subCategories);
                
                array_push($categories, $category);
            }
            
            $stmt->close();
        }
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    return $categories;
}

/**
 * Returns posts wi
 * @param mysqli $dbcon
 * @param numeric $subCategoryID
 * @param numeric $startIndex
 * @param numeric $offset
 * @return oabEFPost[]
 */
function oabEFGetPostsBySubCategory($dbcon, $subCategoryID, $startIndex, $offset) {
    $posts = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT postID, title, datePosted, lastEditted, posterID, text"
                . " FROM ForumPosts"
                . " WHERE subCategoryID=?"
                . " ORDER BY datePosted DESC"
                . " LIMIT ?"
                . " OFFSET ?");
        
        if ($stmt) {
            $stmt->bind_param('ddd', $subCategoryID, $offset, $startIndex);
            $stmt->execute();
            $stmt->bind_result($postID, $title, $datePosted, $lastEditted, $posterID, $text);
            $stmt->store_result();
            
            while ($stmt->fetch()) {
                $author = oab_getUserProfile($dbcon, $posterID, oabUserIdType::_DEFAULT);
                $subCategory = oabEFGetSubCategoryDetails($dbcon, $subCategoryID);
                $attachments = oabEFGetAttachmentByPost($dbcon, $postID);
                
                $post = new oabEFPost($postID, $title, $author, $datePosted, $lastEditted, $subCategory, $text, $attachments); 
                
                array_push($posts, $post);
            }
            
            $stmt->close();
        }
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    return $posts;
}

/**
 * Returns string array of all accepted file extentions capitalized.
 * @param mysqli $dbcon
 * @return string[]
 */
function oabEFGetAcceptedAttachmentTypes($dbcon) {
    $acceptedFileTypes = array();
    
    try {
        $stmt = $dbcon->prepare("SELECT type FROM ForumAttachmentTypes WHERE ?");
        
        if ($stmt) {
            $one = 1;
            $stmt->bind_param('d', $one);
            $stmt->execute();
            $stmt->bind_result($type);
            
            while ($stmt->fetch()) {
                array_push($acceptedFileTypes, $type);
            }
            
            $stmt->close();
        }
    } catch (Exception $ex) {
        oabEFLogError($ex);
    }
    
    return $acceptedFileTypes;
}

/**
 * Assigns an attachment with the provided attachment ID as belonging to a post with the specified postID. Returns true if successful, false in all other cases.
 * This assumes that the attachment ID is not assigned to another post.
 * @param mysqli $dbcon
 * @param numeric $attachmentID
 * @param numeric $postID
 * @return boolean
 */
function oabEFAssignAttachmentToPost($dbcon, $attachmentID, $postID) {
    try {
        $stmt = $dbcon->prepare("INSERT INTO ForumPostAttachments (attachmentID,postID)"
                . " VALUES (?,?)");
        
        if ($stmt) {
            $stmt->bind_param('dd', $attachmentID, $postID);
            $stmt->execute();
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert association of attachment to specified post');
            }
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare insert into ForumPostAttachments');
        }
        
        return true;
    } catch (Exception $ex) {
        oabEFLogError($ex);
        
        if ($stmt) {
            $stmt->close();
        }
        
        return false;
    }
}

/**
 * Assigns an attachment with the provided attachment ID as belonging to a comment with the specified commenttID. Returns true if successful, false in all other cases.
 * This assumes that the attachment ID is not assigned to another comment.
 * @param mysqli $dbcon
 * @param numeric $attachmentID
 * @param numeric $commentID
 * @return boolean
 */
function oabEFAssignAttachmentToComment($dbcon, $attachmentID, $commentID) {
    try {
        $stmt = $dbcon->prepare("INSERT INTO ForumCommentAttachments (attachmentID,commentID)"
                . " VALUES (?,?)");
        
        if ($stmt) {
            $stmt->bind_param('dd', $attachmentID, $commentID);
            $stmt->execute();
            
            if ($stmt->affected_rows < 1) {
                throw new Exception('Failed to insert association of attachment to specified comment');
            }
            
            $stmt->close();
            
        } else {
            throw new Exception('Failed to prepare insert into ForumCommentAttachments');
        }
        
        return true;
        
    } catch (Exception $ex) {
        oabEFLogError($ex);
        
        return false;
    }
}
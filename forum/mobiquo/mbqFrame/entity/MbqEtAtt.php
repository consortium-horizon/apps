<?php

defined('MBQ_IN_IT') or exit;

/**
 * attachment class
 * 
 * @since  2012-7-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtAtt extends MbqBaseEntity {
    
    public $attId;
    public $groupId;
    public $forumId;
    public $postId;
    public $filtersSize;        /* Return the file size of the uploaded file after processed by the forum system. */
    public $uploadFileName;
    public $attType;    /* forum post att or user avatar */
    public $contentType;    /* return "image", "pdf" or "other" */
    public $thumbnailUrl;   /* if content type = "image", use absolute path (optional: if not presented, use "url" to load thumbnail instead) */
    public $url;    /* URL of the attachment source. */
    public $userId;     /* user id who submit this attachment */
    public $mimeType; /* Attachment file MIME type. Example value: image/png */
    
    public $oMbqEtUser; /* user who submit this attachment */
    
    public function __construct() {
        parent::__construct();
        $this->attId = clone MbqMain::$simpleV;
        $this->groupId = clone MbqMain::$simpleV;
        $this->forumId = clone MbqMain::$simpleV;
        $this->postId = clone MbqMain::$simpleV;
        $this->filtersSize = clone MbqMain::$simpleV;
        $this->uploadFileName = clone MbqMain::$simpleV;
        $this->attType = clone MbqMain::$simpleV;
        $this->contentType = clone MbqMain::$simpleV;
        $this->thumbnailUrl = clone MbqMain::$simpleV;
        $this->url = clone MbqMain::$simpleV;
        $this->userId = clone MbqMain::$simpleV;
        $this->mimeType = clone MbqMain::$simpleV;
        
        $this->oMbqEtUser = NULL;
    }
    
    /**
     * judge if this is forum post att
     *
     * @return  Boolean
     */
    public function isForumPostAtt() {
        return ($this->attType->oriValue == MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.attType.range.forumPostAtt')) ? true : false;
    }
    
    /**
     * judge if this is user avatar
     *
     * @return  Boolean
     */
    public function isUserAvatar() {
        return ($this->attType->oriValue == MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.attType.range.userAvatar')) ? true : false;
    }
    
    /**
     * judge if this is private conversation att
     *
     * @return  Boolean
     */
    public function isPcMsgAtt() {
        return ($this->attType->oriValue == MbqBaseFdt::getFdt('MbqFdtAtt.MbqEtAtt.attType.range.pcMsgAtt')) ? true : false;
    }
  
}

?>
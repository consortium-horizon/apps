<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum class
 * 
 * @since  2012-7-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtForum extends MbqBaseEntity {
    
    public $forumId;
    public $forumName;
    public $description;
    public $totalTopicNum;  /* Total number of topics in this forum */
    public $totalPostNum;   /* Post count in this forum.  */
    public $parentId;   /* parent's forum ID of this forum, returns -1 if this forum is the root forum */
    public $logoUrl;    /* Forum icon url */
    public $newPost;    /* returns true if this forum contains unread topic */
    public $isProtected;    /* Forum is password protected or not */
    public $isSubscribed;   /* return true if this forum was subscribed by current user */
    public $canSubscribe;   /* return true if current user can subscribe this forum. Default as true for member. */
    public $url;    /* if it contains a url, it means this forum is just a link to other webpage */
    public $subOnly;    /* Forum contains sub forums only or not */
    public $canPost;    /* return false if user cannot create new topic in this forum */
    public $unreadTopicNum;     /* Unread topic count in this forum. Return -1 if the forum contain unread topic but can not calculate the count */
    public $unreadStickyCount;
    public $unreadAnnounceCount;
    public $requirePrefix;
    public $prefixes;   /* prefixes array.for example:array(array('id'=>1,'name'=>'prefix1'), array('id'=>2,'name'=>'prefix2')) */
    public $canUpload;  /* return true if the user has authority to upload attachments in this sub-forum. */
    
    public $oParentMbqEtForum;  /* parent forum */
    public $objsSubMbqEtForum;  /* sub forums */
    
    public function __construct() {
        parent::__construct();
        $this->forumId = clone MbqMain::$simpleV;
        $this->forumName = clone MbqMain::$simpleV;
        $this->description = clone MbqMain::$simpleV;
        $this->totalTopicNum = clone MbqMain::$simpleV;
        $this->totalPostNum = clone MbqMain::$simpleV;
        $this->parentId = clone MbqMain::$simpleV;
        $this->logoUrl = clone MbqMain::$simpleV;
        $this->newPost = clone MbqMain::$simpleV;
        $this->isProtected = clone MbqMain::$simpleV;
        $this->isSubscribed = clone MbqMain::$simpleV;
        $this->canSubscribe = clone MbqMain::$simpleV;
        $this->url = clone MbqMain::$simpleV;
        $this->subOnly = clone MbqMain::$simpleV;
        $this->canPost = clone MbqMain::$simpleV;
        $this->unreadTopicNum = clone MbqMain::$simpleV;
        $this->unreadStickyCount = clone MbqMain::$simpleV;
        $this->unreadAnnounceCount = clone MbqMain::$simpleV;
        $this->requirePrefix = clone MbqMain::$simpleV;
        $this->prefixes = clone MbqMain::$simpleV;
        $this->canUpload = clone MbqMain::$simpleV;
        
        $this->oParentMbqEtForum = NULL;
        $this->objsSubMbqEtForum = array();
    }
  
}

?>
<?php

defined('MBQ_IN_IT') or exit;

/**
 * private conversation message class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtPcMsg extends MbqBaseEntity {
    
    public $msgId;
    public $convId;         /* private conversation id */
    public $msgTitle;
    public $msgContent;
    public $msgAuthorId;
    public $isUnread;       /* return true if this message is not yet read by the user. Useful for app to scroll to first unread when entering a conversation. */
    public $hasLeft;        /* return true if user has left this conversation. Default is "false" if this field is missing */
    public $postTime;       /* timestamp. message creation time. */
    public $newPost;
    
    public $oMbqEtPc;
    public $oAuthorMbqEtUser;
    public $objsMbqEtAtt;           /* the all attachment objs in this post. */
    public $objsNotInContentMbqEtAtt;   /* the attachement objs not in the content of this post. */
    
    public function __construct() {
        parent::__construct();
        $this->msgId = clone MbqMain::$simpleV;
        $this->convId = clone MbqMain::$simpleV;
        $this->msgTitle = clone MbqMain::$simpleV;
        $this->msgContent = clone MbqMain::$simpleV;
        $this->msgAuthorId = clone MbqMain::$simpleV;
        $this->isUnread = clone MbqMain::$simpleV;
        $this->hasLeft = clone MbqMain::$simpleV;
        $this->postTime = clone MbqMain::$simpleV;
        $this->newPost = clone MbqMain::$simpleV;
        
        $this->oMbqEtPc = NULL;
        $this->oAuthorMbqEtUser = NULL;
        $this->objsMbqEtAtt = array();
        $this->objsNotInContentMbqEtAtt = array();
    }
  
}

?>
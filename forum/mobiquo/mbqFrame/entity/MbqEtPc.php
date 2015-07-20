<?php

defined('MBQ_IN_IT') or exit;

/**
 * private conversation class
 * 
 * @since  2012-7-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtPc extends MbqBaseEntity {
    
    public $convId;         /* private conversation id */
    public $userNames;      /* To support creating a new conversation with multiple recipients, the app constructs an array and insert user_name for each recipient as an element inside the array. */
    public $convTitle;
    public $convContent;
    public $totalMessageNum;     /* reply count + 1 */
    public $participantCount;   /* returns total number of users participated in this conversation. */
    public $startUserId;    
    public $startConvTime;  /* The time-stamp when the first message in this conversation was sent. */
    public $lastUserId;     /* The last poster id of this conversation */
    public $lastConvTime;   /* The time-stamp when the last message in this conversation was sent. */
    public $newPost;        /* Indicate if the conversation has unread message */
    public $canInvite;  /* return true if user can invite new members to this conversation */
    public $canEdit;    /* return true if user can edit this conversation title. Default is false if this field is missing. */
    public $canClose;   /* return true if user can close this conversation. Default is "false" if this field is missing. */
    public $isClosed;   /* Returns true if this conversation has been closed. Default is "false" if this field is missing. */
    public $deleteMode; /* 1 means only support soft-delete,2 means only support hard-delete,3 means support both soft-delete and hard-delete. */
    
    public $firstMsgId; /* the first private conversation message id */
    
    public $objsRecipientMbqEtUser;   /* users be invited to join this private conversation */
    public $objsMbqEtPcMsg;
    public $oFirstMbqEtPcMsg;
    
    public function __construct() {
        parent::__construct();
        $this->convId = clone MbqMain::$simpleV;
        $this->userNames = clone MbqMain::$simpleV;
        $this->convTitle = clone MbqMain::$simpleV;
        $this->convContent = clone MbqMain::$simpleV;
        $this->totalMessageNum = clone MbqMain::$simpleV;
        $this->participantCount = clone MbqMain::$simpleV;
        $this->startUserId = clone MbqMain::$simpleV;
        $this->startConvTime = clone MbqMain::$simpleV;
        $this->lastUserId = clone MbqMain::$simpleV;
        $this->lastConvTime = clone MbqMain::$simpleV;
        $this->newPost = clone MbqMain::$simpleV;
        $this->canInvite = clone MbqMain::$simpleV;
        $this->canEdit = clone MbqMain::$simpleV;
        $this->canClose = clone MbqMain::$simpleV;
        $this->isClosed = clone MbqMain::$simpleV;
        $this->deleteMode = clone MbqMain::$simpleV;
        
        $this->firstMsgId = clone MbqMain::$simpleV;
        
        $this->objsRecipientMbqEtUser = array();
        $this->objsMbqEtPcMsg = array();
        $this->oFirstMbqEtPcMsg = NULL;
    }
  
}

?>
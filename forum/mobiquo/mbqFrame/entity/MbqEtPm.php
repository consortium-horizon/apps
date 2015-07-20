<?php

defined('MBQ_IN_IT') or exit;

/**
 * private message class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtPm extends MbqBaseEntity {
    
    public $msgId;         /* private message id */
    public $toMsgId;        /* It is used in conjunction with "action" parameter to indicate which PM is being replied or forwarded to. */
    public $userNames;      /* To support sending message to multiple recipients, the app constructs an array and insert user_name for each recipient as an element inside the array. */
    public $msgTitle;
    public $msgContent;
    public $shortContent;
    public $isRead; /* boolean,read or unread */
    public $isReply;  /* boolean */
    public $isForward;  /* boolean */
    public $sentDate;
    public $msgFromId;  /* user id of the message sender */
    public $msgFrom;  /* username of the message sender. this field is not required if the current box is a Sent box */
    public $isOnline;
    public $iconUrl;
    public $allowSmilies;
    public $boxId;
    
    public $oMbqEtPmBox;
    public $oFirstRecipientMbqEtUser;
    public $oAuthorMbqEtUser;
    public $objsRecipientMbqEtUser;   /* users be invited to join this private message */
    
    public function __construct() {
        parent::__construct();
        $this->msgId = clone MbqMain::$simpleV;
        $this->toMsgId = clone MbqMain::$simpleV;
        $this->userNames = clone MbqMain::$simpleV;
        $this->msgTitle = clone MbqMain::$simpleV;
        $this->msgContent = clone MbqMain::$simpleV;
        $this->shortContent = clone MbqMain::$simpleV;
        $this->isRead = clone MbqMain::$simpleV;
        $this->isReply = clone MbqMain::$simpleV;
        $this->isForward = clone MbqMain::$simpleV;
        $this->sentDate = clone MbqMain::$simpleV;
        $this->msgFromId = clone MbqMain::$simpleV;
        $this->msgFrom = clone MbqMain::$simpleV;
        $this->isOnline = clone MbqMain::$simpleV;
	$this->iconUrl = clone MbqMain::$simpleV;
        $this->allowSmilies = clone MbqMain::$simpleV;
        $this->boxId = clone MbqMain::$simpleV;
        
        $this->oMbqEtPmBox = NULL;
        $this->oFirstRecipientMbqEtUser = NULL;
        $this->oAuthorMbqEtUser = NULL;
        $this->objsRecipientMbqEtUser = array();
    }
  
}

?>
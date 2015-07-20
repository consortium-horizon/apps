<?php

defined('MBQ_IN_IT') or exit;

/**
 * private message box class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtPmBox extends MbqBaseEntity {
    
    public $boxId;
    public $boxName;
    public $msgCount;   /* number of messages in this box. */
    public $unreadCount;    /* number of unread messages in this box. */
    public $boxType;    /* Optional. returns "INBOX" if it is inbox. Returns "SENT" if it is a sent-box. */
    
    public function __construct() {
        parent::__construct();
        $this->boxId = clone MbqMain::$simpleV;
        $this->boxName = clone MbqMain::$simpleV;
        $this->msgCount = clone MbqMain::$simpleV;
        $this->unreadCount = clone MbqMain::$simpleV;
        $this->boxType = clone MbqMain::$simpleV;
    }
    
    /**
     * judge is a sent box
     *
     * @return  Boolean
     */
    public function isSentBox() {
        if ($this->boxType->hasSetOriValue() && $this->boxType->oriValue == MbqBaseFdt::getFdt('MbqFdtPm.MbqEtPmBox.boxType.range.sent')) {
            return true;
        }
        return false;
    }
  
}

?>
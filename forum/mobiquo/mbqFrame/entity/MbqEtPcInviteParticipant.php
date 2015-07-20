<?php

defined('MBQ_IN_IT') or exit;

/**
 * private conversation invite participant class
 * 
 * @since  2012-7-17
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtPcInviteParticipant extends MbqBaseEntity {
    
    public $convId;         /* private conversation id */
    public $userNames;      /* To support creating a new conversation with multiple recipients, the app constructs an array and insert user_name for each recipient as an element inside the array. */
    public $inviteReasonText;   /* reason to display to invitees */
    
    public $oMbqEtPc;
    public $objsMbqEtUser;
    
    public function __construct() {
        parent::__construct();
        $this->convId = clone MbqMain::$simpleV;
        $this->userNames = clone MbqMain::$simpleV;
        $this->inviteReasonText = clone MbqMain::$simpleV;
        
        $this->oMbqEtPc = NULL;
        $this->objsMbqEtUser = array();
    }
  
}

?>
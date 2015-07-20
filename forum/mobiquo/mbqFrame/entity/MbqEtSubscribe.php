<?php

defined('MBQ_IN_IT') or exit;

/**
 * subscribe class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtSubscribe extends MbqBaseEntity {
    
    public $subscribeMode;      /* Return the notification mode of the subscribed object. Notification Type(sample for vb): 0 = no email notification(Or Through my control panel only) 1 = Instant notification by email 2 = Daily updates by email 3 = Weekly updates by email */
    public $key;    /* now only forumId/topicId/all */
    public $userId; /* user id who subscribed this */
    public $type;   /* subscribe forum/topic or other anything */
    
    public $oMbqEtUser; /* user who subscribed this */
    
    public function __construct() {
        parent::__construct();
        $this->subscribeMode = clone MbqMain::$simpleV;
        $this->key = clone MbqMain::$simpleV;
        $this->userId = clone MbqMain::$simpleV;
        $this->type = clone MbqMain::$simpleV;
        
        $this->oMbqEtUser = NULL;
    }
  
}

?>
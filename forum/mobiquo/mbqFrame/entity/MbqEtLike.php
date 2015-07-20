<?php

defined('MBQ_IN_IT') or exit;

/**
 * like class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtLike extends MbqBaseEntity {
    
    public $key;    /* topicId or postId */
    public $userId; /* user id who liked this */
    public $type;   /* like forum topic/post or other anything */
    public $postTime;   /* timestamp */
    
    public $oMbqEtUser; /* user who like this */
    
    public function __construct() {
        parent::__construct();
        $this->key = clone MbqMain::$simpleV;
        $this->userId = clone MbqMain::$simpleV;
        $this->type = clone MbqMain::$simpleV;
        $this->postTime = clone MbqMain::$simpleV;
        
        $this->oMbqEtUser = NULL;
    }
  
}

?>
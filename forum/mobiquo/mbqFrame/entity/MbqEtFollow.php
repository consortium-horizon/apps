<?php

defined('MBQ_IN_IT') or exit;

/**
 * follow class
 * 
 * @since  2012-7-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtFollow extends MbqBaseEntity {
    
    public $userIdFrom;    /* user id who follow to another user */
    public $userIdTo; /* user id who be followed */
    
    public function __construct() {
        parent::__construct();
        $this->userIdFrom = clone MbqMain::$simpleV;
        $this->userIdTo = clone MbqMain::$simpleV;
    }
  
}

?>
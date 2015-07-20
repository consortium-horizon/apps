<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActGetOnlineUsers');

/**
 * get_online_users action
 * 
 * @since  2012-9-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActGetOnlineUsers extends MbqBaseActGetOnlineUsers {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        parent::actionImplement();
    }
  
}

?>
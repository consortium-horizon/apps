<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActGetThreadByUnread');

/**
 * get_thread_by_unread action
 * 
 * @since  2013-8-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActGetThreadByUnread extends MbqBaseActGetThreadByUnread {
    
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
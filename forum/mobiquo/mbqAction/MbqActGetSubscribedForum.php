<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActGetSubscribedForum');

/**
 * get_subscribed_forum action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActGetSubscribedForum extends MbqBaseActGetSubscribedForum {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        $this->data['total_forums_num'] = 0;
        $this->data['forums'] = array();
    }
  
}

?>
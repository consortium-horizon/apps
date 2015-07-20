<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActGetUnreadTopic');

/**
 * get_unread_topic action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActGetUnreadTopic extends MbqBaseActGetUnreadTopic {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        if (!MbqMain::$oMbqAppEnv->check3rdPluginEnabled('AllViewed')) {
            parent::actionImplement();
        } else {
            //the AllViewed plugin has conflict with our plugin,so need disable this method
            $this->data['result'] = true;
            $this->data['total_topic_num'] = 0;
            $this->data['topics'] = array();
        }
    }
  
}

?>
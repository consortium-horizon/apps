<?php

defined('MBQ_IN_IT') or exit;

/**
 * mark_all_as_read action
 * 
 * @since  2012-8-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActMarkAllAsRead extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
        if ($oMbqAclEtForumTopic->canAclMarkAllAsRead()) {    //acl judge
            $oMbqWrEtForumTopic = MbqMain::$oClk->newObj('MbqWrEtForumTopic');
            $oMbqWrEtForumTopic->markForumTopicRead($dummy = NULL, array('case' => 'markAllAsRead'));
            $this->data['result'] = true;
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>
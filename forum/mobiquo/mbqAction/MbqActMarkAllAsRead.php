<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActMarkAllAsRead');

/**
 * mark_all_as_read action
 * 
 * @since  2012-8-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActMarkAllAsRead extends MbqBaseActMarkAllAsRead {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        //the AllViewed plugin has conflict with our plugin,so need disable this method
        MbqError::alert('', "Sorry!This feature is not available in this forum.Method name:".MbqMain::$cmd, '', MBQ_ERR_NOT_SUPPORT);
        if (!MbqMain::$oMbqAppEnv->check3rdPluginEnabled('AllViewed')) {
            MbqError::alert('', "Sorry!This feature is not available in this forum.Method name:".MbqMain::$cmd, '', MBQ_ERR_NOT_SUPPORT);
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
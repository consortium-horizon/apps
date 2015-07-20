<?php

defined('MBQ_IN_IT') or exit;

/**
 * subscribe_forum action
 * 
 * @since  2012-9-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActSubscribeForum extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('subscribe')) {
            MbqError::alert('', "Not support module subscribe!", '', MBQ_ERR_NOT_SUPPORT);
        }
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $forumId = MbqMain::$input[0];
        $subscribeMode = MbqMain::$input[1];
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForum = MbqMain::$oClk->newObj('MbqAclEtForum');
            if ($oMbqAclEtForum->canAclSubscribeForum($oMbqEtForum)) {  //acl judge
                $oMbqWrEtForum = MbqMain::$oClk->newObj('MbqWrEtForum');
                $oMbqWrEtForum->subscribeForum($oMbqEtForum);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
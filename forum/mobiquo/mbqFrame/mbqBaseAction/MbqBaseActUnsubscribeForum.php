<?php

defined('MBQ_IN_IT') or exit;

/**
 * unsubscribe_forum action
 * 
 * @since  2012-9-15
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActUnsubscribeForum extends MbqBaseAct {
    
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
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForum = MbqMain::$oClk->newObj('MbqAclEtForum');
            if ($oMbqAclEtForum->canAclUnsubscribeForum($oMbqEtForum)) {  //acl judge
                $oMbqWrEtForum = MbqMain::$oClk->newObj('MbqWrEtForum');
                $oMbqWrEtForum->unsubscribeForum($oMbqEtForum);
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
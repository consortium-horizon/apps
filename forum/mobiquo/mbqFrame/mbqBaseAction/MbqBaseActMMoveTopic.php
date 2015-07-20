<?php

defined('MBQ_IN_IT') or exit;

/**
 * m_move_topic action
 * 
 * @since  2012-9-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActMMoveTopic extends MbqBaseAct {
    
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
        $topicId = MbqMain::$input[0];
        $forumId = MbqMain::$input[1];
        $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($topicId, array('case' => 'byTopicId'));
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($oMbqEtForumTopic && $objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            if ($oMbqAclEtForumTopic->canAclMMoveTopic($oMbqEtForumTopic, $oMbqEtForum)) {    //acl judge
                $oMbqWrEtForumTopic = MbqMain::$oClk->newObj('MbqWrEtForumTopic');
                $oMbqWrEtForumTopic->mMoveTopic($oMbqEtForumTopic, $oMbqEtForum);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid topic id or forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
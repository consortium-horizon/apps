<?php

defined('MBQ_IN_IT') or exit;

/**
 * m_move_post action
 * 
 * @since  2012-9-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActMMovePost extends MbqBaseAct {
    
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
        $postId = MbqMain::$input[0];
        $topicId = MbqMain::$input[1];
        $topicTitle = MbqMain::$input[2];
        $forumId = MbqMain::$input[3];
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
        $oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($postId, array('case' => 'byPostId'));
        $oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($topicId, array('case' => 'byTopicId'));
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($oMbqEtForumPost && (($oMbqEtForum = $objsMbqEtForum[0]) || $oMbqEtForumTopic)) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclMMovePost($oMbqEtForumPost, $oMbqEtForum, $oMbqEtForumTopic)) {    //acl judge
                $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                $oMbqWrEtForumPost->mMovePost($oMbqEtForumPost, $oMbqEtForum, $oMbqEtForumTopic, $topicTitle);
                $this->data['result'] = true;
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid param!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
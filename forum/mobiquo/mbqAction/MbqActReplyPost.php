<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActReplyPost');

/**
 * reply_post action
 * 
 * @since  2012-8-20
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActReplyPost extends MbqBaseActReplyPost {
    
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
        $oMbqEtForumPost = MbqMain::$oClk->newObj('MbqEtForumPost');
        $oMbqEtForumPost->forumId->setOriValue(MbqMain::$input[0]);
        $oMbqEtForumPost->topicId->setOriValue(MbqMain::$input[1]);
        $oMbqEtForumPost->postTitle->setOriValue(MbqMain::$input[2]);
        $oMbqEtForumPost->postContent->setOriValue(MbqMain::$input[3]);
        if (isset(MbqMain::$input[4])) $oMbqEtForumPost->attachmentIdArray->setOriValue((array) MbqMain::$input[4]);
        if (isset(MbqMain::$input[5])) $oMbqEtForumPost->groupId->setOriValue(MbqMain::$input[5]);
        $returnHtml = (boolean) MbqMain::$input[6];
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($oMbqEtForumPost->forumId->oriValue), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            if ($oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($oMbqEtForumPost->topicId->oriValue, array('case' => 'byTopicId'))) {
                if ($oMbqEtForumTopic->topicId->oriValue == $oMbqEtForumPost->topicId->oriValue && $oMbqEtForumTopic->forumId->oriValue == $oMbqEtForum->forumId->oriValue) {
                    $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
                    if ($oMbqAclEtForumPost->canAclReplyPost($oMbqEtForumTopic)) {   //acl judge
                        $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                        $oMbqWrEtForumPost->addMbqEtForumPost($oMbqEtForumPost);
                        $state = $oMbqEtForumPost->state->oriValue;
                        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
                        if($oMbqEtForumPost->postId->oriValue)
                        {
                            $this->data['result'] = true;
                            $this->data['post_id'] = (string) $oMbqEtForumPost->postId->oriValue;
                            $this->data['state'] = (int) $state;
                        }
                        else
                        {
                            $this->data['result'] = false;
                            $this->data['result_text'] = 'You cannot reply at this moment';
                        }
                    } else {
                        MbqError::alert('', '', '', MBQ_ERR_APP);
                    }
                } else {
                    MbqError::alert('', "Data error!", '', MBQ_ERR_APP);
                }
            } else {
                MbqError::alert('', "Need valid topic id!", '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
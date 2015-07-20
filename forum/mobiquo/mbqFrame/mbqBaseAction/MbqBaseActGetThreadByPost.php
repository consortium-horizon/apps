<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_thread_by_post action
 * 
 * @since  2013-7-12
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetThreadByPost extends MbqBaseAct {
    
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
        $postsPerRequest = (int) MbqMain::$input[1];
        $postsPerRequest = $postsPerRequest ? $postsPerRequest : 20;
        $returnHtml = (boolean) MbqMain::$input[2];
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($postId, array('case' => 'byPostId'))) {
            $topicId = $oMbqEtForumPost->topicId->oriValue;
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByPositionAndPerPage($oMbqEtForumPost->position->oriValue, $postsPerRequest);
        $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
        if ($oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($topicId, array('case' => 'byTopicId'))) {
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            if ($oMbqAclEtForumTopic->canAclGetThread($oMbqEtForumTopic)) {    //acl judge
                $oMbqDataPage = $oMbqRdEtForumPost->getObjsMbqEtForumPost($oMbqEtForumTopic, array('case' => 'byTopic', 'oMbqDataPage' => $oMbqDataPage));
                $this->data = $oMbqRdEtForumTopic->returnApiDataForumTopic($oMbqEtForumTopic);
                $this->data['position'] = (int) $oMbqEtForumPost->position->oriValue;
                $this->data['forum_name'] = (string) $oMbqEtForumTopic->oMbqEtForum->forumName->oriValue;
                $this->data['can_upload'] = (boolean) $oMbqEtForumTopic->oMbqEtForum->canUpload->oriValue;
                $this->data['posts'] = $oMbqRdEtForumPost->returnApiArrDataForumPost($oMbqDataPage->datas, $returnHtml);
                $oMbqWrEtForumTopic = MbqMain::$oClk->newObj('MbqWrEtForumTopic');
                /* add forum topic view num */
                $oMbqWrEtForumTopic->addForumTopicViewNum($oMbqEtForumTopic);
                /* mark forum topic read */
                $oMbqWrEtForumTopic->markForumTopicRead($oMbqEtForumTopic);
                /* reset forum topic subscription */
                $oMbqWrEtForumTopic->resetForumTopicSubscription($oMbqEtForumTopic);
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid topic id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
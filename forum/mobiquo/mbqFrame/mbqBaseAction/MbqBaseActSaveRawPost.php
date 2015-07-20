<?php

defined('MBQ_IN_IT') or exit;

/**
 * save_raw_post action
 * 
 * @since  2012-9-12
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActSaveRawPost extends MbqBaseAct {
    
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
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($postId, array('case' => 'byPostId'))) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclSaveRawPost($oMbqEtForumPost)) {   //acl judge
                $oMbqEtForumPost->postTitle->setOriValue(MbqMain::$input[1]);
                $oMbqEtForumPost->postContent->setOriValue(MbqMain::$input[2]);
                $returnHtml = (boolean) MbqMain::$input[3];
                $prefixId = MbqMain::$input[4];
                $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                $oMbqWrEtForumPost->mdfMbqEtForumPost($oMbqEtForumPost, array('case' => 'edit'));
                $state = $oMbqEtForumPost->state->oriValue;
                //reload post
                if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($oMbqEtForumPost->postId->oriValue, array('case' => 'byPostId'))) {
                    $this->data['result'] = true;
                    $data1 = $oMbqRdEtForumPost->returnApiDataForumPost($oMbqEtForumPost, $returnHtml);
                    MbqMain::$oMbqCm->mergeApiData($this->data, $data1);
                    $this->data['state'] = $state;
                } else {
                    MbqError::alert('', "Can not load modified post!", '', MBQ_ERR_APP);
                }
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
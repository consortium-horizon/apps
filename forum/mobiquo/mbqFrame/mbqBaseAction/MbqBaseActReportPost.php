<?php

defined('MBQ_IN_IT') or exit;

/**
 * report_post action
 * 
 * @since  2012-9-23
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActReportPost extends MbqBaseAct {
    
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
        $oMbqEtForumReportPost = MbqMain::$oClk->newObj('MbqEtForumReportPost');
        $oMbqEtForumReportPost->postId->setOriValue(MbqMain::$input[0]);
        $oMbqEtForumReportPost->reason->setOriValue(MbqMain::$input[1]);
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($oMbqEtForumReportPost->postId->oriValue, array('case' => 'byPostId'))) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclReportPost($oMbqEtForumPost)) {    //acl judge
                $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                $oMbqWrEtForumPost->reportPost($oMbqEtForumPost, $oMbqEtForumReportPost);
                $this->data['result'] = true;
            } else {
                $this->data['result'] = true;   //for dummy report post
                //MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
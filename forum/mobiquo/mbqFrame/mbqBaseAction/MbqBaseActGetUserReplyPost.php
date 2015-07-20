<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_user_reply_post action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetUserReplyPost extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('user')) {
            MbqError::alert('', "Not support module user!", '', MBQ_ERR_NOT_SUPPORT);
        }
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $userName = MbqMain::$input[0];
        $userId = MbqMain::$input[1];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast(0, 49);
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        if ($userId) {
            $oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userId, array('case' => 'byUserId'));
        } else {
            $oMbqEtUser = $oMbqRdEtUser->initOMbqEtUser($userName, array('case' => 'byLoginName'));
        }
        if ($oMbqEtUser) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclGetUserReplyPost()) {   //acl judge
                $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
                $oMbqDataPage = $oMbqRdEtForumPost->getObjsMbqEtForumPost($oMbqEtUser, array('case' => 'byReplyUser', 'oMbqDataPage' => $oMbqDataPage));
                $this->data = $oMbqRdEtForumPost->returnApiArrDataForumPost($oMbqDataPage->datas);
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid user key!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
<?php

defined('MBQ_IN_IT') or exit;

/**
 * thank_post action
 * 
 * @since  2012-9-24
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActThankPost extends MbqBaseAct {
    
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
        $oMbqEtThank = MbqMain::$oClk->newObj('MbqEtThank');
        $oMbqEtThank->key->setOriValue(MbqMain::$input[0]);
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($oMbqEtThank->key->oriValue, array('case' => 'byPostId'))) {
            $oMbqAclEtForumPost = MbqMain::$oClk->newObj('MbqAclEtForumPost');
            if ($oMbqAclEtForumPost->canAclThankPost($oMbqEtForumPost)) {    //acl judge
                $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
                $oMbqEtThank->userId->setOriValue(MbqMain::$oCurMbqEtUser->userId->oriValue);
                $oMbqWrEtForumPost->thankPost($oMbqEtForumPost, $oMbqEtThank);
                $this->data['result'] = true;
                $oTapatalkPush = new TapatalkPush();
                $oTapatalkPush->callMethod('doPushThank', array(
                    'oMbqEtForumPost' => $oMbqEtForumPost,
                    'oMbqEtThank' => $oMbqEtThank
                ));
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid post id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
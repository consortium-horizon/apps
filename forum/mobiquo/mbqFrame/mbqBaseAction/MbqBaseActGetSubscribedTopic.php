<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_subscribed_topic action
 * 
 * @since  2012-8-16
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetSubscribedTopic extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
        $this->supportLevels = array(4);
        $this->currLevel = 4;
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
        $startNum = (int) MbqMain::$input[0];
        $lastNum = (int) MbqMain::$input[1];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
        if ($oMbqAclEtForumTopic->canAclGetSubscribedTopic()) {     //acl judge
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic(MbqMain::$oCurMbqEtUser->userId->oriValue, array('case' => 'subscribed', 'oMbqDataPage' => $oMbqDataPage));
            $this->data['total_topic_num'] = (int) $oMbqDataPage->totalNum;
            $this->data['topics'] = $oMbqRdEtForumTopic->returnApiArrDataForumTopic($oMbqDataPage->datas);
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>
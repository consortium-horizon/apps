<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_participated_topic action
 * 
 * @since  2012-8-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetParticipatedTopic extends MbqBaseAct {
    
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
        $startNum = (int) MbqMain::$input[1];
        $lastNum = (int) MbqMain::$input[2];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $filter = array(
            'searchuser' => MbqMain::$input[0],
            'userid' => MbqMain::$input[4],
            'searchid' => MbqMain::$input[3],
            'page' => $oMbqDataPage->curPage,
            'perpage' => $oMbqDataPage->numPerPage
        );
        $filter['showposts'] = 0;
        $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
        if ($oMbqAclEtForumTopic->canAclGetParticipatedTopic()) {    //acl judge
            $oMbqRdForumSearch = MbqMain::$oClk->newObj('MbqRdForumSearch');
            $oMbqDataPage = $oMbqRdForumSearch->forumAdvancedSearch($filter, $oMbqDataPage, array('case' => 'getParticipatedTopic', 'participated' => true));
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            $this->data['result'] = true;
            $this->data['total_topic_num'] = (int) $oMbqDataPage->totalNum;
            $this->data['total_unread_num'] = (int) $oMbqDataPage->totalUnreadNum;
            $this->data['topics'] = $oMbqRdEtForumTopic->returnApiArrDataForumTopic($oMbqDataPage->datas);
        } else {
            MbqError::alert('', '', '', MBQ_ERR_APP);
        }
    }
  
}

?>
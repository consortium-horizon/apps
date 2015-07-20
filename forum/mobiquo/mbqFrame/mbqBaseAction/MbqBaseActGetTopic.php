<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_topic action
 * 
 * @since  2012-8-7
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetTopic extends MbqBaseAct {
    
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
        $forumId = MbqMain::$input[0];
        $startNum = (int) MbqMain::$input[1];
        $lastNum = (int) MbqMain::$input[2];
        $mode = MbqMain::$input[3];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByStartAndLast($startNum, $lastNum);
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            if ($oMbqAclEtForumTopic->canAclGetTopic($oMbqEtForum)) {    //acl judge
                switch ($mode) {
                    case 'TOP':     /* returns sticky topics. */
                    $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
                    $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPage, 'top' => true));
                    $this->data = $oMbqRdEtForum->returnApiDataForum($oMbqEtForum);
                    $this->data['total_topic_num'] = (int) $oMbqDataPage->totalNum;
                    $this->data['topics'] = $oMbqRdEtForumTopic->returnApiArrDataForumTopic($oMbqDataPage->datas);
                    break;
                    case 'ANN':     /* returns "Announcement" topics.TODO */
                    $this->data = $oMbqRdEtForum->returnApiDataForum($oMbqEtForum);
                    $this->data['total_topic_num'] = (int) 0;
                    $this->data['topics'] = array();
                    break;
                    default:        /* returns standard topics */
                    $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
                    $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPage, 'notIncludeTop' => true));
                    $this->data = $oMbqRdEtForum->returnApiDataForum($oMbqEtForum);
                    $this->data['total_topic_num'] = (int) $oMbqDataPage->totalNum;
                    $this->data['topics'] = $oMbqRdEtForumTopic->returnApiArrDataForumTopic($oMbqDataPage->datas);
                    break;
                }
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
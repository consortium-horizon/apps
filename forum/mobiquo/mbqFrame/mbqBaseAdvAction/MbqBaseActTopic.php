<?php

defined('MBQ_IN_IT') or exit;

/**
 * topic action
 * 
 * @since  2013-5-11
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActTopic extends MbqBaseAct {
    
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
        $pid = MbqMain::$input['get']['pid'];
        $tid = MbqMain::$input['get']['tid'];
        $uid = MbqMain::$input['get']['uid'];
        $goto = MbqMain::$input['get']['goto'];
        $page = (int) MbqMain::$input['get']['page'];
        $perpage = (int) MbqMain::$input['get']['perpage'];
        $order = MbqMain::$input['get']['order'] ? MbqMain::$input['get']['order'] : 'asc';
        $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
        if ($pid) {
            MbqError::alert('', "Not supported pid:$pid.", '', MBQ_ERR_APP);
        } elseif ($tid) {
            if ($oMbqEtForumTopic = $oMbqRdEtForumTopic->initOMbqEtForumTopic($tid, array('case' => 'byTopicId'))) {
                $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
                if (!$oMbqAclEtForumTopic->canAclGetThread($oMbqEtForumTopic)) {    //acl judge
                    MbqError::alert('', '', '', MBQ_ERR_APP);
                }
            } else {
                MbqError::alert('', "Need valid topic id!", '', MBQ_ERR_APP);
            }
            if ($uid) MbqError::alert('', "Not supported uid:$uid.", '', MBQ_ERR_APP);
            if ($goto) MbqError::alert('', "Not supported goto:$goto.", '', MBQ_ERR_APP);
            $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
            $oMbqDataPage->initByPageAndPerPage($page, $perpage);
            if ($order == 'asc') {
                $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
                $oMbqDataPage = $oMbqRdEtForumPost->getObjsMbqEtForumPost($oMbqEtForumTopic, array('case' => 'byTopic', 'oMbqDataPage' => $oMbqDataPage));
                $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
                $this->data['navi'] = $oMbqRdEtForum->returnApiTreeDataForum($oMbqEtForumTopic->objsBreadcrumbMbqEtForum);
                $this->data['topic'] = $oMbqRdEtForumTopic->returnApiDataForumTopic($oMbqEtForumTopic);
                $this->data['posts'] = $oMbqRdEtForumPost->returnApiArrDataForumPost($oMbqDataPage->datas);
                $oMbqWrEtForumTopic = MbqMain::$oClk->newObj('MbqWrEtForumTopic');
                /* add forum topic view num */
                $oMbqWrEtForumTopic->addForumTopicViewNum($oMbqEtForumTopic);
                /* mark forum topic read */
                $oMbqWrEtForumTopic->markForumTopicRead($oMbqEtForumTopic);
                /* reset forum topic subscription */
                $oMbqWrEtForumTopic->resetForumTopicSubscription($oMbqEtForumTopic);
            } else {
                MbqError::alert('', "Not supported order:$order.", '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need pid or tid.", '', MBQ_ERR_APP);
        }
    }
  
}

?>
<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum action
 * 
 * @since  2013-5-11
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActForum extends MbqBaseAct {
    
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
        $forumId = MbqMain::$input['get']['fid'];
        $content = MbqMain::$input['get']['content'] ? MbqMain::$input['get']['content'] : 'both';
        $page = (int) MbqMain::$input['get']['page'];
        $perpage = (int) MbqMain::$input['get']['perpage'];
        $type = MbqMain::$input['get']['type'] ? MbqMain::$input['get']['type'] : 'normal';
        $prefix = MbqMain::$input['get']['prefix'];
        $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
        $oMbqDataPage->initByPageAndPerPage($page, $perpage);
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $objsMbqEtForum = $oMbqRdEtForum->getObjsMbqEtForum(array($forumId), array('case' => 'byForumIds'));
        if ($objsMbqEtForum && ($oMbqEtForum = $objsMbqEtForum[0])) {
            $oMbqAclEtForumTopic = MbqMain::$oClk->newObj('MbqAclEtForumTopic');
            if ($oMbqAclEtForumTopic->canAclGetTopic($oMbqEtForum)) {    //acl judge
                if ($content == 'sub' || $content == 'both') {
                    $objsSubMbqEtForum = $oMbqRdEtForum->getObjsSubMbqEtForum($oMbqEtForum->forumId->oriValue);
                } else {
                    $objsSubMbqEtForum = array();
                }
                $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
                switch ($type) {
                    case 'sticky':     /* returns sticky topics. */
                    $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPage, 'top' => true));
                    $totalNum = $oMbqDataPage->totalNum;
                    break;
                    case 'normal':        /* returns standard topics */
                    $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPage, 'notIncludeTop' => true));
                    $totalNum = $oMbqDataPage->totalNum;
                    break;
                    case 'all': /* returns all topics */
                    $oMbqDataPageSticky = MbqMain::$oClk->newObj('MbqDataPage');
                    $oMbqDataPageSticky->initByPageAndPerPage(1, 1000);
                    $oMbqDataPageSticky = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPageSticky, 'top' => true));
                    $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($oMbqEtForum, array('case' => 'byForum', 'oMbqDataPage' => $oMbqDataPage, 'notIncludeTop' => true));
                    $totalNum = $oMbqDataPageSticky->totalNum + $oMbqDataPage->totalNum;
                    /* merge data */
                    $topics = array();
                    foreach ($oMbqDataPageSticky->datas as $v) {
                        $topics[] = $v;
                    }
                    foreach ($oMbqDataPage->datas as $v) {
                        $topics[] = $v;
                    }
                    $oMbqDataPage->datas = $topics;
                    break;
                    default:
                    if ($content != 'sub') {
                        MbqError::alert('', "Unknown topic type filter:$type.", '', MBQ_ERR_APP);
                    }
                    break;
                }
                $this->data['total'] = (int) $totalNum;   //!!! must
                $this->data['forum'] = $oMbqRdEtForum->returnApiDataForum($oMbqEtForum);
                $this->data['forums'] = $oMbqRdEtForum->returnApiTreeDataForum($objsSubMbqEtForum);
                $this->data['topics'] = $oMbqRdEtForumTopic->returnApiArrDataForumTopic($oMbqDataPage->datas);
            } else {
                MbqError::alert('', '', '', MBQ_ERR_APP);
            }
        } else {
            MbqError::alert('', "Need valid forum id!", '', MBQ_ERR_APP);
        }
    }
  
}

?>
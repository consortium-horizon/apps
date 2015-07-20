<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseRdForumSearch');

/**
 * forum search class
 * 
 * @since  2012-8-27
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqRdForumSearch extends MbqBaseRdForumSearch {
    
    public function __construct() {
    }
    
    /**
     * forum advanced search
     *
     * @param  Array  $filter  search filter
     * @param  Object  $oMbqDataPage
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'advanced' means advanced search
     * $mbqOpt['participated'] = true means get participated data
     * $mbqOpt['unread'] = true means get unread data
     * @return  Object  $oMbqDataPage
     */
    public function forumAdvancedSearch($filter, $oMbqDataPage, $mbqOpt) {
        if ($mbqOpt['case'] == 'getLatestTopic' || $mbqOpt['case'] == 'getUnreadTopic' || $mbqOpt['case'] == 'getParticipatedTopic') {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
            $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            $oExttMbqDiscussionModel->Watching = TRUE;
            if ($mbqOpt['case'] == 'getLatestTopic') {
                $arr = $oExttMbqDiscussionModel->exttMbqGetTopics($oMbqDataPage->startNum, $oMbqDataPage->numPerPage, '', NULL);
            } elseif ($mbqOpt['case'] == 'getUnreadTopic') {
                $arr = $oExttMbqDiscussionModel->exttMbqGetTopics($oMbqDataPage->startNum, $oMbqDataPage->numPerPage, '', NULL, array('unread' => true));
            } elseif ($mbqOpt['case'] == 'getParticipatedTopic') {
                $arr = $oExttMbqDiscussionModel->exttMbqGetTopics($oMbqDataPage->startNum, $oMbqDataPage->numPerPage, '', NULL, array('participated' => true));
            }
            $objsStdForumTopic = $arr['data']->Result();
            $oMbqDataPage->totalNum = $arr['total'];
            $newMbqOpt['case'] = 'byObjsStdForumTopic';
            $newMbqOpt['oMbqDataPage'] = $oMbqDataPage;
            $oMbqDataPage = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($objsStdForumTopic, $newMbqOpt);
            return $oMbqDataPage;
        } elseif ($mbqOpt['case'] == 'searchTopic') {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
            $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
            $subSqlCanReadTopicIds = $oExttMbqDiscussionModel->exttMbqGetTopics('', '', '', NULL, array('onlyGetSqlForTopicIds' => true));
            $oCommentModel = new CommentModel();
            $oSql = $oCommentModel->SQL;
            $dbPre = $oSql->Database->DatabasePrefix;
            $subSqlTopic = "select DiscussionID as topicId, DateInserted from ".$dbPre."Discussion as mbqD where mbqD.Name like '%".addslashes($filter['keywords'])."%' or mbqD.Body like '%".addslashes($filter['keywords'])."%' and mbqD.DiscussionID in ($subSqlCanReadTopicIds)";
            $subSqlPost = "select DiscussionID as topicId, DateInserted from ".$dbPre."Comment as mbqC where mbqC.Body like '%".addslashes($filter['keywords'])."%' and mbqC.DiscussionID in ($subSqlCanReadTopicIds)";
            $sqlCount = "select count(topicId) as totalNum from (($subSqlTopic) union all ($subSqlPost)) as data";
            $oMbqDataPage->totalNum = $oSql->Query($sqlCount)->FirstRow()->totalNum;
            $sql = "($subSqlTopic) union all ($subSqlPost) order by DateInserted desc limit $oMbqDataPage->startNum,$oMbqDataPage->numPerPage";
            $records = $oSql->Query($sql)->Result();
            $topicIds = array();
            foreach ($records as $r) {
                $topicIds[] = $r->topicId;
            }
            $oMbqRdEtForumTopic = MbqMain::$oClk->newObj('MbqRdEtForumTopic');
            $oMbqDataPage->datas = $oMbqRdEtForumTopic->getObjsMbqEtForumTopic($topicIds, array('case' => 'byTopicIds'));
            return $oMbqDataPage;
        } elseif ($mbqOpt['case'] == 'searchPost') {
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionModel.php');
            $oExttMbqDiscussionModel = new ExttMbqDiscussionModel();
            $subSqlCanReadTopicIds = $oExttMbqDiscussionModel->exttMbqGetTopics('', '', '', NULL, array('onlyGetSqlForTopicIds' => true));
            $oCommentModel = new CommentModel();
            $oSql = $oCommentModel->SQL;
            $dbPre = $oSql->Database->DatabasePrefix;
            $subSqlTopic = "select concat('topic_', DiscussionID) as postId, DateInserted from ".$dbPre."Discussion as mbqD where mbqD.Name like '%".addslashes($filter['keywords'])."%' or mbqD.Body like '%".addslashes($filter['keywords'])."%' and mbqD.DiscussionID in ($subSqlCanReadTopicIds)";
            $subSqlPost = "select CommentID as postId, DateInserted from ".$dbPre."Comment as mbqC where mbqC.Body like '%".addslashes($filter['keywords'])."%' and mbqC.DiscussionID in ($subSqlCanReadTopicIds)";
            $sqlCount = "select count(postId) as totalNum from (($subSqlTopic) union all ($subSqlPost)) as data";
            $oMbqDataPage->totalNum = $oSql->Query($sqlCount)->FirstRow()->totalNum;
            $sql = "($subSqlTopic) union all ($subSqlPost) order by DateInserted desc limit $oMbqDataPage->startNum,$oMbqDataPage->numPerPage";
            $records = $oSql->Query($sql)->Result();
            $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
            foreach ($records as $r) {
                if ($oMbqEtForumPost = $oMbqRdEtForumPost->initOMbqEtForumPost($r->postId, array('case' => 'byPostId'))) {
                    $oMbqDataPage->datas[] = $oMbqEtForumPost;
                }
            }
            return $oMbqDataPage;
        } elseif ($mbqOpt['case'] == 'advanced') {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_UNKNOWN_CASE);
    }
  
}

?>
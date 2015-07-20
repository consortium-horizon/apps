<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseWrEtForumTopic');

/**
 * forum topic write class
 * 
 * @since  2012-8-15
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqWrEtForumTopic extends MbqBaseWrEtForumTopic {
    
    public function __construct() {
    }
    
    /**
     * add forum topic
     *
     * @param  Mixed  $var($oMbqEtForumTopic or $objsMbqEtForumTopic)
     */
    public function addMbqEtForumTopic(&$var) {
        if (is_array($var)) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } else {
            $oMbqWrEtForumPost = MbqMain::$oClk->newObj('MbqWrEtForumPost');
            $var->topicContent->setOriValue($oMbqWrEtForumPost->exttConvertContentForSave($var->topicContent->oriValue));
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqPostController.php');
            $oExttMbqPostController = new ExttMbqPostController();
            $oExttMbqPostController->Initialize();
            $oExttMbqPostController->exttMbqDiscussion('', $var);
            //mark read through the following code
            $oMbqDataPage = MbqMain::$oClk->newObj('MbqDataPage');
            $oMbqDataPage->initByStartAndLast(0, 1);
            require_once(MBQ_APPEXTENTION_PATH.'ExttMbqDiscussionController.php');
            $oExttMbqDiscussionController = new ExttMbqDiscussionController();
            $oExttMbqDiscussionController->Initialize();
            $oExttMbqDiscussionController->exttMbqGetTopicPosts($var->topicId->oriValue, '', '', '', $oMbqDataPage);
        }
    }
    
    /**
     * mark forum topic read
     *
     * @param  Mixed  $var($oMbqEtForumTopic or $objsMbqEtForumTopic)
     * @param  Array  $mbqOpt
     * $mbqOpt['case'] = 'markAllAsRead' means mark all my unread topics as read
     */
    public function markForumTopicRead(&$var = NULL, $mbqOpt = array()) {
        if ($mbqOpt['case'] == 'markAllAsRead') {
            //the AllViewed plugin has conflict with our plugin,so need disable this method
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
            $UserModel = Gdn::UserModel();
            $UserModel->UpdateAllViewed();
        } else {
            //dummy case
        }
    }
    
    /**
     * add forum topic view num
     * dummy method
     */
    public function addForumTopicViewNum($oMbqEtForumTopic) {
    }
    
    /**
     * reset forum topic subscription
     * dummy method
     */
    public function resetForumTopicSubscription($oMbqEtForumTopic) {
    }
    
    /**
     * subscribe topic
     *
     * @param  Mixed  $var($oMbqEtForumTopic or $objsMbqEtForumTopic)
     */
    public function subscribeTopic(&$var) {
        if (is_array($var)) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } else {
            //ref DiscussionController::Bookmark()
            $Session = Gdn::Session();
            $DiscussionModel = new DiscussionModel();
            $Discussion = NULL;
            $State = $DiscussionModel->BookmarkDiscussion($var->topicId->oriValue, $Session->UserID, $Discussion);
            // Update the user's bookmark count
            $CountBookmarks = $DiscussionModel->SetUserBookmarkCount($Session->UserID);
        }
    }
    
    /**
     * unsubscribe topic
     *
     * @param  Mixed  $var($oMbqEtForumTopic or $objsMbqEtForumTopic)
     */
    public function unsubscribeTopic(&$var) {
        if (is_array($var)) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NOT_ACHIEVE);
        } else {
            //ref DiscussionController::Bookmark()
            $Session = Gdn::Session();
            $DiscussionModel = new DiscussionModel();
            $Discussion = NULL;
            $State = $DiscussionModel->BookmarkDiscussion($var->topicId->oriValue, $Session->UserID, $Discussion);
            // Update the user's bookmark count
            $CountBookmarks = $DiscussionModel->SetUserBookmarkCount($Session->UserID);
        }
    }
  
}

?>
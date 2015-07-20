<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseAclEtForumTopic');

/**
 * forum topic acl class
 * 
 * @since  2012-8-10
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqAclEtForumTopic extends MbqBaseAclEtForumTopic {
    
    public function __construct() {
    }
    
    /**
     * judge can get topic from the forum
     *
     * @param  Object  $oMbqEtForum
     * @return  Boolean
     */
    public function canAclGetTopic($oMbqEtForum) {
        $Category = CategoryModel::Categories($oMbqEtForum->forumId->oriValue);
        if (empty($Category)) {
            return false;
        }
        $Category = (object)$Category;
        if (Gdn::Session()->CheckPermission('Vanilla.Discussions.View', TRUE, 'Category', GetValue('PermissionCategoryID', $Category))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * judge can get thread
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Boolean
     */
    public function canAclGetThread($oMbqEtForumTopic) {
        if (Gdn::Session()->CheckPermission('Vanilla.Discussions.View', TRUE, 'Category', $oMbqEtForumTopic->mbqBind['oStdForumTopic']->PermissionCategoryID)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * judge can new topic
     *
     * @param  Object  $oMbqEtForum
     * @return  Boolean
     */
    public function canAclNewTopic($oMbqEtForum) {
        /* modified from PostController::Discussion() */
        $Category = CategoryModel::Categories($oMbqEtForum->forumId->oriValue);
        if ($Category)
            $Category = (object)$Category;
        else
            $Category = NULL;
        if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Add') && is_object($Category)) {
            if (Gdn::Session()->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Category', $Category->PermissionCategoryID)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * judge can get_unread_topic
     *
     * @return  Boolean
     */
    public function canAclGetUnreadTopic() {
        return MbqMain::hasLogin();
    }
    
    /**
     * judge can get_participated_topic
     *
     * @return  Boolean
     */
    public function canAclGetParticipatedTopic() {
        return MbqMain::hasLogin();
    }
    
    /**
     * judge can get_latest_topic
     *
     * @return  Boolean
     */
    public function canAclGetLatestTopic() {
        if (MbqMain::$oMbqConfig->getCfg('forum.guest_search')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.guest_search.range.support')) {
            return true;
        } else {
            return MbqMain::hasLogin();
        }
    }
    
    /**
     * judge can search_topic
     *
     * @return  Boolean
     */
    public function canAclSearchTopic() {
        if (MbqMain::$oMbqConfig->getCfg('forum.guest_search')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.guest_search.range.support')) {
            return true;
        } else {
            return MbqMain::hasLogin();
        }
    }
    
    /**
     * judge can mark all my unread topics as read
     *
     * @return  Boolean
     */
    public function canAclMarkAllAsRead() {
        return MbqMain::hasLogin();
    }
    
    /**
     * judge can get_user_topic
     *
     * @return  Boolean
     */
    public function canAclGetUserTopic() {
        if (MbqMain::$oMbqConfig->getCfg('user.guest_okay')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.user.guest_okay.range.support')) {
            return true;
        } elseif (MbqMain::isJsonProtocol() && MbqMain::$oMbqConfig->getCfg('forum.private')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.private.range.no')) {   //for json
            return true;
        } else {
            return MbqMain::hasLogin();
        }
    }
    
    /**
     * judge can get subscribed topic
     *
     * @return  Boolean
     */
    public function canAclGetSubscribedTopic() {
        return MbqMain::hasLogin();
    }
    
    /**
     * judge can subscribe_topic
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Boolean
     */
    public function canAclSubscribeTopic($oMbqEtForumTopic) {
        if (MbqMain::hasLogin() && $this->canAclGetThread($oMbqEtForumTopic) && !$oMbqEtForumTopic->isSubscribed->oriValue) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * judge can unsubscribe_topic
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Boolean
     */
    public function canAclUnsubscribeTopic($oMbqEtForumTopic) {
        if (MbqMain::hasLogin() && $this->canAclGetThread($oMbqEtForumTopic) && $oMbqEtForumTopic->isSubscribed->oriValue) {
            return true;
        } else {
            return false;
        }
    }
  
}

?>
<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseAclEtForumPost');

/**
 * forum post acl class
 * 
 * @since  2012-8-20
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqAclEtForumPost extends MbqBaseAclEtForumPost {
    
    public function __construct() {
    }
    
    /**
     * judge can reply post
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Boolean
     */
    public function canAclReplyPost($oMbqEtForumTopic) {
        if (Gdn::Session()->CheckPermission('Vanilla.Comments.Add', TRUE, 'Category', $oMbqEtForumTopic->mbqBind['oStdForumTopic']->PermissionCategoryID)) {
            return true;
        }
        return false;
    }
    
    /**
     * judge can get quote post
     *
     * @param  Object  $oMbqEtForumPost
     * @return  Boolean
     */
    public function canAclGetQuotePost($oMbqEtForumPost) {
        return $this->canAclReplyPost($oMbqEtForumPost->oMbqEtForumTopic);
    }
    
    /**
     * judge can search_post
     *
     * @return  Boolean
     */
    public function canAclSearchPost() {
        if (MbqMain::$oMbqConfig->getCfg('forum.guest_search')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.guest_search.range.support')) {
            return true;
        } else {
            return MbqMain::hasLogin();
        }
    }
    
    /**
     * judge can get_user_reply_post
     *
     * @return  Boolean
     */
    public function canAclGetUserReplyPost() {
        if (MbqMain::$oMbqConfig->getCfg('user.guest_okay')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.user.guest_okay.range.support')) {
            return true;
        } elseif (MbqMain::isJsonProtocol() && MbqMain::$oMbqConfig->getCfg('forum.private')->oriValue == MbqBaseFdt::getFdt('MbqFdtConfig.forum.private.range.no')) {   //for json
            return true;
        } else {
            return MbqMain::hasLogin();
        }
    }
    
    /**
     * judge can get_raw_post
     *
     * @param  Object  $oMbqEtForumPost
     * @return  Boolean
     */
    public function canAclGetRawPost($oMbqEtForumPost) {
        return $this->canAclSaveRawPost($oMbqEtForumPost);
    }
    
    /**
     * judge can save_raw_post
     *
     * @param  Object  $oMbqEtForumPost
     * @return  Boolean
     */
    public function canAclSaveRawPost($oMbqEtForumPost) {
        return ($oMbqEtForumPost->canEdit->hasSetOriValue() && ($oMbqEtForumPost->canEdit->oriValue == MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.range.yes'))) ? true : false;
    }
  
}

?>
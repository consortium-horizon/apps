<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum topic read class
 * 
 * @since  2012-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtForumTopic extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return forum topic api data
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Array
     */
    public function returnApiDataForumTopic($oMbqEtForumTopic) {
        if (MbqMain::isJsonProtocol()) return $this->returnJsonApiDataForumTopic($oMbqEtForumTopic);
        $data = array();
        if ($oMbqEtForumTopic->totalPostNum->hasSetOriValue()) {
            $data['total_post_num'] = (int) $oMbqEtForumTopic->totalPostNum->oriValue;
        }
        if ($oMbqEtForumTopic->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtForumTopic->topicId->oriValue;
        }
        if ($oMbqEtForumTopic->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForumTopic->forumId->oriValue;
        }
        if ($oMbqEtForumTopic->oMbqEtForum) {
            $data['forum_name'] = (string) $oMbqEtForumTopic->oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForumTopic->topicTitle->hasSetOriValue()) {
            $data['topic_title'] = (string) $oMbqEtForumTopic->topicTitle->oriValue;
        }
        $data['short_content'] = (string) $oMbqEtForumTopic->shortContent->oriValue;
        if ($oMbqEtForumTopic->prefixId->hasSetOriValue()) {
            $data['prefix_id'] = (string) $oMbqEtForumTopic->prefixId->oriValue;
        }
        if ($oMbqEtForumTopic->prefixName->hasSetOriValue()) {
            $data['prefix'] = (string) $oMbqEtForumTopic->prefixName->oriValue;
        }
        if (MbqMain::$cmd == 'get_topic' || MbqMain::$cmd == 'get_user_topic') {
            if ($oMbqEtForumTopic->topicAuthorId->hasSetOriValue()) {
                $data['topic_author_id'] = (string) $oMbqEtForumTopic->topicAuthorId->oriValue;
            }
            if ($oMbqEtForumTopic->oAuthorMbqEtUser) {
                $data['topic_author_name'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->getDisplayName();
            }
        }
        if ($oMbqEtForumTopic->oLastReplyMbqEtUser) {
            $data['last_reply_author_name'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->getDisplayName();
            $data['last_reply_author_id'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->userId->oriValue;
        }
        if ($oMbqEtForumTopic->attachmentIdArray->hasSetOriValue()) {
            $data['attachment_id_array'] = (array) $oMbqEtForumTopic->attachmentIdArray->oriValue;
        }
        if ($oMbqEtForumTopic->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtForumTopic->groupId->oriValue;
        }
        if ($oMbqEtForumTopic->state->hasSetOriValue()) {
            $data['state'] = (int) $oMbqEtForumTopic->state->oriValue;
        }
        if ($oMbqEtForumTopic->isSubscribed->hasSetOriValue()) {
            $data['is_subscribed'] = (boolean) $oMbqEtForumTopic->isSubscribed->oriValue;
        }
        if ($oMbqEtForumTopic->canSubscribe->hasSetOriValue()) {
            $data['can_subscribe'] = (boolean) $oMbqEtForumTopic->canSubscribe->oriValue;
        } else {
            $data['can_subscribe'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canSubscribe.default');
        }
        if ($oMbqEtForumTopic->isClosed->hasSetOriValue()) {
            $data['is_closed'] = (boolean) $oMbqEtForumTopic->isClosed->oriValue;
        }
        if ($oMbqEtForumTopic->postTime->hasSetOriValue()) {
            $data['post_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumTopic->postTime->oriValue);
        }
        if (MbqMain::$cmd == 'get_topic' || MbqMain::$cmd == 'get_user_topic') {
            if ($oMbqEtForumTopic->authorIconUrl->hasSetOriValue()) {
                $data['icon_url'] = (string) $oMbqEtForumTopic->authorIconUrl->oriValue;
            }
            $data['post_author_name'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->getDisplayName();
            $data['post_author_id'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->userId->oriValue;
        } else {
            if ($oMbqEtForumTopic->oLastReplyMbqEtUser) {
                if( $oMbqEtForumTopic->oLastReplyMbqEtUser->iconUrl->hasSetOriValue())
                {
                    $data['icon_url'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->iconUrl->oriValue;
                }
                $data['post_author_name'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->getDisplayName();
                $data['post_author_id'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->userId->oriValue;
            }
        }
        if ($oMbqEtForumTopic->lastReplyTime->hasSetOriValue()) {
            $data['last_reply_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumTopic->lastReplyTime->oriValue);
        }
        if ($oMbqEtForumTopic->replyNumber->hasSetOriValue()) {
            $data['reply_number'] = (int) $oMbqEtForumTopic->replyNumber->oriValue;
        }
        if ($oMbqEtForumTopic->newPost->hasSetOriValue()) {
            $data['new_post'] = (boolean) $oMbqEtForumTopic->newPost->oriValue;
        }
        if ($oMbqEtForumTopic->viewNumber->hasSetOriValue()) {
            $data['view_number'] = (int) $oMbqEtForumTopic->viewNumber->oriValue;
        }
        if ($oMbqEtForumTopic->participatedUids->hasSetOriValue()) {
            $data['participated_uids'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtForumTopic->participatedUids->oriValue);
        }
        if ($oMbqEtForumTopic->canThank->hasSetOriValue()) {
            $data['can_thank'] = (boolean) $oMbqEtForumTopic->canThank->oriValue;
        } else {
            $data['can_thank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canThank.default');
        }
        if ($oMbqEtForumTopic->thankCount->hasSetOriValue()) {
            $data['thank_count'] = (int) $oMbqEtForumTopic->thankCount->oriValue;
        }
        if ($oMbqEtForumTopic->canLike->hasSetOriValue()) {
            $data['can_like'] = (boolean) $oMbqEtForumTopic->canLike->oriValue;
        } else {
            $data['can_like'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canLike.default');
        }
        if ($oMbqEtForumTopic->isLiked->hasSetOriValue()) {
            $data['is_liked'] = (boolean) $oMbqEtForumTopic->isLiked->oriValue;
        }
        if ($oMbqEtForumTopic->likeCount->hasSetOriValue()) {
            $data['like_count'] = (int) $oMbqEtForumTopic->likeCount->oriValue;
        }
        if ($oMbqEtForumTopic->canDelete->hasSetOriValue()) {
            $data['can_delete'] = (boolean) $oMbqEtForumTopic->canDelete->oriValue;
        } else {
            $data['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canDelete.default');
        }
        if ($oMbqEtForumTopic->isDeleted->hasSetOriValue()) {
            $data['is_deleted'] = (boolean) $oMbqEtForumTopic->isDeleted->oriValue;
        }
        if ($oMbqEtForumTopic->canApprove->hasSetOriValue()) {
            $data['can_approve'] = (boolean) $oMbqEtForumTopic->canApprove->oriValue;
        } else {
            $data['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canApprove.default');
        }
        if ($oMbqEtForumTopic->isApproved->hasSetOriValue()) {
            $data['is_approved'] = (boolean) $oMbqEtForumTopic->isApproved->oriValue;
        }
        if ($oMbqEtForumTopic->canStick->hasSetOriValue()) {
            $data['can_stick'] = (boolean) $oMbqEtForumTopic->canStick->oriValue;
        } else {
            $data['can_stick'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canStick.default');
        }
        if ($oMbqEtForumTopic->isSticky->hasSetOriValue()) {
            $data['is_sticky'] = (boolean) $oMbqEtForumTopic->isSticky->oriValue;
        }
        if ($oMbqEtForumTopic->canClose->hasSetOriValue()) {
            $data['can_close'] = (boolean) $oMbqEtForumTopic->canClose->oriValue;
        } else {
            $data['can_close'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canClose.default');
        }
        if ($oMbqEtForumTopic->canRename->hasSetOriValue()) {
            $data['can_rename'] = (boolean) $oMbqEtForumTopic->canRename->oriValue;
        } else {
            $data['can_rename'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canRename.default');
        }
        if ($oMbqEtForumTopic->canMove->hasSetOriValue()) {
            $data['can_move'] = (boolean) $oMbqEtForumTopic->canMove->oriValue;
        } else {
            $data['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canMove.default');
        }
        if ($oMbqEtForumTopic->isMoved->hasSetOriValue()) {
            $data['is_moved'] = (boolean) $oMbqEtForumTopic->isMoved->oriValue;
            if ($data['is_moved'] && $oMbqEtForumTopic->realTopicId->hasSetOriValue()){
                $data['topic_id'] = (string) $oMbqEtForumTopic->realTopicId->oriValue;
            }
        }
        if ($oMbqEtForumTopic->realTopicId->hasSetOriValue()) {
            $data['real_topic_id'] = (string) $oMbqEtForumTopic->realTopicId->oriValue;
        }
        if ($oMbqEtForumTopic->modByUserId->hasSetOriValue()) {
            $data['moderated_by_id'] = (string) $oMbqEtForumTopic->modByUserId->oriValue;
        }
        if ($oMbqEtForumTopic->deleteByUserId->hasSetOriValue()) {
            $data['deleted_by_id'] = (string) $oMbqEtForumTopic->deleteByUserId->oriValue;
        }
        if ($oMbqEtForumTopic->deleteReason->hasSetOriValue()) {
            $data['delete_reason'] = (string) $oMbqEtForumTopic->deleteReason->oriValue;
        }
        if ($oMbqEtForumTopic->canReply->hasSetOriValue()) {
            $data['can_reply'] = (boolean) $oMbqEtForumTopic->canReply->oriValue;
        } else {
            $data['can_reply'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReply.default');
        }
        if ($oMbqEtForumTopic->canReport->hasSetOriValue()) {
            $data['can_report'] = (boolean) $oMbqEtForumTopic->canReport->oriValue;
        } else {
            $data['can_report'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReport.default');
        }
        /*
        if ($oMbqEtForumTopic->objsBreadcrumbMbqEtForum) {
        $data['navi'] = array();
        foreach ($oMbqEtForumTopic->objsBreadcrumbMbqEtForum as $oBreadcrumbMbqEtForum) {
        $data['navi'][] = array(
        'id' => (string) $oBreadcrumbMbqEtForum->forumId->oriValue,
        'name' => (string) $oBreadcrumbMbqEtForum->forumName->oriValue  
        );
        }
        }
         */
        return $data;
    }
    public function returnJsonApiDataForumTopic($oMbqEtForumTopic) {
        $data = array();
        if ($oMbqEtForumTopic->totalPostNum->hasSetOriValue()) {
            $data['total_post_num'] = (int) $oMbqEtForumTopic->totalPostNum->oriValue;
        }
        if ($oMbqEtForumTopic->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtForumTopic->topicId->oriValue;
        }
        if ($oMbqEtForumTopic->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForumTopic->forumId->oriValue;
        }
        if ($oMbqEtForumTopic->oMbqEtForum) {
            $data['forum_name'] = (string) $oMbqEtForumTopic->oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForumTopic->topicTitle->hasSetOriValue()) {
            $data['topic_title'] = (string) $oMbqEtForumTopic->topicTitle->oriValue;
        }
        $data['short_content'] = (string) $oMbqEtForumTopic->shortContent->oriValue;
        if ($oMbqEtForumTopic->prefixId->hasSetOriValue()) {
            $data['prefix_id'] = (string) $oMbqEtForumTopic->prefixId->oriValue;
        }
        if ($oMbqEtForumTopic->prefixName->hasSetOriValue()) {
            $data['prefix'] = (string) $oMbqEtForumTopic->prefixName->oriValue;
        }
        if (MbqMain::$cmd == 'get_topic' || MbqMain::$cmd == 'get_user_topic') {
            if ($oMbqEtForumTopic->topicAuthorId->hasSetOriValue()) {
                $data['topic_author_id'] = (string) $oMbqEtForumTopic->topicAuthorId->oriValue;
            }
            if ($oMbqEtForumTopic->oAuthorMbqEtUser) {
                $data['topic_author_name'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->getDisplayName();
            }
        }
        if ($oMbqEtForumTopic->oLastReplyMbqEtUser) {
            $data['last_reply_author_name'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->getDisplayName();
            $data['last_reply_author_id'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->userId->oriValue;
        }
        if ($oMbqEtForumTopic->attachmentIdArray->hasSetOriValue()) {
            $data['attachment_id_array'] = (array) $oMbqEtForumTopic->attachmentIdArray->oriValue;
        }
        if ($oMbqEtForumTopic->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtForumTopic->groupId->oriValue;
        }
        if ($oMbqEtForumTopic->state->hasSetOriValue()) {
            $data['state'] = (int) $oMbqEtForumTopic->state->oriValue;
        }
        if ($oMbqEtForumTopic->isSubscribed->hasSetOriValue()) {
            $data['is_subscribed'] = (boolean) $oMbqEtForumTopic->isSubscribed->oriValue;
        }
        if ($oMbqEtForumTopic->canSubscribe->hasSetOriValue()) {
            $data['can_subscribe'] = (boolean) $oMbqEtForumTopic->canSubscribe->oriValue;
        } else {
            $data['can_subscribe'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canSubscribe.default');
        }
        if ($oMbqEtForumTopic->isClosed->hasSetOriValue()) {
            $data['is_closed'] = (boolean) $oMbqEtForumTopic->isClosed->oriValue;
        }
        if ($oMbqEtForumTopic->postTime->hasSetOriValue()) {
            $data['post_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumTopic->postTime->oriValue);
        }
        if (MbqMain::$cmd == 'get_topic' || MbqMain::$cmd == 'get_user_topic') {
            if ($oMbqEtForumTopic->authorIconUrl->hasSetOriValue()) {
                $data['icon_url'] = (string) $oMbqEtForumTopic->authorIconUrl->oriValue;
                $data['post_author_name'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->getDisplayName();
                $data['post_author_id'] = (string) $oMbqEtForumTopic->oAuthorMbqEtUser->userId->oriValue;
            }
        } else {
            if ($oMbqEtForumTopic->oLastReplyMbqEtUser && $oMbqEtForumTopic->oLastReplyMbqEtUser->iconUrl->hasSetOriValue()) {
                $data['icon_url'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->iconUrl->oriValue;
                $data['post_author_name'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->getDisplayName();
                $data['post_author_id'] = (string) $oMbqEtForumTopic->oLastReplyMbqEtUser->userId->oriValue;
            }
        }
        if ($oMbqEtForumTopic->lastReplyTime->hasSetOriValue()) {
            $data['last_reply_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumTopic->lastReplyTime->oriValue);
        }
        if ($oMbqEtForumTopic->replyNumber->hasSetOriValue()) {
            $data['reply_number'] = (int) $oMbqEtForumTopic->replyNumber->oriValue;
        }
        if ($oMbqEtForumTopic->newPost->hasSetOriValue()) {
            $data['new_post'] = (boolean) $oMbqEtForumTopic->newPost->oriValue;
        }
        if ($oMbqEtForumTopic->viewNumber->hasSetOriValue()) {
            $data['view_number'] = (int) $oMbqEtForumTopic->viewNumber->oriValue;
        }
        if ($oMbqEtForumTopic->participatedUids->hasSetOriValue()) {
            $data['participated_uids'] = (array) MbqMain::$oMbqCm->changeArrValueToString($oMbqEtForumTopic->participatedUids->oriValue);
        }
        if ($oMbqEtForumTopic->canThank->hasSetOriValue()) {
            $data['can_thank'] = (boolean) $oMbqEtForumTopic->canThank->oriValue;
        } else {
            $data['can_thank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canThank.default');
        }
        if ($oMbqEtForumTopic->thankCount->hasSetOriValue()) {
            $data['thank_count'] = (int) $oMbqEtForumTopic->thankCount->oriValue;
        }
        if ($oMbqEtForumTopic->canLike->hasSetOriValue()) {
            $data['can_like'] = (boolean) $oMbqEtForumTopic->canLike->oriValue;
        } else {
            $data['can_like'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canLike.default');
        }
        if ($oMbqEtForumTopic->isLiked->hasSetOriValue()) {
            $data['is_liked'] = (boolean) $oMbqEtForumTopic->isLiked->oriValue;
        }
        if ($oMbqEtForumTopic->likeCount->hasSetOriValue()) {
            $data['like_count'] = (int) $oMbqEtForumTopic->likeCount->oriValue;
        }
        if ($oMbqEtForumTopic->canDelete->hasSetOriValue()) {
            $data['can_delete'] = (boolean) $oMbqEtForumTopic->canDelete->oriValue;
        } else {
            $data['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canDelete.default');
        }
        if ($oMbqEtForumTopic->isDeleted->hasSetOriValue()) {
            $data['is_deleted'] = (boolean) $oMbqEtForumTopic->isDeleted->oriValue;
        }
        if ($oMbqEtForumTopic->canApprove->hasSetOriValue()) {
            $data['can_approve'] = (boolean) $oMbqEtForumTopic->canApprove->oriValue;
        } else {
            $data['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canApprove.default');
        }
        if ($oMbqEtForumTopic->isApproved->hasSetOriValue()) {
            $data['is_approved'] = (boolean) $oMbqEtForumTopic->isApproved->oriValue;
        }
        if ($oMbqEtForumTopic->canStick->hasSetOriValue()) {
            $data['can_stick'] = (boolean) $oMbqEtForumTopic->canStick->oriValue;
        } else {
            $data['can_stick'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canStick.default');
        }
        if ($oMbqEtForumTopic->isSticky->hasSetOriValue()) {
            $data['is_sticky'] = (boolean) $oMbqEtForumTopic->isSticky->oriValue;
        }
        if ($oMbqEtForumTopic->canClose->hasSetOriValue()) {
            $data['can_close'] = (boolean) $oMbqEtForumTopic->canClose->oriValue;
        } else {
            $data['can_close'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canClose.default');
        }
        if ($oMbqEtForumTopic->canRename->hasSetOriValue()) {
            $data['can_rename'] = (boolean) $oMbqEtForumTopic->canRename->oriValue;
        } else {
            $data['can_rename'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canRename.default');
        }
        if ($oMbqEtForumTopic->canMove->hasSetOriValue()) {
            $data['can_move'] = (boolean) $oMbqEtForumTopic->canMove->oriValue;
        } else {
            $data['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canMove.default');
        }
        if ($oMbqEtForumTopic->isMoved->hasSetOriValue()) {
            $data['is_moved'] = (boolean) $oMbqEtForumTopic->isMoved->oriValue;
            if ($data['is_moved'] && $oMbqEtForumTopic->realTopicId->hasSetOriValue()){
                $data['topic_id'] = (string) $oMbqEtForumTopic->realTopicId->oriValue;
            }
        }
        if ($oMbqEtForumTopic->realTopicId->hasSetOriValue()) {
            $data['real_topic_id'] = (string) $oMbqEtForumTopic->realTopicId->oriValue;
        }
        if ($oMbqEtForumTopic->modByUserId->hasSetOriValue()) {
            $data['moderated_by_id'] = (string) $oMbqEtForumTopic->modByUserId->oriValue;
        }
        if ($oMbqEtForumTopic->deleteByUserId->hasSetOriValue()) {
            $data['deleted_by_id'] = (string) $oMbqEtForumTopic->deleteByUserId->oriValue;
        }
        if ($oMbqEtForumTopic->deleteReason->hasSetOriValue()) {
            $data['delete_reason'] = (string) $oMbqEtForumTopic->deleteReason->oriValue;
        }
        if ($oMbqEtForumTopic->canReply->hasSetOriValue()) {
            $data['can_reply'] = (boolean) $oMbqEtForumTopic->canReply->oriValue;
        } else {
            $data['can_reply'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReply.default');
        }
        if ($oMbqEtForumTopic->canReport->hasSetOriValue()) {
            $data['can_report'] = (boolean) $oMbqEtForumTopic->canReport->oriValue;
        } else {
            $data['can_report'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReport.default');
        }
        /*
        if ($oMbqEtForumTopic->objsBreadcrumbMbqEtForum) {
        $data['navi'] = array();
        foreach ($oMbqEtForumTopic->objsBreadcrumbMbqEtForum as $oBreadcrumbMbqEtForum) {
        $data['navi'][] = array(
        'id' => (string) $oBreadcrumbMbqEtForum->forumId->oriValue,
        'name' => (string) $oBreadcrumbMbqEtForum->forumName->oriValue  
        );
        }
        }
         */
        return $data;
    }
    /**
     * return forum topic json api data
     *
     * @param  Object  $oMbqEtForumTopic
     * @return  Array
     */
    protected function returnAdvJsonApiDataForumTopic($oMbqEtForumTopic) {
        $data = array();
        if ($oMbqEtForumTopic->topicId->hasSetOriValue()) {
            $data['id'] = (string) $oMbqEtForumTopic->topicId->oriValue;
        }
        if ($oMbqEtForumTopic->topicTitle->hasSetOriValue()) {
            $data['title'] = (string) $oMbqEtForumTopic->topicTitle->oriValue;
        }
        if ($oMbqEtForumTopic->postTime->hasSetOriValue()) {
            $data['time'] = (int) $oMbqEtForumTopic->postTime->oriValue;
        }
        if ($oMbqEtForumTopic->replyNumber->hasSetOriValue()) {
            $data['replies'] = (int) $oMbqEtForumTopic->replyNumber->oriValue;
        }
        if ($oMbqEtForumTopic->viewNumber->hasSetOriValue()) {
            $data['views'] = (int) $oMbqEtForumTopic->viewNumber->oriValue;
        }
        if ($oMbqEtForumTopic->oMbqEtForum) {
            $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
            $data['forum'] = $oMbqRdEtForum->returnApiDataForum($oMbqEtForumTopic->oMbqEtForum);
        }
        if ($oMbqEtForumTopic->oAuthorMbqEtUser) {
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $data['author'] = $oMbqRdEtUser->returnApiDataUser($oMbqEtForumTopic->oAuthorMbqEtUser);
        }
        if ($oMbqEtForumTopic->prefixId->hasSetOriValue() && $oMbqEtForumTopic->prefixName->hasSetOriValue()) {
            $data['prefix'] = array('id' => $oMbqEtForumTopic->prefixId->oriValue, 'name' => $oMbqEtForumTopic->prefixName->oriValue);
        }
        $data['status'] = array();
        if ($oMbqEtForumTopic->newPost->hasSetOriValue()) {
            $data['status']['is_unread'] = (boolean) $oMbqEtForumTopic->newPost->oriValue;
        }
        if ($oMbqEtForumTopic->isSubscribed->hasSetOriValue()) {
            $data['status']['is_follow'] = (boolean) $oMbqEtForumTopic->isSubscribed->oriValue;
        }
        if ($oMbqEtForumTopic->isHot->hasSetOriValue()) {
            $data['status']['is_hot'] = (boolean) $oMbqEtForumTopic->isHot->oriValue;
        }
        if ($oMbqEtForumTopic->isDigest->hasSetOriValue()) {
            $data['status']['is_digest'] = (boolean) $oMbqEtForumTopic->isDigest->oriValue;
        }
        if ($oMbqEtForumTopic->isClosed->hasSetOriValue()) {
            $data['status']['is_closed'] = (boolean) $oMbqEtForumTopic->isClosed->oriValue;
        }
        if ($oMbqEtForumTopic->isSticky->hasSetOriValue()) {
            $data['status']['is_sticky'] = (boolean) $oMbqEtForumTopic->isSticky->oriValue;
        }
        if ($oMbqEtForumTopic->state->hasSetOriValue()) {
            $data['status']['is_pending'] = (boolean) $oMbqEtForumTopic->state->oriValue;   //!!!
        }
        if ($oMbqEtForumTopic->isDeleted->hasSetOriValue()) {
            $data['status']['is_deleted'] = (boolean) $oMbqEtForumTopic->isDeleted->oriValue;
        }
        $data['permission'] = array();
        if ($oMbqEtForumTopic->canReply->hasSetOriValue()) {
            $data['permission']['can_reply'] = (boolean) $oMbqEtForumTopic->canReply->oriValue;
        } else {
            $data['permission']['can_reply'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canReply.default');
        }
        if ($oMbqEtForumTopic->canRename->hasSetOriValue()) {
            $data['permission']['can_edit'] = (boolean) $oMbqEtForumTopic->canRename->oriValue;
        } else {
            $data['permission']['can_edit'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canRename.default');
        }
        if ($oMbqEtForumTopic->canSubscribe->hasSetOriValue()) {
            $data['permission']['can_follow'] = (boolean) $oMbqEtForumTopic->canSubscribe->oriValue;
        } else {
            $data['permission']['can_follow'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canSubscribe.default');
        }
        if ($oMbqEtForumTopic->canClose->hasSetOriValue()) {
            $data['permission']['can_close'] = (boolean) $oMbqEtForumTopic->canClose->oriValue;
        } else {
            $data['permission']['can_close'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canClose.default');
        }
        if ($oMbqEtForumTopic->canStick->hasSetOriValue()) {
            $data['permission']['can_stick'] = (boolean) $oMbqEtForumTopic->canStick->oriValue;
        } else {
            $data['permission']['can_stick'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canStick.default');
        }
        if ($oMbqEtForumTopic->canApprove->hasSetOriValue()) {
            $data['permission']['can_approve'] = (boolean) $oMbqEtForumTopic->canApprove->oriValue;
        } else {
            $data['permission']['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canApprove.default');
        }
        if ($oMbqEtForumTopic->canDelete->hasSetOriValue()) {
            $data['permission']['can_delete'] = (boolean) $oMbqEtForumTopic->canDelete->oriValue;
        } else {
            $data['permission']['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canDelete.default');
        }
        if ($oMbqEtForumTopic->canMove->hasSetOriValue()) {
            $data['permission']['can_move'] = (boolean) $oMbqEtForumTopic->canMove->oriValue;
        } else {
            $data['permission']['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumTopic.canMove.default');
        }
        $oMbqRdEtForumPost = MbqMain::$oClk->newObj('MbqRdEtForumPost');
        if ($oMbqEtForumTopic->oFirstMbqEtForumPost) {
            $data['first_post'] = $oMbqRdEtForumPost->returnApiDataForumPost($oMbqEtForumTopic->oFirstMbqEtForumPost);
        }
        if ($oMbqEtForumTopic->oLastMbqEtForumPost) {
            $data['last_post'] = $oMbqRdEtForumPost->returnApiDataForumPost($oMbqEtForumTopic->oLastMbqEtForumPost);
        }
        return $data;
    }
    
    /**
     * return forum topic array api data
     *
     * @param  Array  $objsMbqEtForumTopic
     * @return  Array
     */
    public function returnApiArrDataForumTopic($objsMbqEtForumTopic) {
        $data = array();
        foreach ($objsMbqEtForumTopic as $oMbqEtForumTopic) {
            $data[] = $this->returnApiDataForumTopic($oMbqEtForumTopic);
        }
        return $data;
    }
    
    /**
     * get forum topic objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtForumTopic() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one forum topic by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtForumTopic() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
}

?>
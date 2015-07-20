<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum post read class
 * 
 * @since  2012-8-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtForumPost extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return forum post api data
     *
     * @param  Object  $oMbqEtForumPost
     * @param  Boolean  $returnHtml
     * @return  Array
     */
    public function returnApiDataForumPost($oMbqEtForumPost, $returnHtml = true) {
        if (MbqMain::isJsonProtocol()) return $this->returnJsonApiDataForumPost($oMbqEtForumPost);
        $data = array();
        if ($oMbqEtForumPost->postId->hasSetOriValue()) {
            $data['post_id'] = (string) $oMbqEtForumPost->postId->oriValue;
        }
        if ($oMbqEtForumPost->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForumPost->forumId->oriValue;
        }
        if ($oMbqEtForumPost->oMbqEtForum) {
            $data['forum_name'] = (string) $oMbqEtForumPost->oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForumPost->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtForumPost->topicId->oriValue;
        }
        if ($oMbqEtForumPost->oMbqEtForumTopic) {
            $data['topic_title'] = (string) $oMbqEtForumPost->oMbqEtForumTopic->topicTitle->oriValue;
            if ($oMbqEtForumPost->oMbqEtForumTopic->replyNumber->hasSetOriValue()) {
                $data['reply_number'] = (int) $oMbqEtForumPost->oMbqEtForumTopic->replyNumber->oriValue;
            }
            if ($oMbqEtForumPost->oMbqEtForumTopic->newPost->hasSetOriValue()) {
                $data['new_post'] = (boolean) $oMbqEtForumPost->oMbqEtForumTopic->newPost->oriValue;
            }
            if ($oMbqEtForumPost->oMbqEtForumTopic->viewNumber->hasSetOriValue()) {
                $data['view_number'] = (int) $oMbqEtForumPost->oMbqEtForumTopic->viewNumber->oriValue;
            }
        }
        if ($oMbqEtForumPost->postTitle->hasSetOriValue()) {
            $data['post_title'] = (string) $oMbqEtForumPost->postTitle->oriValue;
        }
        if ($returnHtml) {
            if ($oMbqEtForumPost->postContent->hasSetTmlDisplayValue()) {
                $data['post_content'] = (string) $oMbqEtForumPost->postContent->tmlDisplayValue;
            }
        } else {
            if ($oMbqEtForumPost->postContent->hasSetTmlDisplayValueNoHtml()) {
                $data['post_content'] = (string) $oMbqEtForumPost->postContent->tmlDisplayValueNoHtml;
            }
        }
        $data['short_content'] = (string) $oMbqEtForumPost->shortContent->oriValue;
        if ($oMbqEtForumPost->postAuthorId->hasSetOriValue()) {
            $data['post_author_id'] = (string) $oMbqEtForumPost->postAuthorId->oriValue;
        }
        if ($oMbqEtForumPost->oAuthorMbqEtUser) {
            $data['post_author_name'] = (string) $oMbqEtForumPost->oAuthorMbqEtUser->getDisplayName();
        }
        if ($oMbqEtForumPost->attachmentIdArray->hasSetOriValue()) {
            $data['attachment_id_array'] = (array) $oMbqEtForumPost->attachmentIdArray->oriValue;
        }
        if ($oMbqEtForumPost->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtForumPost->groupId->oriValue;
        }
        if ($oMbqEtForumPost->state->hasSetOriValue()) {
            $data['state'] = (int) $oMbqEtForumPost->state->oriValue;
        }
        if ($oMbqEtForumPost->isOnline->hasSetOriValue()) {
            $data['is_online'] = (boolean) $oMbqEtForumPost->isOnline->oriValue;
        }
        if ($oMbqEtForumPost->canEdit->hasSetOriValue()) {
            $data['can_edit'] = (boolean) $oMbqEtForumPost->canEdit->oriValue;
        } else {
            $data['can_edit'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.default');
        }
        if ($oMbqEtForumPost->authorIconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtForumPost->authorIconUrl->oriValue;
        }
        if ($oMbqEtForumPost->postTime->hasSetOriValue()) {
            $data['post_time'] = (string) MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumPost->postTime->oriValue);
        }
        if ($oMbqEtForumPost->allowSmilies->hasSetOriValue()) {
            $data['allow_smilies'] = (boolean) $oMbqEtForumPost->allowSmilies->oriValue;
        }
        if ($oMbqEtForumPost->position->hasSetOriValue()) {
            $data['position'] = (int) $oMbqEtForumPost->position->oriValue;
        }
        if ($oMbqEtForumPost->canThank->hasSetOriValue()) {
            $data['can_thank'] = (boolean) $oMbqEtForumPost->canThank->oriValue;
        } else {
            $data['can_thank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canThank.default');
        }
        if ($oMbqEtForumPost->thankCount->hasSetOriValue()) {
            $data['thank_count'] = (int) $oMbqEtForumPost->thankCount->oriValue;
        }
        if ($oMbqEtForumPost->canLike->hasSetOriValue()) {
            $data['can_like'] = (boolean) $oMbqEtForumPost->canLike->oriValue;
        } else {
            $data['can_like'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canLike.default');
        }
        if ($oMbqEtForumPost->isLiked->hasSetOriValue()) {
            $data['is_liked'] = (boolean) $oMbqEtForumPost->isLiked->oriValue;
        }
        if ($oMbqEtForumPost->likeCount->hasSetOriValue()) {
            $data['like_count'] = (int) $oMbqEtForumPost->likeCount->oriValue;
        }
        if ($oMbqEtForumPost->canDelete->hasSetOriValue()) {
            $data['can_delete'] = (boolean) $oMbqEtForumPost->canDelete->oriValue;
        } else {
            $data['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canDelete.default');
        }
        if ($oMbqEtForumPost->isDeleted->hasSetOriValue()) {
            $data['is_deleted'] = (boolean) $oMbqEtForumPost->isDeleted->oriValue;
        }
        if ($oMbqEtForumPost->canApprove->hasSetOriValue()) {
            $data['can_approve'] = (boolean) $oMbqEtForumPost->canApprove->oriValue;
        } else {
            $data['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canApprove.default');
        }
        if ($oMbqEtForumPost->isApproved->hasSetOriValue()) {
            $data['is_approved'] = (boolean) $oMbqEtForumPost->isApproved->oriValue;
        }
        if ($oMbqEtForumPost->canMove->hasSetOriValue()) {
            $data['can_move'] = (boolean) $oMbqEtForumPost->canMove->oriValue;
        } else {
            $data['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canMove.default');
        }
        if ($oMbqEtForumPost->modByUserId->hasSetOriValue()) {
            $data['moderated_by_id'] = (string) $oMbqEtForumPost->modByUserId->oriValue;
        }
        if ($oMbqEtForumPost->deleteByUserId->hasSetOriValue()) {
            $data['deleted_by_id'] = (string) $oMbqEtForumPost->deleteByUserId->oriValue;
        }
        if ($oMbqEtForumPost->deleteReason->hasSetOriValue()) {
            $data['delete_reason'] = (string) $oMbqEtForumPost->deleteReason->oriValue;
        }
        if ($oMbqEtForumPost->canReport->hasSetOriValue()) {
            $data['can_report'] = (boolean) $oMbqEtForumPost->canReport->oriValue;
        } else {
            $data['can_report'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canReport.default');
        }
        /* attachments */
        $oMbqRdEtAtt = MbqMain::$oClk->newObj('MbqRdEtAtt');
        $data['attachments'] = (array) $oMbqRdEtAtt->returnApiArrDataAttachment($oMbqEtForumPost->objsNotInContentMbqEtAtt);
        /* thanks_info */
        $oMbqRdEtThank = MbqMain::$oClk->newObj('MbqRdEtThank');
        $data['thanks_info'] = (array) $oMbqRdEtThank->returnApiArrDataThank($oMbqEtForumPost->objsMbqEtThank);
        /* likes_info.TODO */
        $data['likes_info'] = (array) array();
        return $data;
    }
    public function returnJsonApiDataForumPost($oMbqEtForumPost, $returnHtml = true) {
        $data = array();
        if ($oMbqEtForumPost->postId->hasSetOriValue()) {
            $data['post_id'] = (string) $oMbqEtForumPost->postId->oriValue;
        }
        if ($oMbqEtForumPost->forumId->hasSetOriValue()) {
            $data['forum_id'] = (string) $oMbqEtForumPost->forumId->oriValue;
        }
        if ($oMbqEtForumPost->oMbqEtForum) {
            $data['forum_name'] = (string) $oMbqEtForumPost->oMbqEtForum->forumName->oriValue;
        }
        if ($oMbqEtForumPost->topicId->hasSetOriValue()) {
            $data['topic_id'] = (string) $oMbqEtForumPost->topicId->oriValue;
        }
        if ($oMbqEtForumPost->oMbqEtForumTopic) {
            $data['topic_title'] = (string) $oMbqEtForumPost->oMbqEtForumTopic->topicTitle->oriValue;
            if ($oMbqEtForumPost->oMbqEtForumTopic->replyNumber->hasSetOriValue()) {
                $data['reply_number'] = (int) $oMbqEtForumPost->oMbqEtForumTopic->replyNumber->oriValue;
            }
            if ($oMbqEtForumPost->oMbqEtForumTopic->newPost->hasSetOriValue()) {
                $data['new_post'] = (boolean) $oMbqEtForumPost->oMbqEtForumTopic->newPost->oriValue;
            }
            if ($oMbqEtForumPost->oMbqEtForumTopic->viewNumber->hasSetOriValue()) {
                $data['view_number'] = (int) $oMbqEtForumPost->oMbqEtForumTopic->viewNumber->oriValue;
            }
        }
        if ($oMbqEtForumPost->postTitle->hasSetOriValue()) {
            $data['post_title'] = (string) $oMbqEtForumPost->postTitle->oriValue;
        }
        if ($returnHtml) {
            if ($oMbqEtForumPost->postContent->hasSetTmlDisplayValue()) {
                $data['post_content'] = (string) $oMbqEtForumPost->postContent->tmlDisplayValue;
            }
        } else {
            if ($oMbqEtForumPost->postContent->hasSetTmlDisplayValueNoHtml()) {
                $data['post_content'] = (string) $oMbqEtForumPost->postContent->tmlDisplayValueNoHtml;
            }
        }
        $data['short_content'] = (string) $oMbqEtForumPost->shortContent->oriValue;
        if ($oMbqEtForumPost->postAuthorId->hasSetOriValue()) {
            $data['post_author_id'] = (string) $oMbqEtForumPost->postAuthorId->oriValue;
        }
        if ($oMbqEtForumPost->oAuthorMbqEtUser) {
            $data['post_author_name'] = (string) $oMbqEtForumPost->oAuthorMbqEtUser->getDisplayName();
        }
        if ($oMbqEtForumPost->attachmentIdArray->hasSetOriValue()) {
            $data['attachment_id_array'] = (array) $oMbqEtForumPost->attachmentIdArray->oriValue;
        }
        if ($oMbqEtForumPost->groupId->hasSetOriValue()) {
            $data['group_id'] = (string) $oMbqEtForumPost->groupId->oriValue;
        }
        if ($oMbqEtForumPost->state->hasSetOriValue()) {
            $data['state'] = (int) $oMbqEtForumPost->state->oriValue;
        }
        if ($oMbqEtForumPost->isOnline->hasSetOriValue()) {
            $data['is_online'] = (boolean) $oMbqEtForumPost->isOnline->oriValue;
        }
        if ($oMbqEtForumPost->canEdit->hasSetOriValue()) {
            $data['can_edit'] = (boolean) $oMbqEtForumPost->canEdit->oriValue;
        } else {
            $data['can_edit'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.default');
        }
        if ($oMbqEtForumPost->authorIconUrl->hasSetOriValue()) {
            $data['icon_url'] = (string) $oMbqEtForumPost->authorIconUrl->oriValue;
        }
        if ($oMbqEtForumPost->postTime->hasSetOriValue()) {
            $data['post_time'] = MbqMain::$oMbqCm->datetimeIso8601Encode($oMbqEtForumPost->postTime->oriValue);
        }
        if ($oMbqEtForumPost->allowSmilies->hasSetOriValue()) {
            $data['allow_smilies'] = (boolean) $oMbqEtForumPost->allowSmilies->oriValue;
        }
        if ($oMbqEtForumPost->position->hasSetOriValue()) {
            $data['position'] = (int) $oMbqEtForumPost->position->oriValue;
        }
        if ($oMbqEtForumPost->canThank->hasSetOriValue()) {
            $data['can_thank'] = (boolean) $oMbqEtForumPost->canThank->oriValue;
        } else {
            $data['can_thank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canThank.default');
        }
        if ($oMbqEtForumPost->thankCount->hasSetOriValue()) {
            $data['thank_count'] = (int) $oMbqEtForumPost->thankCount->oriValue;
        }
        if ($oMbqEtForumPost->canLike->hasSetOriValue()) {
            $data['can_like'] = (boolean) $oMbqEtForumPost->canLike->oriValue;
        } else {
            $data['can_like'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canLike.default');
        }
        if ($oMbqEtForumPost->isLiked->hasSetOriValue()) {
            $data['is_liked'] = (boolean) $oMbqEtForumPost->isLiked->oriValue;
        }
        if ($oMbqEtForumPost->likeCount->hasSetOriValue()) {
            $data['like_count'] = (int) $oMbqEtForumPost->likeCount->oriValue;
        }
        if ($oMbqEtForumPost->canDelete->hasSetOriValue()) {
            $data['can_delete'] = (boolean) $oMbqEtForumPost->canDelete->oriValue;
        } else {
            $data['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canDelete.default');
        }
        if ($oMbqEtForumPost->isDeleted->hasSetOriValue()) {
            $data['is_deleted'] = (boolean) $oMbqEtForumPost->isDeleted->oriValue;
        }
        if ($oMbqEtForumPost->canApprove->hasSetOriValue()) {
            $data['can_approve'] = (boolean) $oMbqEtForumPost->canApprove->oriValue;
        } else {
            $data['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canApprove.default');
        }
        if ($oMbqEtForumPost->isApproved->hasSetOriValue()) {
            $data['is_approved'] = (boolean) $oMbqEtForumPost->isApproved->oriValue;
        }
        if ($oMbqEtForumPost->canMove->hasSetOriValue()) {
            $data['can_move'] = (boolean) $oMbqEtForumPost->canMove->oriValue;
        } else {
            $data['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canMove.default');
        }
        if ($oMbqEtForumPost->modByUserId->hasSetOriValue()) {
            $data['moderated_by_id'] = (string) $oMbqEtForumPost->modByUserId->oriValue;
        }
        if ($oMbqEtForumPost->deleteByUserId->hasSetOriValue()) {
            $data['deleted_by_id'] = (string) $oMbqEtForumPost->deleteByUserId->oriValue;
        }
        if ($oMbqEtForumPost->deleteReason->hasSetOriValue()) {
            $data['delete_reason'] = (string) $oMbqEtForumPost->deleteReason->oriValue;
        }
        if ($oMbqEtForumPost->canReport->hasSetOriValue()) {
            $data['can_report'] = (boolean) $oMbqEtForumPost->canReport->oriValue;
        } else {
            $data['can_report'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canReport.default');
        }
        /* attachments */
        $oMbqRdEtAtt = MbqMain::$oClk->newObj('MbqRdEtAtt');
        $data['attachments'] = (array) $oMbqRdEtAtt->returnApiArrDataAttachment($oMbqEtForumPost->objsNotInContentMbqEtAtt);
        /* thanks_info */
        $oMbqRdEtThank = MbqMain::$oClk->newObj('MbqRdEtThank');
        $data['thanks_info'] = (array) $oMbqRdEtThank->returnApiArrDataThank($oMbqEtForumPost->objsMbqEtThank);
        /* likes_info.TODO */
        $data['likes_info'] = (array) array();
        return $data;
    }
    /**
     * return forum post json api data
     *
     * @param  Object  $oMbqEtForumPost
     * @return  Array
     */
    protected function returnAdvJsonApiDataForumPost($oMbqEtForumPost) {
        $data = array();
        if ($oMbqEtForumPost->postId->hasSetOriValue()) {
            $data['id'] = (string) $oMbqEtForumPost->postId->oriValue;
        }
        if ($oMbqEtForumPost->postTime->hasSetOriValue()) {
            $data['time'] = (int) $oMbqEtForumPost->postTime->oriValue;
        }
        if ($oMbqEtForumPost->oAuthorMbqEtUser) {
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $data['author'] = $oMbqRdEtUser->returnApiDataUser($oMbqEtForumPost->oAuthorMbqEtUser);
        }
        if ($oMbqEtForumPost->postContent->hasSetTmlDisplayValue()) {
            $data['content'] = (string) $oMbqEtForumPost->postContent->tmlDisplayValue;
        }
        $data['preview'] = (string) $oMbqEtForumPost->shortContent->oriValue;
        if ($oMbqEtForumPost->allowSmilies->hasSetOriValue()) {
            $data['smiley_off'] = (boolean) !$oMbqEtForumPost->allowSmilies->oriValue;  //!!!
        }
        if ($oMbqEtForumPost->objsNotInContentMbqEtAtt) {
            $oMbqRdEtAtt = MbqMain::$oClk->newObj('MbqRdEtAtt');
            $data['attachs'] = (array) $oMbqRdEtAtt->returnApiArrDataAttachment($oMbqEtForumPost->objsNotInContentMbqEtAtt);
        } else {
            $data['attachs'] = array();
        }
        $data['status'] = array();
        if ($oMbqEtForumPost->state->hasSetOriValue()) {
            $data['status']['is_pending'] = (boolean) $oMbqEtForumPost->state->oriValue;   //!!!
        }
        if ($oMbqEtForumPost->isDeleted->hasSetOriValue()) {
            $data['status']['is_deleted'] = (boolean) $oMbqEtForumPost->isDeleted->oriValue;
        }
        if ($oMbqEtForumPost->isLiked->hasSetOriValue()) {
            $data['status']['is_liked'] = (boolean) $oMbqEtForumPost->isLiked->oriValue;
        }
        if ($oMbqEtForumPost->isThanked->hasSetOriValue()) {
            $data['status']['is_thanked'] = (boolean) $oMbqEtForumPost->isThanked->oriValue;
        }
        $data['permission'] = array();
        if ($oMbqEtForumPost->canEdit->hasSetOriValue()) {
            $data['permission']['can_edit'] = (boolean) $oMbqEtForumPost->canEdit->oriValue;
        } else {
            $data['permission']['can_edit'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canEdit.default');
        }
        if ($oMbqEtForumPost->canApprove->hasSetOriValue()) {
            $data['permission']['can_approve'] = (boolean) $oMbqEtForumPost->canApprove->oriValue;
        } else {
            $data['permission']['can_approve'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canApprove.default');
        }
        if ($oMbqEtForumPost->canDelete->hasSetOriValue()) {
            $data['permission']['can_delete'] = (boolean) $oMbqEtForumPost->canDelete->oriValue;
        } else {
            $data['permission']['can_delete'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canDelete.default');
        }
        if ($oMbqEtForumPost->canMove->hasSetOriValue()) {
            $data['permission']['can_move'] = (boolean) $oMbqEtForumPost->canMove->oriValue;
        } else {
            $data['permission']['can_move'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canMove.default');
        }
        if ($oMbqEtForumPost->canLike->hasSetOriValue()) {
            $data['permission']['can_like'] = (boolean) $oMbqEtForumPost->canLike->oriValue;
        } else {
            $data['permission']['can_like'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canLike.default');
        }
        if ($oMbqEtForumPost->canUnlike->hasSetOriValue()) {
            $data['permission']['can_unlike'] = (boolean) $oMbqEtForumPost->canUnlike->oriValue;
        } else {
            $data['permission']['can_unlike'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canUnlike.default');
        }
        if ($oMbqEtForumPost->canThank->hasSetOriValue()) {
            $data['permission']['can_thank'] = (boolean) $oMbqEtForumPost->canThank->oriValue;
        } else {
            $data['permission']['can_thank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canThank.default');
        }
        if ($oMbqEtForumPost->canUnthank->hasSetOriValue()) {
            $data['permission']['can_unthank'] = (boolean) $oMbqEtForumPost->canUnthank->oriValue;
        } else {
            $data['permission']['can_unthank'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canUnthank.default');
        }
        if ($oMbqEtForumPost->canReport->hasSetOriValue()) {
            $data['permission']['can_report'] = (boolean) $oMbqEtForumPost->canReport->oriValue;
        } else {
            $data['permission']['can_report'] = (boolean) MbqBaseFdt::getFdt('MbqFdtForum.MbqEtForumPost.canReport.default');
        }
        return $data;
    }
    
    /**
     * return forum post array api data
     *
     * @param  Array  $objsMbqEtForumPost
     * @param  Boolean  $returnHtml
     * @return  Array
     */
    public function returnApiArrDataForumPost($objsMbqEtForumPost, $returnHtml = true) {
        $data = array();
        foreach ($objsMbqEtForumPost as $oMbqEtForumPost) {
            $data[] = $this->returnApiDataForumPost($oMbqEtForumPost, $returnHtml);
        }
        return $data;
    }
    
    /**
     * get forum post objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtForumPost() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one forum post by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtForumPost() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * process content for display in mobile app
     *
     * @return  String
     */
    public function processContentForDisplay() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * return quote post content
     *
     * @return  String
     */
    public function getQuotePostContent() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * return raw post content
     *
     * @return  String
     */
    public function getRawPostContent() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>
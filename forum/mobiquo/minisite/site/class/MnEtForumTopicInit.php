<?php
/**
 * MnEtForumTopic init class
 * 
 * @since  2013-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtForumTopicInit Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * init MnEtForumTopic object by record
     *
     * @param  Object  $recordForumTopic
     */
    public function initMnEtForumTopicByRecord($recordForumTopic) {
        $oMnEtForumTopic = MainApp::$oClk->newObj('MnEtForumTopic');
        $oMnEtForumInit = MainApp::$oClk->newObj('MnEtForumInit');
        $oMnEtUserInit = MainApp::$oClk->newObj('MnEtUserInit');
        $oMnEtForumPostInit = MainApp::$oClk->newObj('MnEtForumPostInit');
        if (property_exists($recordForumTopic, 'id')) {
            $oMnEtForumTopic->topicId->setOriValue($recordForumTopic->id);
        }
        if (property_exists($recordForumTopic, 'title')) {
            $oMnEtForumTopic->topicTitle->setOriValue($recordForumTopic->title);
        }
        if (property_exists($recordForumTopic, 'time')) {
            $oMnEtForumTopic->postTime->setOriValue($recordForumTopic->time);
        }
        if (property_exists($recordForumTopic, 'replies')) {
            $oMnEtForumTopic->replyNumber->setOriValue($recordForumTopic->replies);
            $oMnEtForumTopic->totalPostNum->setOriValue($recordForumTopic->replies + 1);
        }
        if (property_exists($recordForumTopic, 'views')) {
            $oMnEtForumTopic->viewNumber->setOriValue($recordForumTopic->views);
        }
        if (property_exists($recordForumTopic, 'forum')) {
            $oMnEtForumTopic->oMnEtForum = $oMnEtForumInit->initMnEtForumByRecord($recordForumTopic->forum);
        }
        if (property_exists($recordForumTopic, 'author')) {
            $oMnEtForumTopic->oAuthorMnEtUser = $oMnEtUserInit->initMnEtUserByRecord($recordForumTopic->author);
        }
        if (property_exists($recordForumTopic, 'prefix')) {
            $oMnEtForumTopic->prefixId->setOriValue($recordForumTopic->prefix->id);
            $oMnEtForumTopic->prefixName->setOriValue($recordForumTopic->prefix->name);
        }
        if (property_exists($recordForumTopic, 'status') && $recordForumTopic->status) {
            if (property_exists($recordForumTopic->status, 'is_unread')) {
                $oMnEtForumTopic->newPost->setOriValue($recordForumTopic->status->is_unread);
            }
            if (property_exists($recordForumTopic->status, 'is_follow')) {
                $oMnEtForumTopic->isSubscribed->setOriValue($recordForumTopic->status->is_follow);
            }
            if (property_exists($recordForumTopic->status, 'is_hot')) {
                $oMnEtForumTopic->isHot->setOriValue($recordForumTopic->status->is_hot);
            }
            if (property_exists($recordForumTopic->status, 'is_digest')) {
                $oMnEtForumTopic->isDigest->setOriValue($recordForumTopic->status->is_digest);
            }
            if (property_exists($recordForumTopic->status, 'is_closed')) {
                $oMnEtForumTopic->isClosed->setOriValue($recordForumTopic->status->is_closed);
            }
            if (property_exists($recordForumTopic->status, 'is_sticky')) {
                $oMnEtForumTopic->isSticky->setOriValue($recordForumTopic->status->is_sticky);
            }
            if (property_exists($recordForumTopic->status, 'is_pending')) {
                $oMnEtForumTopic->state->setOriValue((int) $recordForumTopic->status->is_pending);  //!!!
            }
            if (property_exists($recordForumTopic->status, 'is_deleted')) {
                $oMnEtForumTopic->isDeleted->setOriValue($recordForumTopic->status->is_deleted);
            }
        }
        if (property_exists($recordForumTopic, 'permission') && $recordForumTopic->permission) {
            if (property_exists($recordForumTopic->permission, 'can_reply')) {
                $oMnEtForumTopic->canReply->setOriValue($recordForumTopic->permission->can_reply);
            }
            if (property_exists($recordForumTopic->permission, 'can_edit')) {
                $oMnEtForumTopic->canRename->setOriValue($recordForumTopic->permission->can_edit);
            }
            if (property_exists($recordForumTopic->permission, 'can_follow')) {
                $oMnEtForumTopic->canSubscribe->setOriValue($recordForumTopic->permission->can_follow);
            }
            if (property_exists($recordForumTopic->permission, 'can_close')) {
                $oMnEtForumTopic->canClose->setOriValue($recordForumTopic->permission->can_close);
            }
            if (property_exists($recordForumTopic->permission, 'can_stick')) {
                $oMnEtForumTopic->canStick->setOriValue($recordForumTopic->permission->can_stick);
            }
            if (property_exists($recordForumTopic->permission, 'can_approve')) {
                $oMnEtForumTopic->canApprove->setOriValue($recordForumTopic->permission->can_approve);
            }
            if (property_exists($recordForumTopic->permission, 'can_delete')) {
                $oMnEtForumTopic->canDelete->setOriValue($recordForumTopic->permission->can_delete);
            }
            if (property_exists($recordForumTopic->permission, 'can_move')) {
                $oMnEtForumTopic->canMove->setOriValue($recordForumTopic->permission->can_move);
            }
        }
        if (property_exists($recordForumTopic, 'first_post')) {
            $oMnEtForumTopic->oFirstMnEtForumPost = $oMnEtForumPostInit->initMnEtForumPostByRecord($recordForumTopic->first_post);
        }
        if (property_exists($recordForumTopic, 'last_post')) {
            $oMnEtForumTopic->oLastMbqEtForumPost = $oMnEtForumPostInit->initMnEtForumPostByRecord($recordForumTopic->last_post);
        }
        return $oMnEtForumTopic;
    }
    
    /**
     * init objsMnEtForum by records
     *
     * @param  Array  $recordsForum
     */
    public function initObjsMnEtForumTopicByRecords($recordsForumTopic) {
        $objsMnEtForumTopic = array();
        foreach ($recordsForumTopic as $recordForumTopic) {
            $objsMnEtForumTopic[] = $this->initMnEtForumTopicByRecord($recordForumTopic);
        }
        return $objsMnEtForumTopic;
    }
    
}

?>
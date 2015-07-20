<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum topic class
 * 
 * @since  2012-7-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtForumTopic extends MbqBaseEntity {
    
    /* some properties comes from $this->oFirstMbqEtForumPost */
    public $totalPostNum;   /* total number of posts in this topic */
    public $topicId;
    public $forumId;
    public $firstPostId;    /* first post id.in some appcations the first post id = topic id perhaps. */
    public $topicTitle;
    public $topicContent;
    public $shortContent;
    public $prefixId;
    public $prefixName;
    public $topicAuthorId;
    public $lastReplyAuthorId;
    public $attachmentIdArray;
    public $groupId;
    public $state;  /* 1 = post is success but need moderation. Otherwise no need to return this key. */
    public $isSubscribed;   /* return true if this thread has been subscribed by this user */
    public $canSubscribe;   /* returns false if the subscription feature is turned off */
    public $isClosed;       /* return true if this thread has been closed. */
    public $postTime;       /* timestamp. If this topic has no reply, use the topic creation time. */
    public $lastReplyTime;  /* timestamp. If this topic has no reply, use the topic creation time. */
    public $replyNumber;    /* total number of reply in this topic. If this is no reply in this return, return 0. */
    public $newPost;        /* return true if this topic contains new post since user last login */
    public $viewNumber;     /* total number of view in this topic */
    public $participatedUids;
    public $canThank;
    public $thankCount;
    public $canLike;
    public $isLiked;
    public $likeCount;
    public $canDelete;
    public $isDeleted;
    public $canApprove;
    public $isApproved;
    public $canStick;   /* return true if the user has authority to stick or unstick this topic. */
    public $isSticky;   /* return true if this topic is set as sticky mode. */
    public $canClose;   /* return true if the user has authority to close this topic. */
    public $canRename;  /* return true if the user has authority to rename this topic. */
    public $canMove;    /* return true if the user has authority to move this topic to somewhere else. */
    public $isMoved;
    public $modByUserId;    /* If this topic has already been moderated, return the user id of the person who moderated this topic */
    public $deleteByUserId; /* return the user id of the person who has previously soft-deleted this topic */
    public $deleteReason;   /* return reason of deletion, if any. */
    public $canReply;
    public $canReport;
    public $authorIconUrl;  /* author icon url */
    public $isHot;          /* Topic is hot or not */
    public $isDigest;       /* Topic is digest or not */
    public $realTopicId;
    
    public $oMbqEtForum;
    public $oFirstMbqEtForumPost;
    public $oLastMbqEtForumPost;
    /* the dummy first post of this topic when the topic itself is the first post.this post id format is:topic_topicId,for example:topic_123 */
    public $oDummyFirstMbqEtForumPost;
    public $oAuthorMbqEtUser;
    public $oLastReplyMbqEtUser;
    public $objsMbqEtAtt;
    public $objsMbqEtForumPost;
    public $objsBreadcrumbMbqEtForum;
    
    public function __construct() {
        parent::__construct();
        $this->totalPostNum = clone MbqMain::$simpleV;
        $this->topicId = clone MbqMain::$simpleV;
        $this->forumId = clone MbqMain::$simpleV;
        $this->firstPostId = clone MbqMain::$simpleV;
        $this->topicTitle = clone MbqMain::$simpleV;
        $this->topicContent = clone MbqMain::$simpleV;
        $this->shortContent = clone MbqMain::$simpleV;
        $this->prefixId = clone MbqMain::$simpleV;
        $this->prefixName = clone MbqMain::$simpleV;
        $this->topicAuthorId = clone MbqMain::$simpleV;
        $this->lastReplyAuthorId = clone MbqMain::$simpleV;
        $this->attachmentIdArray = clone MbqMain::$simpleV;
        $this->state = clone MbqMain::$simpleV;
        $this->groupId = clone MbqMain::$simpleV;
        $this->isSubscribed = clone MbqMain::$simpleV;
        $this->canSubscribe = clone MbqMain::$simpleV;
        $this->isClosed = clone MbqMain::$simpleV;
        $this->postTime = clone MbqMain::$simpleV;
        $this->lastReplyTime = clone MbqMain::$simpleV;
        $this->replyNumber = clone MbqMain::$simpleV;
        $this->newPost = clone MbqMain::$simpleV;
        $this->viewNumber = clone MbqMain::$simpleV;
        $this->participatedUids = clone MbqMain::$simpleV;
        $this->canThank = clone MbqMain::$simpleV;
        $this->thankCount = clone MbqMain::$simpleV;
        $this->canLike = clone MbqMain::$simpleV;
        $this->isLiked = clone MbqMain::$simpleV;
        $this->likeCount = clone MbqMain::$simpleV;
        $this->canDelete = clone MbqMain::$simpleV;
        $this->isDeleted = clone MbqMain::$simpleV;
        $this->canApprove = clone MbqMain::$simpleV;
        $this->isApproved = clone MbqMain::$simpleV;
        $this->canStick = clone MbqMain::$simpleV;
        $this->isSticky = clone MbqMain::$simpleV;
        $this->canClose = clone MbqMain::$simpleV;
        $this->canRename = clone MbqMain::$simpleV;
        $this->canMove = clone MbqMain::$simpleV;
        $this->isMoved = clone MbqMain::$simpleV;
        $this->modByUserId = clone MbqMain::$simpleV;
        $this->deleteByUserId = clone MbqMain::$simpleV;
        $this->deleteReason = clone MbqMain::$simpleV;
        $this->canReply = clone MbqMain::$simpleV;
        $this->canReport = clone MbqMain::$simpleV;
        $this->authorIconUrl = clone MbqMain::$simpleV;
        $this->isHot = clone MbqMain::$simpleV;
        $this->isDigest = clone MbqMain::$simpleV;
        $this->realTopicId = clone MbqMain::$simpleV;
        
        $this->oMbqEtForum = NULL;
        $this->oFirstMbqEtForumPost = NULL;
        $this->oLastMbqEtForumPost = NULL;
        $this->oDummyFirstMbqEtForumPost = NULL;
        $this->oAuthorMbqEtUser = NULL;
        $this->oLastReplyMbqEtUser = NULL;
        $this->objsMbqEtAtt = array();
        $this->objsMbqEtForumPost = array();
        $this->objsBreadcrumbMbqEtForum = NULL;
    }
  
}

?>
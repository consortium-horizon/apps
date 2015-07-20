<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum post class
 * 
 * @since  2012-7-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtForumPost extends MbqBaseEntity {
    
    public $postId;
    public $parentPostId;    /* parent post id */
    public $forumId;
    public $topicId;
    public $postTitle;
    public $postContent;
    public $shortContent;
    public $postAuthorId;
    public $attachmentIdArray;
    public $groupId;
    public $state;      /* 1 = post is success but need moderation. Otherwise no need to return this key */
    public $isOnline;
    public $canEdit;
    public $postTime;   /* timestamp */
    public $allowSmilies;
    public $position;
    public $canThank;
    public $thankCount;
    public $canLike;
    public $isLiked;
    public $isThanked;
    public $likeCount;
    public $canDelete;
    public $isDeleted;
    public $canApprove;
    public $isApproved;
    public $canMove;        /* return true if the user has authority to move this post to somewhere else. */
    public $modByUserId;    /* If this post has already been moderated, return the user id of the person who moderated this post */
    public $deleteByUserId; /* return the user id of the person who has previously soft-deleted this post */
    public $deleteReason;   /* return reason of deletion, if any. */
    public $authorIconUrl;  /* author icon url */
    public $canReport;
    public $canUnlike;
    public $canUnthank;
    
    public $isDummyForumPost;   /* boolean value(default is false),the flag to judge the dummy forum post,used for the topic when itself is the first post */
        
    public $oMbqEtForum;
    public $oMbqEtForumTopic;
    public $oAuthorMbqEtUser;
    public $objsMbqEtAtt;           /* the all attachment objs in this post. */
    public $objsNotInContentMbqEtAtt;   /* the attachement objs not in the content of this post. */
    public $objsMbqEtThank;
    public $objsMbqEtLike;
    
    public function __construct() {
        parent::__construct();
        $this->postId = clone MbqMain::$simpleV;
        $this->parentPostId = clone MbqMain::$simpleV;
        $this->forumId = clone MbqMain::$simpleV;
        $this->topicId = clone MbqMain::$simpleV;
        $this->postTitle = clone MbqMain::$simpleV;
        $this->postContent = clone MbqMain::$simpleV;
        $this->shortContent = clone MbqMain::$simpleV;
        $this->postAuthorId = clone MbqMain::$simpleV;
        $this->attachmentIdArray = clone MbqMain::$simpleV;
        $this->groupId = clone MbqMain::$simpleV;
        $this->state = clone MbqMain::$simpleV;
        $this->isOnline = clone MbqMain::$simpleV;
        $this->canEdit = clone MbqMain::$simpleV;
        $this->postTime = clone MbqMain::$simpleV;
        $this->allowSmilies = clone MbqMain::$simpleV;
        $this->position = clone MbqMain::$simpleV;
        $this->canThank = clone MbqMain::$simpleV;
        $this->thankCount = clone MbqMain::$simpleV;
        $this->canLike = clone MbqMain::$simpleV;
        $this->isLiked = clone MbqMain::$simpleV;
        $this->isThanked = clone MbqMain::$simpleV;
        $this->likeCount = clone MbqMain::$simpleV;
        $this->canDelete = clone MbqMain::$simpleV;
        $this->isDeleted = clone MbqMain::$simpleV;
        $this->canApprove = clone MbqMain::$simpleV;
        $this->isApproved = clone MbqMain::$simpleV;
        $this->canMove = clone MbqMain::$simpleV;
        $this->modByUserId = clone MbqMain::$simpleV;
        $this->deleteByUserId = clone MbqMain::$simpleV;
        $this->deleteReason = clone MbqMain::$simpleV;
        $this->authorIconUrl = clone MbqMain::$simpleV;
        $this->canReport = clone MbqMain::$simpleV;
        $this->canUnlike = clone MbqMain::$simpleV;
        $this->canUnthank = clone MbqMain::$simpleV;
        
        $this->isDummyForumPost = clone MbqMain::$simpleV;
        $this->isDummyForumPost->setOriValue(false);
        
        $this->oMbqEtForum = NULL;
        $this->oMbqEtForumTopic = NULL;
        $this->oAuthorMbqEtUser = NULL;
        $this->objsMbqEtAtt = array();
        $this->objsMbqEtThank = array();
        $this->objsMbqEtLike = array();
        $this->objsNotInContentMbqEtAtt = array();
    }
  
}

?>
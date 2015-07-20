<?php

defined('MBQ_IN_IT') or exit;

/**
 * user class
 * 
 * @since  2012-7-13
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtUser extends MbqBaseEntity {
    
    public $userId;
    public $loginName;
    public $password;
    public $userName;   /* for display */
    public $userGroupIds;   /* usergroup ids that this user belongs to. */
    public $iconUrl;
    public $userEmail;
    public $postCount;  /* Return total number of post of this user */
    public $canPm;
    public $canSendPm;
    public $canModerate;
    public $canSearch;
    public $canWhosonline;
    public $canUploadAvatar;
    public $maxAttachment;
    public $maxPngSize;
    public $maxJpgSize;
    public $displayText;    /* user signature or self-introduction */
    public $regTime;        /* timestamp */
    public $lastActivityTime;   /* timestamp */
    public $isOnline;
    public $acceptPm;
    public $iFollowU;
    public $uFollowMe;
    public $acceptFollow;
    public $followingCount;     /* number of person this user is following to */
    public $follower;           /* number of person following this user */
    public $currentAction;  /* If the user is currently online, return what the user is currently doing */
    public $topicId;    /* Required if [TOPIC] BBCode is presented in the display_text above */
    public $canBan;     /* return true if current user can ban this user */
    public $isBan;      /* return true if this user has been baned */
    public $canMarkSpam;
    public $isSpam;
    public $reputation;     /* reputation system provides a way of rating users based on the quality of their posts. Users can add or subtract reputation points from other users.  */
    public $customFieldsList;
    public $postCountdown;
    
    public $pmUnreadCount;      /* returns total number of private message with unread message inside. */
    public $pcUnreadCount;   /* returns total number of private conversations with unread message inside. */
    public $subscribedTopicUnreadCount; /* returns total number of subscribed topics that are unread. */
    
    public function __construct() {
        parent::__construct();
        $this->userId = clone MbqMain::$simpleV;
        $this->loginName = clone MbqMain::$simpleV;
        $this->password = clone MbqMain::$simpleV;
        $this->userName = clone MbqMain::$simpleV;
        $this->userGroupIds = clone MbqMain::$simpleV;
        $this->iconUrl = clone MbqMain::$simpleV;
        $this->userEmail = clone MbqMain::$simpleV;
        $this->postCount = clone MbqMain::$simpleV;
        $this->canPm = clone MbqMain::$simpleV;
        $this->canSendPm = clone MbqMain::$simpleV;
        $this->canModerate = clone MbqMain::$simpleV;
        $this->canSearch = clone MbqMain::$simpleV;
        $this->canWhosonline = clone MbqMain::$simpleV;
        $this->canUploadAvatar = clone MbqMain::$simpleV;
        $this->maxAttachment = clone MbqMain::$simpleV;
        $this->maxPngSize = clone MbqMain::$simpleV;
        $this->maxJpgSize = clone MbqMain::$simpleV;
        $this->displayText = clone MbqMain::$simpleV;
        $this->regTime = clone MbqMain::$simpleV;
        $this->lastActivityTime = clone MbqMain::$simpleV;
        $this->isOnline = clone MbqMain::$simpleV;
        $this->acceptPm = clone MbqMain::$simpleV;
        $this->iFollowU = clone MbqMain::$simpleV;
        $this->uFollowMe = clone MbqMain::$simpleV;
        $this->acceptFollow = clone MbqMain::$simpleV;
        $this->followingCount = clone MbqMain::$simpleV;
        $this->follower = clone MbqMain::$simpleV;
        $this->currentAction = clone MbqMain::$simpleV;
        $this->topicId = clone MbqMain::$simpleV;
        $this->canBan = clone MbqMain::$simpleV;
        $this->isBan = clone MbqMain::$simpleV;
        $this->canMarkSpam = clone MbqMain::$simpleV;
        $this->isSpam = clone MbqMain::$simpleV;
        $this->reputation = clone MbqMain::$simpleV;
        $this->postCountdown = clone MbqMain::$simpleV;
        $this->customFieldsList = clone MbqMain::$simpleV;
        
        $this->pmUnreadCount = clone MbqMain::$simpleV;
        $this->pcUnreadCount = clone MbqMain::$simpleV;
        $this->subscribedTopicUnreadCount = clone MbqMain::$simpleV;
    }
    
    /**
     * normally,we return $this->loginName if $this->userName is invalid.
     *
     * @return  String
     */
    public function getDisplayName() {
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        return $oMbqRdEtUser->getDisplayName($this);
    }
  
}

?>
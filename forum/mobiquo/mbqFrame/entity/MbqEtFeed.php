<?php

defined('MBQ_IN_IT') or exit;

/**
 * feed class
 * 
 * @since  2012-7-17
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtFeed extends MbqBaseEntity {
    
    public $key;    /* any key associated with this feed,except userId/topicId/postId */
    public $userId; /* the user associated with this feed */
    public $topicId;    /* the topic associated with this feed */
    public $postId;    /* the post associated with this feed */
    public $type;   /* user/topic/post or other anything */
    public $newFeed;   /* to indicate whether this feed is an unread feed. */
    public $message;    /* message to be displayed in the app about this feed.  */
    public $postTime;   /* timestamp */
    
    public function __construct() {
        parent::__construct();
        $this->key = clone MbqMain::$simpleV;
        $this->userId = clone MbqMain::$simpleV;
        $this->topicId = clone MbqMain::$simpleV;
        $this->postId = clone MbqMain::$simpleV;
        $this->type = clone MbqMain::$simpleV;
        $this->newFeed = clone MbqMain::$simpleV;
        $this->message = clone MbqMain::$simpleV;
        $this->postTime = clone MbqMain::$simpleV;
    }
  
}

?>
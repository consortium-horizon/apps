<?php

defined('MBQ_IN_IT') or exit;

/**
 * application environment base class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseAppEnv {
    
    //the following is the proposed properties may need be used by your application environment.
    public $db;         //application db
    public $user;       //application current login user
    public $config;     //application config
    public $cache;      //application cache
    
    public $forumTree;  //forum tree
    
    public function __construct() {
        $this->forumTree = NULL;
    }
    
    /**
     * application environment init
     */
    abstract protected function init();
    
    /**
     * check whether a 3rd plugin is enabled
     *
     * @return  Boolean
     */
    public function check3rdPluginEnabled() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * return $this->forumTree,make it if it is NULL.
     *
     * @return  Array
     */
    public function returnForumTree() {
        if (is_null($this->forumTree)) {
            $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
            $this->forumTree = $oMbqRdEtForum->getForumTree();
        }
        return $this->forumTree;
    }
  
}

?>
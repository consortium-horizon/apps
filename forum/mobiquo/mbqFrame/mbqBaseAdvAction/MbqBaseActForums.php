<?php

defined('MBQ_IN_IT') or exit;

/**
 * forums action
 * 
 * @since  2013-5-11
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActForums extends MbqBaseAct {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    protected function actionImplement() {
        if (!MbqMain::$oMbqConfig->moduleIsEnable('forum')) {
            MbqError::alert('', "Not support module forum!", '', MBQ_ERR_NOT_SUPPORT);
        }
        $oMbqRdEtForum = MbqMain::$oClk->newObj('MbqRdEtForum');
        $tree = $oMbqRdEtForum->getForumTree();
        $this->data = $oMbqRdEtForum->returnApiTreeDataForum($tree);
    }
  
}

?>
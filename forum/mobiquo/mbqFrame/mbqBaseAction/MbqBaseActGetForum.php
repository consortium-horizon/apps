<?php

defined('MBQ_IN_IT') or exit;

/**
 * get_forum action
 * 
 * @since  2012-8-3
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseActGetForum extends MbqBaseAct {
    
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
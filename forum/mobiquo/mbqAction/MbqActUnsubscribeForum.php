<?php

defined('MBQ_IN_IT') or exit;

MbqMain::$oClk->includeClass('MbqBaseActUnsubscribeForum');

/**
 * unsubscribe_forum action
 * 
 * @since  2012-9-15
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqActUnsubscribeForum extends MbqBaseActUnsubscribeForum {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * action implement
     */
    public function actionImplement() {
        parent::actionImplement();
    }
  
}

?>
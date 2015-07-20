<?php

MainApp::$oClk->includeClass('MbqEtSubscribe');

/**
 * subscribe class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtSubscribe extends MbqEtSubscribe {
    
    public $oMnEtUser; /* user who subscribed this */
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtUser = NULL;
    }
  
}

?>
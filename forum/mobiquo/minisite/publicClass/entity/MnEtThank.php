<?php

MainApp::$oClk->includeClass('MbqEtThank');

/**
 * thank class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtThank extends MbqEtThank {
    
    public $oMnEtUser; /* user who thanked this */
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtUser = NULL;
    }
  
}

?>
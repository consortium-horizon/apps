<?php

MainApp::$oClk->includeClass('MbqEtPm');

/**
 * private message class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtPm extends MbqEtPm {
    
    public $oMnEtPmBox;
    public $oFirstRecipientMnEtUser;
    public $oAuthorMnEtUser;
    public $objsRecipientMnEtUser;   /* users be invited to join this private message */
    
    public function __construct() {
        parent::__construct();
        
        $this->oMnEtPmBox = NULL;
        $this->oFirstRecipientMnEtUser = NULL;
        $this->oAuthorMnEtUser = NULL;
        $this->objsRecipientMnEtUser = array();
    }
  
}

?>
<?php

MainApp::$oClk->includeClass('MbqEtPcMsg');

/**
 * private conversation message class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtPcMsg extends MbqEtPcMsg {
    
    public $oAuthorMnEtUser;
    public $objsMnEtAtt;           /* the all attachment objs in this post. */
    public $objsNotInContentMnEtAtt;   /* the attachement objs not in the content of this post. */
    
    public function __construct() {
        parent::__construct();
        
        $this->oAuthorMnEtUser = NULL;
        $this->objsMnEtAtt = array();
        $this->objsNotInContentMnEtAtt = array();
    }
  
}

?>
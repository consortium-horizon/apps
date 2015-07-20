<?php

defined('MBQ_IN_IT') or exit;

/**
 * forum smilie class
 * 
 * @since  2012-7-12
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqEtForumSmilie extends MbqBaseEntity {
    
    public $categoryName;
    public $code;
    public $url;
    public $title;
    
    public function __construct() {
        parent::__construct();
        $this->categoryName = clone MbqMain::$simpleV;
        $this->code = clone MbqMain::$simpleV;
        $this->url = clone MbqMain::$simpleV;
        $this->title = clone MbqMain::$simpleV;
    }
  
}

?>
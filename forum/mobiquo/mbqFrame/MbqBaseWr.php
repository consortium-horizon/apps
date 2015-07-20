<?php

defined('MBQ_IN_IT') or exit;

/**
 * write base class
 * 
 * @since  2012-8-9
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseWr {
    
    public $neededMethod;   /* describe the methods that should be implemented in all extention class. */
    
    public function __construct() {
        $this->neededMethods = array();
    }
  
}

?>
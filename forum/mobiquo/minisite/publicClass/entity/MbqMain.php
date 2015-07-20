<?php

/**
 * dummy MbqMain class
 * 
 * @since  2013-8-5
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqMain {
    
    public static $simpleV;   /* an empty MbqValue object for simple value initialization */
  
}

MbqMain::$simpleV = MainApp::$oClk->newObj('MbqValue');

?>
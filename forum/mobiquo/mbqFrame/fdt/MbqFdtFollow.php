<?php

defined('MBQ_IN_IT') or exit;

/**
 * follow module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtFollow extends MbqBaseFdt {
    
    public static $df = array(
        
    );
  
}
MbqBaseFdt::$df['MbqFdtFollow'] = &MbqFdtFollow::$df;

?>
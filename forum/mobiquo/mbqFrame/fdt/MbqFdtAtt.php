<?php

defined('MBQ_IN_IT') or exit;

/**
 * attachment field definition class
 * 
 * @since  2012-8-14
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtAtt extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtAtt' => array(
            'attType' => array(
                'range' => array(
                    'forumPostAtt' => 'forumPostAtt',
                    'userAvatar' => 'userAvatar',
                    'pcMsgAtt' => 'pcMsgAtt'
                )
            ),
            'contentType' => array(
                'range' => array(
                    'image' => 'image',
                    'pdf' => 'pdf',
                    'other' => 'other'
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtAtt'] = &MbqFdtAtt::$df;

?>
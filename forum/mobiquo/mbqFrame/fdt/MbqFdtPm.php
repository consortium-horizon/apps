<?php

defined('MBQ_IN_IT') or exit;

/**
 * pm module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtPm extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtPm' => array(
            'isRead' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isReply' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isForward' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isUnread' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        ),
        'MbqEtPmBox' => array(
            'boxType' => array(
                'range' => array(
                    'inbox' => 'INBOX',
                    'sent' => 'SENT'
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtPm'] = &MbqFdtPm::$df;

?>
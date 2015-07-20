<?php

defined('MBQ_IN_IT') or exit;

/**
 * subscribe module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtSubscribe extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtSubscribe' => array(
            'subscribeMode' => array(
                'range' => array(
                    'noEmailNotificationOrThroughMyControlPanelOnly' => 0,
                    'instantNotificationByEmail' => 1,
                    'dailyUpdatesByEmail' => 2,
                    'weeklyUpdatesByEmail' => 3
                )
            ),
            'type' => array(
                'range' => array(
                    'forum' => 'forum',
                    'topic' => 'topic'
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtSubscribe'] = &MbqFdtSubscribe::$df;

?>
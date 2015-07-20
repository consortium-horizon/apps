<?php

defined('MBQ_IN_IT') or exit;

/**
 * pc module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtPc extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtPc' => array(
            'newPost' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canInvite' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canEdit' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canClose' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isClosed' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'deleteMode' => array(
                'range' => array(
                    'soft-delete' => 1,
                    'hard-delete' => 2,
                    'soft-and-hard-delete' => 3
                )
            )
        ),
        'MbqEtPcMsg' => array(
            'isUnread' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'hasLeft' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'newPost' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtPc'] = &MbqFdtPc::$df;

?>
<?php

defined('MBQ_IN_IT') or exit;

/**
 * user module field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtUser extends MbqBaseFdt {
    
    public static $df = array(
        'MbqEtUser' => array(
            'canPm' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canSendPm' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canModerate' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canSearch' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canWhosonline' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canUploadAvatar' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isOnline' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'acceptPm' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'iFollowU' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'uFollowMe' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'acceptFollow' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canBan' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isBan' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'canMarkSpam' => array(
                'default' => false,
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            ),
            'isSpam' => array(
                'range' => array(
                    'yes' => true,
                    'no' => false
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtUser'] = &MbqFdtUser::$df;

?>
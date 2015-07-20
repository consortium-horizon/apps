<?php

defined('MBQ_IN_IT') or exit;

/**
 * config field definition class
 * 
 * @since  2012-7-18
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqFdtConfig extends MbqBaseFdt {
    
    public static $df = array(
        'otherDefine' => array(
            'cfgValueType' => array(
                /* 'xmlrpc' means used for xmlrpc type,'adv' means used for adv type,'all' means used for both xmlrpc type and adv type. */
                'range' => array(
                    'xmlrpc' => 'xmlrpc',
                    'adv' => 'adv',
                    'all' => 'all'
                ) 
            )
        ),
        'base' => array(
            'is_open' => array(
                'default' => false,         /* default value */
                'range' => array (      /* value range */
                    'yes' => true,
                    'no' => false
                )
            ),
            'inbox_stat' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'announcement' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'disable_bbcode' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 0,
                    'disable' => 1
                )
            ),
            'push' => array(
                'default' => 0,
                'range' => array(
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'push_type' => array(
                'default' => '',
            ),
            'version' => array(
                'default' => 'dev',
            ),
            'sys_version' => array(
                'default' => '',
            ),
            'api' => array(
                'default' => array(),
                'cfgValueType' => 'adv'
            )
        ),
        'user' => array(
            'module_enable' => array(
                'default' => 1,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            ),
            'reg_url' => array(
                'default' => '/'
            ),
            'guest_okay' => array(
                'default' => false,
                'range' => array (
                    'support' => true,
                    'notSupport' => false
                )
            ),
            'anonymous' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'guest_whosonline' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'avatar' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'support_md5' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_smilies' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'advanced_online_users' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'user_id' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'upload_avatar' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'sign_in' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'inappreg' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'sso_login' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'sso_signin' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'sso_register' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'native_register' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            )
        ),
        'forum' => array(
            'module_enable' => array(
                'default' => 1,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            ),
            'report_post' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'goto_post' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'goto_unread' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mark_read' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mark_forum' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'no_refresh_on_post' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'subscribe_forum' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_latest_topic' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_id_by_url' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'delete_reason' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mod_approve' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mod_delete' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mod_report' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'guest_search' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'subscribe_load' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'subscribe_topic_mode' => array(
                'default' => '',
                'range' => array (
                    'noOption' => '',
                    'noNotification' => 0,
                    'instantly' => 1,
                    'daily' => 2,
                    'weekly' => 3,
                    'onlyWhenOffline' => 4
                )
            ),
            'subscribe_forum_mode' => array(
                'default' => '',
                'range' => array (
                    'noOption' => '',
                    'noNotification' => 0,
                    'instantly' => 1,
                    'daily' => 2,
                    'weekly' => 3,
                    'onlyWhenOffline' => 4
                )
            ),
            'min_search_length' => array(
                'default' => 3
            ),
            'multi_quote' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'default_smilies' => array(
                'default' => 1,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'can_unread' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_forum' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_topic_status' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_participated_forum' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'get_forum_status' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'advanced_search' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mark_topic_read' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'advanced_delete' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'first_unread' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'max_attachment' => array(
                'default' => 20
            ),
            'soft_delete' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'system' => array(
                'default' => '',
                'cfgValueType' => 'adv'     /* this field only used to mark adv cfgValueType */
            ),
            'offline' => array(
                'default' => true,
                'range' => array (
                    'yes' => true,
                    'no' => false
                ),
                'cfgValueType' => 'adv' 
            ),
            'private' => array(
                'default' => false,
                'range' => array (
                    'yes' => true,
                    'no' => false
                ),
                'cfgValueType' => 'adv'
            ),
            'charset' => array(
                'default' => 'UTF-8',
                'cfgValueType' => 'adv'
            ),
            'timezone' => array(
                'default' => 0,
                'cfgValueType' => 'adv'
            )
        ),
        'pm' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            ),
            'report_pm' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'pm_load' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            ),
            'mark_pm_unread' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            )
        ),
        'pc' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            ),
            'conversation' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            )
        ),
        'like' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            )
        ),
        'subscribe' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            ),
            'mass_subscribe' => array(
                'default' => 0,
                'range' => array (
                    'support' => 1,
                    'notSupport' => 0
                )
            )
        ),
        'thank' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            )
        ),
        'follow' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            )
        ),
        'feed' => array(
            'module_enable' => array(
                'default' => 0,
                'range' => array (
                    'enable' => 1,
                    'disable' => 0
                )
            )
        )
    );
  
}
MbqBaseFdt::$df['MbqFdtConfig'] = &MbqFdtConfig::$df;

?>
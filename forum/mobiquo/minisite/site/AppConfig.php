<?php
define('MPF_C_APPNAME', 'SITE');    /* 模块名 */
require_once('../MpfGlobalConfig.php');

require_once('../tapatalkPluginApiConfig.php');

/** 
 * 模块配置 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */

/* 模块自定义常量 */
/* constants and classes for mobiquo */
define('MBQ_IN_IT', true);  /* is in mobiquo flag */
define('MBQ_DS', DIRECTORY_SEPARATOR);
define('MBQ_PATH', dirname(__FILE__).MBQ_DS.'..'.MBQ_DS.'..'.MBQ_DS);    /* mobiquo path */
define('MBQ_FRAME_PATH', MBQ_PATH.'mbqFrame'.MBQ_DS);    /* frame path */
/* error constant */
define('MBQ_ERR_TOP', 1);   /* the worst error that must stop the program immediately.we often use this constant in plugin development. */
define('MBQ_ERR_HIGH', 3);  /* serious error that must stop the program immediately for display in html page.we need not use this constant in plugin development,but can use it in other projects development perhaps. */
define('MBQ_ERR_NOT_SUPPORT', 5);  /* not support corresponding function error that must stop the program immediately. */
define('MBQ_ERR_APP', 7);   /* normal error that maked by program logic can be displayed,the program can works continue or not. */
define('MBQ_ERR_INFO', 9);  /* success info that maked by program logic can be displayed,the program can works continue or not. */
define('MBQ_ERR_TOP_NOIO', 11);  /* the worst error that must stop the program immediately and then the MbqIo is not valid,will output error info and stop the program immediately. */
define('MBQ_ENTITY_PATH', MBQ_FRAME_PATH.'entity'.MBQ_DS);    /* entity class path */
define('MBQ_FDT_PATH', MBQ_FRAME_PATH.'fdt'.MBQ_DS);    /* fdt class path */

define('MPF_SITE_DEFAULT_PAGE_NUM', 1);    /* default page num */
define('MPF_SITE_DEFAULT_PER_PAGE_NUM', 20);    /* default per page num */
define('MPF_SITE_API_ERROR', 'apiError');    /* api error */

/** 
 * 模块配置类 
 */
Class AppConfig Extends AppConfigBase {

    /**
     * 构造函数
     */
    public function __construct() {
        global $mpf_config;
        parent::__construct();
        
        $this->appCfg['db'] = array (    /* 数据库配置，只有底层程序可以直接访问这个配置，其他程序一律不得直接访问。 */
            'dbName' => '',  /* 数据库名 */
            'ip' => '',  /* ip地址 */
            'user' => '',  /* 用户名 */
            'pass' => ''  /* 密码 */
        );
        
        $this->interfaceSetting = array (    /* 模块间接口配置，只有底层程序可以直接访问这个配置，其他程序一律不得直接访问。 */
            'apps' => $mpf_config['apps'],  /* 各个模块的配置 */
            'interface' => array (  /* 接口配置 */
            )
        );
    }
    
}

?>
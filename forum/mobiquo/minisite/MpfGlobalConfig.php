<?php

/** 
 * 全局配置（各个模块共用的配置）
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
//error_reporting(E_ALL);
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
set_time_limit(60);
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

/* 框架相关常量 */
define('MPFFRAME_PATH', '../MYPHPFRAME/');   /* 框架目录路径 */
require_once(MPFFRAME_PATH.'FrameCommon.php');
/* smarty运行文件目录。存放对应的templates、templates_c、configs、cache文件。 */
define('SMARTY_RUN_DIR', $oCf->getPath('./smarty_run/'));
require_once(MPFFRAME_PATH.'class/AppConfigBase.php');  /* 包含模块配置基类 */

define('MPF_IN_IT', TRUE);    /* judge is in mpf */
define('MPF_C_MAIN_HOMEDOMAIN', $_SERVER['HTTP_HOST']);    /* 主站域名 */
define('MPF_C_HOMEDOMAIN', $_SERVER['HTTP_HOST']);    /* 模块域名 */
define('MPF_C_PREURL', (strrpos($_SERVER['REQUEST_URI'], '/') === 0) ? '' : substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')));    /* !!! 模块URL前缀（含模块目录名），如果没有前缀则返回空的字符串。 */
define('MPF_C_COOKIE_DOMAIN', '.'.$_SERVER['HTTP_HOST']);    /* 网站cookie域 */
define('MPF_C_COOKIE_LEFT_TIME', 0);    /* cookie有效期 */
define('MPF_C_COOKIE_PATH', '/');    /* cookie路径 */
define('MPF_C_COOKIE_SECURE', false);    /* cookie secure */
define('MPF_C_COOKIE_HTTPONLY', false);    /* cookie httponly */
define('MPF_C_SESSION_NAME', 'coolSession');    /* session名称 */
define('MPF_C_THEME', 'default');    /* 主题 */
define('MPF_C_JS_PUBLIC_URL', '../public/js/public/');    /* 公共js目录 */
define('MPF_C_CSS_PUBLIC_URL', '../public/css/'.MPF_C_THEME.'/public/');    /* 公共css目录 */
define('MPF_C_CSS_PUBLIC_ADMIN_URL', '../public/css/'.MPF_C_THEME.'/public/admin/');    /* 公共后台css目录 */
define('MPF_C_IMG_PUBLIC_URL', '../public/img/'.MPF_C_THEME.'/public/');    /* 公共img目录 */
define('MPF_C_FLASH_PUBLIC_URL', '../public/flash/public/');    /* 公共flash目录 */

/* 模块相关常量 */
define('MPF_C_APP_JS_URL', '../public/js/'.strtolower(MPF_C_APPNAME).'/');    /* 模块js目录 */
define('MPF_C_APP_CSS_URL', '../public/css/'.MPF_C_THEME.'/'.strtolower(MPF_C_APPNAME).'/');    /* 模块css目录 */
define('MPF_C_APP_IMG_URL', '../public/img/'.MPF_C_THEME.'/'.strtolower(MPF_C_APPNAME).'/');    /* 模块img目录 */
define('MPF_C_APP_FLASH_URL', '../public/flash/'.strtolower(MPF_C_APPNAME).'/');    /* 模块flash目录 */
define('MPF_C_APP_CLASS_PATH', $oCf->getPath('./class/'));    /* 模块类文件目录 */
define('MPF_C_APP_CLASS_PATH_ITFSERVER', $oCf->getPath('./class/interfaceServer/'));    /* 模块服务端接口类目录 */
define('MPF_C_APP_CLASS_PATH_ET', $oCf->getPath('../publicClass/entity/'));    /* 模块实体类文件目录 */
define('MPF_C_APP_CLASS_PATH_FDT', $oCf->getPath('../publicClass/fdt/'));    /* 模块字段定义类文件目录 */
define('MPF_C_APP_CLASS_PATH_ITFCLIENT', $oCf->getPath('../publicClass/interfaceClient/'));    /*  模块客户端接口类目录 */
define('MPF_C_APP_WRPRIVATE_PATH', $oCf->getPath('../wrDir/private/'.strtolower(MPF_C_APPNAME).'/'));    /* 模块私有写目录路径 */
define('MPF_C_APP_WRPUBLIC_PATH', $oCf->getPath('../wrDir/public/'.strtolower(MPF_C_APPNAME).'/'));    /* 模块公共写目录路径 */
define('MPF_C_APP_WRPUBLIC_URL', '../wrDir/public/'.strtolower(MPF_C_APPNAME).'/');    /* 模块公共写目录web路径 */
define('MPF_C_APP_DB_CONNECTION_TYPE', 'mysqli');    /* db连接类型（mysql/mysqli） */
define('MPF_C_APP_TBPREFIX', 'mpf_');    /* 数据库表名前缀 */
//define('MPF_C_TIMEZONE', 'PRC');    /* 时区 */
//date_default_timezone_set(MPF_C_TIMEZONE);
/* 其他 */

/* 包含翻译文件 */
define('MPF_C_LANG', 'en');    /* 语言 */
require_once('../lang/'.MPF_C_LANG.'/cm.php');  /* 公共翻译 */
require_once('../lang/'.MPF_C_LANG.'/'.strtolower(MPF_C_APPNAME).'.php');  /* 模块翻译 */

/* 各个模块名定义 */
define('MPF_C_APPNAME_SITE', 'SITE');    /* SITE模块的模块名 */

/* 启用/禁用模块 */
define('MPF_SITE_ENABLE', true);    /* 标记是否启用SITE模块 */

/* 基本配置信息数组 */
$mpf_config = array (
    /* 各个模块的配置 */
    'apps' => array (
        MPF_C_APPNAME_SITE => array (   /* SITE模块 */
            'appName' => MPF_C_APPNAME_SITE,    /* 模块名 */
            'domain' => MPF_C_HOMEDOMAIN, /* 模块所在域名 */
            'dir' => 'site',    /* 模块所在目录 */
            'preurl' => MPF_C_PREURL    /* 模块url前缀（含模块目录名） */
        )
    )
);

?>
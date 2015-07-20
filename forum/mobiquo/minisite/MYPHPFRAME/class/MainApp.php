<?php
/**
 * 主程序框架类
 * 定义了程序运行的各种环境，是程序运行的入口
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MainApp {
    
    public static $oMainApp;    /* 主程序对象 */
    public static $oAppConfig;  /* 模块配置对象，在模块运行时初始化。 */
    public static $oCf;  /* 公共函数对象 */
    public static $oCt;  /* 类的类型类对象 */
    public static $oClk;  /* 类链接器，控制了各种类文件的包含 */
    public static $oSmt; /* smarty应用类对象 */
    public static $oV;  /* 数据校验对象 */
    public static $oDt; /* 日期时间对象 */
    
    public static $oSession;    /* session处理对象 */
    public static $oCookie;     /* cookie处理对象 */
    public static $oDb; /* 数据库对象 */
    public static $tpl;     /* smarty模板文件名（包含路径） */
    public static $delayRedirectTpl = 'public_delayRedirect.html';    /* 延迟跳转模板文件名 */
    
    public static $cmd;   /* 操作码 */
    public static $title;      /* 页面title */
    public static $pg;     /* 翻页的页码 */
    public static $ob;      /* 翻页时按哪个列排序 */
    public static $od;      /* 排序方式，'desc'表示降序，'asc'表示升序 */
    public static $theme;   /* 样式主题名 */
    
    public static $isAjax;  /* 是否是ajax请求 */
    public static $rewriteMethod;   /* rewrite方式 */
    
    public static $tplVars = array();   /* 模板变量数组 */

    /**
     * 构造函数
     */
    public function __construct() {
        self::$oMainApp = $this;
        global $oCf;
        self::$oCf = $oCf;
        self::$oCf->loadLang();
        self::$oCt = new OptClassType();
        self::$oClk = new ClassLink();
        self::$oClk->includeClass('Error');     /* 必须的 */
        self::$oSmt = self::$oClk->newObj('AppSmarty');
        self::$oSession = self::$oClk->newObj('AppSession');
        self::$oCookie = self::$oClk->newObj('AppCookie');
        if (defined('MPF_C_APP_DB_CONNECTION_TYPE')) {
            if (MPF_C_APP_DB_CONNECTION_TYPE == 'mysqli') {
                self::$oClk->includeClass('AppDb'); /* 预包含 */
                self::$oDb = self::$oClk->newObj('AppDbi');
            } elseif (MPF_C_APP_DB_CONNECTION_TYPE == 'mysql') {
                self::$oDb = self::$oClk->newObj('AppDb');
            } else {
                define('MPF_C_APP_DB_CONNECTION_TYPE', 'mysql');
                self::$oDb = self::$oClk->newObj('AppDb');
            }
        } else {
            define('MPF_C_APP_DB_CONNECTION_TYPE', 'mysql');
            self::$oDb = self::$oClk->newObj('AppDb');
        }

        self::$oCf->oV = self::$oClk->newObj('Verify');
        self::$oCf->oDt = self::$oClk->newObj('DT');
        self::$oV = self::$oCf->oV;
        self::$oDt = self::$oCf->oDt;
        self::$title = "Welcome!";
        
        //self::setTpl(self::$oCf->getPath(self::$oSmt->template_dir) . "public_error.html");   /* 默认输出到报错模板 */
        self::setTpl('public_error.html');   /* 默认输出到报错模板 */
        self::$tplVars = &self::$oSmt->_tpl_vars;   /* attention!!! */
        
        /* 预包含 */
        self::$oClk->includeClass('AppClass');
        self::$oClk->includeClass('AppDo');
        self::$oClk->includeClass('Et');
        self::$oClk->includeClass('Fdt');
    }
    
    /**
     * 选择rewrite方式
     *
     * @param  String  $v  为'php'表示用php程序仿rewrite，为'normal'表示通过http服务器配置rewrite，为'none'表示不启用rewrite；
     */
    protected function selectRewrite($v = 'none') {
        if ($v == 'php') {
            self::$oCf->makeRewrite();
            self::$rewriteMethod = 'php';
        } elseif ($v == 'normal') {
            self::$rewriteMethod = 'normal';
        } else {
            self::$rewriteMethod = 'none';
        }
        self::$oCf->doStripslashesRequest();
        self::$cmd = $_GET['cmd'];
        self::$pg = ((int) $_GET['pg']) ? ((int) $_GET['pg']) : 1;
        self::$ob = $_GET['ob'];
        self::$od = $_GET['od'];
        self::$theme = MPF_C_THEME;
        self::$isAjax = $_GET['isAjax'];
    }
    
    /**
     * 输出模板前的操作，一般用于输出公共smarty模板变量
     *
     * @param  Mixed  $oMain
     * @param  Array  $opt
     */
    public static function cmEnd($oMain = NULL, $opt = array()) {
        self::assign('oCf', self::$oCf);
        self::assign('errInfo', Error::$errInfo);       /* 错误信息多维数组 */
        self::assign('errInfoList', Error::$errInfoList);       /* 错误信息一维数组 */
        if (Error::hasErr()) {
            $hasErr = true;
        } else {
            $hasErr = false;
        }
        self::assign('hasErr', $hasErr);
        self::assign('cmd', self::$cmd);
        self::assign('pg', self::$pg);
        self::assign('ob', self::$ob);
        self::assign('od', self::$od);
        self::assign('title', self::$title);
        self::assign('theme', self::$theme);
        self::assign('rewriteMethod', self::$rewriteMethod);
        $tempTime = time();
        self::assign('ajaxSec', $tempTime);
        self::assign('oAppConfig', self::$oAppConfig);
        self::assign('mpfLangCm', self::$oCf->mpfLangCm);   /* 公共翻译 */
        self::assign('mpfLang', self::$oCf->mpfLang);   /* 模块翻译 */
    }
    
    /**
     * 输出模板
     */
    final public static function display() {
        self::$oSmt->display(self::$tpl);
    }
    
    /**
     * 设置smarty模板变量
     *
     * @param  String  $name  模板变量名
     * @param  Mixed  $value  要传递到模板的变量
     */
    final public static function assign($name, &$value) {
        /* self::$oSmt->assign_by_ref($name, $value); */
        self::$oSmt->assign($name, $value);
    }
    
    /**
     * 设置smarty模板self::$tpl
     *
     * @param  String  模板文件名
     */
    final public static function setTpl($tpl) {
        self::$tpl = $tpl;
    }
    
    /**
     * 直接输出错误到错误信息模板
     */
    final protected function outputErr() {
        //self::setTpl(self::$oCf->getPath(self::$oSmt->template_dir) . "public_error.html");
        self::setTpl('public_error.html');
    }
    
    /**
     * 设置seo
     *
     * @param  Array  $v  要设置seo的数组
     */
    protected function setSeo($v) {
        if ($v['title']) {
            self::$title = $v['title'];
        }
    }
    
    /**
     * 设置session配置
     */
    protected function cfgSession() {
        $appCfg = self::$oAppConfig->getAppCfg();
        session_name($appCfg['session']['sessName']);
        /* 与cookie一致 */
        session_set_cookie_params($appCfg['session']['sessCookieLeftTime'], $appCfg['session']['sessCookiePath'], $appCfg['session']['sessCookieDomain']);
        $sname = session_name();
        if ($_COOKIE[$sname]) {
            session_id($_COOKIE[$sname]);
        } elseif ($_POST[$sname]) {
            session_id($_POST[$sname]);
        }
    }
    
    /**
     * 初始化session
     */
    protected function initSession() {
        $this->cfgSession();
        self::$oSession->sessionStart();
    }
    
    /**
     * 注册模块中的各种类 
     */
    protected function regClass() {
        $type = self::$oCt->getApp();
        self::$oClk->reg($type, 'AppConfig', './AppConfig.php');  /* 模块配置类 */
        $this->regPublicClass();
        $this->regAppClass();
    }
    
    /**
     * 注册公共类，各个模块中都要用到的类（主要是些实体类及模块字段定义类和接口调用客户端类）
     */
    protected function regPublicClass() {
    }
    
    /**
     * 注册应用类，模块中独有的类
     */
    protected function regAppClass() {
    }
  
}

?>
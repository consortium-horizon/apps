<?php

/**
 * 公共函数类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class CF {  /* CF是Common Function的缩写 */
    
    public $oV; /* 数据校验对象 */
    public $oDt; /* 日期时间对象 */
    
    public $mpfLangCm;     /* 公共翻译 */
    public $mpfLang;           /* 模块翻译 */
    
    public $currProtocol;   /* http or https */

    /**
     * 构造函数
     */
    public function __construct() {
        $isSsl = false;
        if($_SERVER['HTTPS'] === 1){ //Apache
            $isSsl = true;
        }elseif($_SERVER['HTTPS'] === 'on'){ //IIS
            $isSsl = true;
        }elseif($_SERVER['SERVER_PORT'] == 443){ //other
            $isSsl = true;
        }
        $this->currProtocol = $isSsl ? 'https' : 'http';
    }
    
    /**
     * 得到当前页面的完整url
     */
    public function getCurrUrl() {
        $serverName = $_SERVER['SERVER_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        return $this->currProtocol.'://'.$serverName.$requestUri;
    }
    
    /**
     * 获取客户端ip地址
     *
     * @return  String  返回对应的ip地址
     */
    public function getIp() {
        if(getenv('HTTP_CLIENT_IP')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR')) {
            $onlineip = getenv('REMOTE_ADDR');
        } else {
            $onlineip = $_SERVER["REMOTE_ADDR"];
        }
        return $onlineip;
    }
    
    /**
     * 载入翻译文件
     */
    public function loadLang() {
        global $mpf_lang_cm;
        $var = 'mpf_lang_'.strtolower(MPF_C_APPNAME);
        global $$var;
        $this->mpfLangCm = $mpf_lang_cm;
        $this->mpfLang = $$var;
    }
    
    /**
     * 返回翻译文件中对应的翻译
     *
     * @param  String  $key  数组项的key（格式：小写模块名（对应公共翻译这里是cm）_若干英文单词（以_分隔））
     * @return  String  返回对应的翻译
     */
    public function _L($key) {
        if (isset($this->mpfLang[$key])) {
            return $this->mpfLang[$key];
        }
        if (isset($this->mpfLangCm[$key])) {
            return $this->mpfLangCm[$key];
        }
        return "____L_FIND_NONE_KEY____".$key;
    }
    
    /**
     * 返回翻译的字符串
     */
    public function _($str) {
        return $str;
    }
    
    /**
     * 返回最后一个字符为斜杠（/）的绝对路径，如果路径无效则返回false
     *
     * @param  String  $path  路径字符串
     */
    public function getPath($path) {
        if (mb_strrpos($path, '/') === false || mb_strlen($path) == 0) {    /* 路径无效 */
            return false;
        } elseif (strcmp(mb_substr($path, mb_strlen($path) - 1, 1), '/') == 0) {  /* $path的最后一个字符是'/' */
            return $path;
        } else {
            return $path . '/';
        }
    }
    
    /**
     * 转换字符串
     *
     * @param  String  $str  待转换的字符串
     * @param  String  $type  转换的类型
     * @return  String  返回转换后的字符串
     */
    public function cv($str, $type = 'ENT_QUOTES') {
        switch ($type) {
            case 'ENT_QUOTES':    /* 转换特殊html字符，包括单/双引号 */
                return htmlspecialchars($str, ENT_QUOTES);
                break;
            default:
                Error::alert('cv', __METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid type!', ERR_TOP);   /* 无效的转换类型 */
                break;
        }
    }
    
    /**
     * 根据数组参数返回被逗号分隔并且每个数组元素被单引号括起来的字符串,用于sql语句中的in条件。
     * 这个方法将被废除
     *
     * @param  $arr  数组参数
     * @return  Mixed  如果没有错误则返回字符串，否则返回false
     */
    public function getSqlIn($arr) {
        $sqlIn = '';
        if (is_array($arr)) {
            if (count($arr) > 0) {
                $flag = true;   /* 第一个数组元素标记 */
                foreach ($arr as $value) {
                    if ($flag) {
                        $sqlIn .= "'".addslashes($value)."'";   /* 必须使用addslashes转义并且用单引号引起来 */
                        $flag = false;
                    } else {
                        $sqlIn .= ", '".addslashes($value)."'";
                    }
                }
                return $sqlIn;
            } else {
                return false;   /* 数组中没有数组项 */
            }
        } else {
            return false;  /* 参数错误，参数必须是数组 */
        }
    }
    
    /**
     * 仿rewrite
     */
    public function makeRewrite() {
        $pos = strpos($_SERVER['REQUEST_URI'], '.php');
        if ($pos !== false) {
            $queryString = substr($_SERVER['REQUEST_URI'], ($pos+4));
            $arr = explode('/', $queryString);
            if (count($arr) == 1) { /* 没有找到'/' */
                return NULL;
            } else {
                array_shift($arr);
            }
            $_GET = array();
            $_REQUEST = array();
            foreach ($arr as $key => $value ) {
                if ($key%2 != 1) {
                    if($value !='' ) {
                        $_GET[$value] = $arr[$key+1];
                        $_REQUEST[$value] = $arr[$key+1];
                    }
                }
            }
            foreach ($_POST as $key => $value) {
                $_REQUEST[$key] = $value;
            }
        } else {
            Error::alert('makeRewrite', __METHOD__ . ',line:' . __LINE__ . '.' . 'Can not make rewrite!', ERR_TOP);
        }
    }
    
    /**
     * 将$_POST、$_GET、$_COOKIE变量作stripslashes()转化
     *
     * 在执行sql语句时需要根据情况作addcslashes()转化
     */
    public function doStripslashesRequest() {
        if (get_magic_quotes_gpc()) {
            $this->stripslashesRequest($_GET);
            $this->stripslashesRequest($_POST);
            $this->stripslashesRequest($_COOKIE);
            $this->stripslashesRequest($_REQUEST);
        }
    }
    
    /**
     * 将$var变量作stripslashes()转化
     *
     * 这个函数被$this->doStripslashesRequest()调用
     */
    private function stripslashesRequest(&$var) {
        foreach($var as $key => $value) {
            if (is_array($value)) { /* $_REQUEST */
                $this->stripslashesRequest($var[$key]); /* 递归调用 */
            } else {
                $var[$key] = stripslashes($var[$key]);
            }
        }
    }
    
    /**
     * 返回对应的url
     *
     * @param  String  $appName  模块名
     * @param  String  $mainName  主程序名（例如MainHomePage.php）
     * @param  String  $cmd  命令名
     * @param  Array  $getParames  get参数数组（例如：array('pg' => 123)）
     * @param  Boolean  $getAbsoluteUrl  标记是否返回完整地址
     */
    public function makeUrl($appName, $mainName, $cmd, $getParams = array(), $getAbsoluteUrl = false) {
        if (!$appName && !$mainName && !$cmd && !$getParams && !$getAbsoluteUrl) return ''; //only used for json retrun,means do not need redirection
        $sameApp = false;   /* 标记是否是在同一个模块 */
        if (!$appName) $appName = MPF_C_APPNAME;
        if ($appName == MPF_C_APPNAME) {
            $sameApp = true;
        }
        $appUrl = MainApp::$oAppConfig->getAppUrl($appName);
        $rewriteMethod = MainApp::$rewriteMethod;
        if ($rewriteMethod == 'php') {
            $str = '/';
            if (is_array($getParams)) {
                foreach ($getParams as $k => $v) {
                    $str .= "$k/$v/";
                }
            }
            if ($cmd) {
                $cmdStr = "cmd/$cmd";
            } else {
                $cmdStr = "";
            }
            if ($sameApp && !$getAbsoluteUrl) {
                if ($mainName) {
                    //return "$mainName/$cmdStr".$str;  /* 这样写有问题 */
                    return $appUrl."/$mainName/$cmdStr".$str;
                } else {
                    //return "$cmdStr".$str;    /* 这样写有问题 */
                    return $appUrl."/$cmdStr".$str;
                }
            } else {
                if ($mainName) {
                    return $appUrl."/$mainName/$cmdStr".$str;
                } else {
                    return $appUrl."/$cmdStr".$str;
                }
            }
        } elseif ($rewriteMethod == 'normal') {
            
        } elseif ($rewriteMethod == 'none') {
            $str = '';
            if (is_array($getParams)) {
                foreach ($getParams as $k => $v) {
                    $str .= "&$k=$v";
                }
            }
            if ($cmd) {
                $cmdStr = "cmd=$cmd";
            } else {
                $cmdStr = "";
            }
            if (!$cmdStr && !$str) {
                $newStr = '';
            } else {
                $newStr = "?$cmdStr$str";
            }
            if ($sameApp && !$getAbsoluteUrl) {
                if ($mainName) {
                    return "$mainName$newStr";
                } else {
                    return "$newStr";
                }
            } else {
                if ($mainName) {
                    return $appUrl."/$mainName$newStr";
                } else {
                    return $appUrl."/$newStr";
                }
            }
        }
    }
    
    /**
     * 页面返回
     *
     * @param  $type  返回类型，'delayRedirect'表示延迟跳转，'ajax'表示ajax返回
     * @param  String  $appName  模块名
     * @param  String  $mainAppName  主程序名（例如MainHomePage.php）
     * @param  String  $cmd  命令名
     * @param  Array  $getParames  get参数数组（例如：array('pg' => 123)）
     * @param  Array  $returnData  返回的数据（对于delayRedirect返回则是返回对应的提示信息和延迟的秒数，对于ajax返回则是返回对应的数据格式的数据。）
     * @param  Mixed  $returnFormat  返回的数据格式（只对ajax返回有效），'json'表示返回json数据（默认）
     * @param  Boolean  $getAbsoluteUrl  标记是否返回完整地址
     * @param  Boolean  $needReturnUrl  标记是否需要返回地址url（即标记是否需要跳转）
     */
    public function pageReturn($type, $appName, $mainName, $cmd, $getParams = array(), $returnData = array(), $returnFormat = 'json', $getAbsoluteUrl = false, $needReturnUrl = true) {
        if (MainApp::$isAjax) {
            $type = 'ajax';
        } else {
            $type = ($type ? $type : 'delayRedirect');
        }
        //if ($mainName) {    /* 如果没有$mainName则表示无须跳转 */
        //    $url = $this->makeUrl($appName, $mainName, $cmd, $getParams, $getAbsoluteUrl);
        //}
        $url = $this->makeUrl($appName, $mainName, $cmd, $getParams, $getAbsoluteUrl);
        $url = ($url ? $url : $returnData['redirectUrl']);
        if (!$needReturnUrl) {  /* 无需跳转 */
            $url = '';
        }
        if ($type == 'ajax') {
            $ret = array(
                'status' => $returnData['status'],
                'info' => $returnData['info'],
                'redirectUrl' => $url,
                'redirectTarget' => ($returnData['redirectTarget'] ? $returnData['redirectTarget'] : '_self'),
                'delaySec' => ($returnData['delaySec'] ? $returnData['delaySec'] : 5)
            );
            if ($returnData['returnData']) {
                $ret['returnData'] = $returnData['returnData'];
            }
            header('Content-type: application/json');
            echo json_encode($ret);
            die();
        } elseif ($type == 'delayRedirect') {
            $delayRedirectData = array (
                'status' => $returnData['status'],
                'info' => $returnData['info'],
                'redirectUrl' => $url,
                'redirectTarget' => ($returnData['redirectTarget'] ? $returnData['redirectTarget'] : '_self'),
                'delaySec' => ($returnData['delaySec'] ? $returnData['delaySec'] : 5)
            );
            if ($returnData['returnData']) {
                $delayRedirectData['returnData'] = $returnData['returnData'];
            }
            MainApp::cmEnd();
            MainApp::setTpl(MainApp::$delayRedirectTpl);    //!!! this templet is very very important.
            MainApp::assign('dfvDelayRedirectData', $delayRedirectData);
            MainApp::display();
            die();
        }
    }
    
    /**
     * 跳转到指定的url
     *
     * @param  String  $appName  模块名
     * @param  String  $mainAppName  主程序名（例如MainHomePage.php）
     * @param  String  $cmd  命令名
     * @param  Array  $getParames  get参数数组（例如：array('pg' => 123)）
     * @param  Boolean  $getAbsoluteUrl  标记是否返回完整地址
     */
    public function redirectTo($appName, $mainName, $cmd, $getParams = NULL, $getAbsoluteUrl = false) {
        $url = $this->makeUrl($appName, $mainName, $cmd, $getParams, $getAbsoluteUrl);
        $redirect = "Location:$url";
        /*
        $sname = session_name();
        $sid = session_id();
        if (ini_get('session.use_trans_sid')) {
            if (isset($_COOKIE[$sname])) {
                header($redirect);
            } else {
                if (self::$rewriteMethod == 'none') {
                    if (strpos($redirect, $sname."=".$sid) !== false) {
                        header($redirect);
                    } else {
                        if(strpos($redirect, "?") > 0) {
                            $separator = "&";
                        } else {
                            $separator = "?";
                        }
                        $fixed = $redirect . $separator . $sname . "=" . $sid;
                        header( $fixed );
                    }
                } else {
                    $fixed = self::$oCf->getPath($redirect) . $sname . "/" . $sid;
                    header( $fixed );
                }
            }
        } else {
            header($redirect);
        }
        */
        header($redirect);
        die();
    }
    
    /**
     * 显示对应的url
     *
     * @param  String  $appName  模块名
     * @param  String  $mainAppName  主程序名（例如MainHomePage.php）
     * @param  String  $cmd  命令名
     * @param  Array  $getParames  get参数数组（例如：array('pg' => 123)）
     * @param  Boolean  $getAbsoluteUrl  标记是否返回完整地址
     */
    public function echoUrl($appName, $mainName, $cmd, $getParams = NULL, $getAbsoluteUrl = false) {
        echo $this->makeUrl($appName, $mainName, $cmd, $getParams, $getAbsoluteUrl);
    }
    
    /**
     * 返回主站首页url
     */
    public function getMainHomeUrl() {
        return $this->currProtocol.'://'.MPF_C_MAIN_HOMEDOMAIN;
    }
    
    /**
     * 返回模块首页url
     */
    public function getHomeUrl() {
        return $this->currProtocol.'://'.MPF_C_HOMEDOMAIN;
    }
    
    /**
     * 返回当前php程序占用的内存量（以兆为单位）
     *
     * @return  Float
     */
    public function getMemUsage() {
        return $this->oDt->getDateTime().','.round((memory_get_usage()/1024/1024), 2).'M';
    }
    
    /**
     * 返回数组$arr的开头$num个的数组项，$arr的长度减$num并将所有其它单元向前移动$num位
     *
     * @param  Array  $arr  数组
     * @param  Integer  $num  要返回的最大数组项的数目
     * @return  Mixed  返回对应的数组项组成的数组，如果$arr为空（或者不是数组），则返回false
     */
    public function shiftArray(&$arr, $num) {
        if (is_array($arr) && $arr) {
            $ret = array();
            $i = 0;
            while ($v = array_shift($arr)) {
                $ret[$i] = $v;
                $i ++;
                if ($i == $num) {
                    break;
                }
            }
            return $ret;
        } else {
            return false;
        }
    }
    
    /**
     * 获取db结果集中的$num个的记录数组项
     *
     * @param  Resource  $result  db结果集
     * @param  Integer  $num  要返回的最大数组项的数组
     * @return  Array  返回对应的数组项组成的数组，如果$result没有更多行，则返回空数组
     */
    public function shiftDbResult(&$result, $num) {
        $ret = array();
        $i = 0;
        while ($record = mysql_fetch_array($result)) {
            $ret[] = $record;
            $i ++;
            if ($i == $num) {
                break;
            }
        }
        return $ret;
    }
    
    /**
     * 返回guid
     */
    public function getGuid() {
        $randStr = str_replace('.', '', uniqid(mt_rand(100000000, 999999999), true)).mt_rand(100000000, 999999999).str_replace('.', '', uniqid(mt_rand(100000000, 999999999), true)).mt_rand(100000000, 999999999);
        $v = str_split($randStr, 9);
        $ret = '';
        foreach ($v as $vv) {
            $vv = base_convert($vv, 10, 32);
            $ret .= $vv;
        }
        if (strlen($ret) > 64) {
            return $this->getGuid();
        } else {
            return $ret;
        }
    }
   
}

?>
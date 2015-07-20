<?php
/**
 * 模块配置基类 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class AppConfigBase {
    
    protected $appCfg = array();  /* 模块配置多维数组 */
    protected $interfaceSetting = array(); /* 模块间接口配置 */

    /**
     * 构造函数
     */
    public function __construct() {
        $this->appCfg['session'] = array (  /* session配置，只有底层程序可以直接访问这个配置，其他程序一律不得直接访问。 */
            'sessName' => MPF_C_SESSION_NAME, /* session name */
            'sessCookieLeftTime'=> MPF_C_COOKIE_LEFT_TIME, /* session cookie有效期 */
            'sessCookiePath' => MPF_C_COOKIE_PATH,  /* cookie路径 */
            'sessCookieDomain' => MPF_C_COOKIE_DOMAIN, /* cookie域 */
        );
    }
    
    /**
     * 返回$this->appCfg
     */
    final public function getAppCfg() {
        return $this->appCfg;
    }
    
    /**
     * 根据模块名返回对应的url
     */
    public function getAppUrl($appName) {
        if (isset($this->interfaceSetting['apps'][$appName]) && ($app = $this->interfaceSetting['apps'][$appName])) {
            /*
            if ($app['dir']) {
                return MainApp::$oCf->currProtocol.'://'.$app['domain'].'/'.$app['dir'];
            } else {
                return MainApp::$oCf->currProtocol.'://'.$app['domain'];
            }
            */
            if (!$app['preurl']) {
                return MainApp::$oCf->currProtocol.'://'.$app['domain'];
            } else {
                return MainApp::$oCf->currProtocol.'://'.$app['domain'].$app['preurl'];
            }
        } else {
            Error::alert('appConfig', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find app info for $appName!", ERR_TOP);
        }
    }
   
}

?>

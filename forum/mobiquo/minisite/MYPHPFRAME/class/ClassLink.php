<?php
/**
 * 类链接器类
 * 控制了各种类文件的包含
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class ClassLink {
    
    public $classes = array();  /* 各种类别的已被注册的类的类名、类文件路径信息组成的多维数组 */
    public $infos = array();  /* 以类名为key的类信息的一维数组，用于控制避免重复注册类 */

    /**
     * 构造函数
     */
    public function __construct() {
        $reserve = MainApp::$oCt->getReserve();
        $base = MainApp::$oCt->getBase();
        $application = MainApp::$oCt->getApp();
        $this->classes[$reserve] = array();  /* 保留类数组 */
        $this->classes[$base] = array();  /* 基础类数组 */
        $this->classes[$application] = array();  /* 应用类数组 */
        /* 注册保留类，保留类不需要被类链接器包含就可直接使用，这里只注册它们的类名。 */
        $this->reg($reserve, 'CF');
        $this->reg($reserve, 'MainApp');
        $this->reg($reserve, 'Select');
        $this->reg($reserve, 'OptClassType');
        $this->reg($reserve, 'ClassLink');
        $this->reg($reserve, 'Smarty');
        /* 注册基础类 */
        $path = MainApp::$oCf->getPath(MPFFRAME_CLASS_PATH);
        $this->reg($base, 'AppSmarty', $path . 'AppSmarty.php');
        $this->reg($base, 'Error', $path . 'Error.php');
        $this->reg($base, 'ErrorInfo', $path . 'Error.php');
        $this->reg($base, 'AppSession', $path . 'AppSession.php');
        $this->reg($base, 'AppCookie', $path . 'AppCookie.php');
        $this->reg($base, 'AppDb', $path . 'AppDb.php');
        $this->reg($base, 'AppDbi', $path . 'AppDbi.php');
        $this->reg($base, 'Verify', $path . 'Verify.php');
        $this->reg($base, 'DT', $path . 'DT.php');
        $this->reg($base, 'BasePage', $path . 'BasePage.php');
        $this->reg($base, 'AppPage', $path . 'BasePage.php');
        $this->reg($base, 'AppClass', $path . 'AppClass.php');
        $this->reg($base, 'AppDo', $path . 'AppDo.php');
        $this->reg($base, 'Et', $path . 'Et.php');
        $this->reg($base, 'AppConfigBase', $path . 'AppConfigBase.php');
        $this->reg($base, 'Fdt', $path . 'Fdt.php');
        /* 注册应用类，由具体应用自己注册所要用到的各种应用类 */
        /* ... */
    }
    
    /**
     * 判断是否注册了对应类
     *
     * @param  String  $className
     * @return  Boolean
     */
    public function hasReg($className) {
        return (isset($this->infos[$className]) ? true : false);
    }
    
    /**
     * 注册一个类
     *
     * @param  String  $type  类的类型
     * @param  String  $name  类名
     * @param  String  $path  类文件的路径（包括文件名）
     */
    public function reg($type, $name, $path = NULL) {
        if ($this->hasReg($name)) {
            Error::alert('reg', __METHOD__ . ',line:' . __LINE__ . '.' . 'Can not repeat reg class:'.$name.'!', ERR_TOP);
        }
        switch ($type) {
            case MainApp::$oCt->getReserve():
                $this->classes[$type][$name] = array();
                $this->classes[$type][$name]['name'] = $name;   /* 类名 */
                $this->classes[$type][$name]['path'] = '';  /* 类文件的路径。保留类无需设置类文件路径就可直接使用。 */
                $this->infos[$name] = &$this->classes[$type][$name];
                break;
            case MainApp::$oCt->getBase():
                $this->classes[$type][$name] = array();
                $this->classes[$type][$name]['name'] = $name;
                $this->classes[$type][$name]['path'] = $path;
                $this->infos[$name] = &$this->classes[$type][$name];
                break;
            case MainApp::$oCt->getApp():
                $this->classes[$type][$name] = array();
                $this->classes[$type][$name]['name'] = $name;
                $this->classes[$type][$name]['path'] = $path;
                $this->infos[$name] = &$this->classes[$type][$name];
                break;
            default:
                Error::alert('reg', __METHOD__ . ',line:' . __LINE__ . '.' . 'Invalid class type!', ERR_TOP);
                break;
        }
    }
    
    /**
     * 包含一个类的类文件
     * 注意：在开发时禁止直接使用这个方法，这个方法仅用于框架和最底层的开发需要。
     *
     * @param  String  $className  类名
     */
    public function includeClass($className) {
        foreach ($this->classes as $type => $classes) {
            foreach ($classes as $class) {
                if ($class['name'] == $className && $type != MainApp::$oCt->getReserve()) {
                    require_once($class['path']);
                }
            }
        }
    }
    
    /**
     * 声明一个对象
     *
     * 如果在$this->classes中找不到对应类名的类则退出程序并报错
     * @param  String  $className  类名
     * @param  Array  $p  其他参数组成的数组
     * @return Object  返回相应的对象
     */
    public function newObj($className, $p = NULL) {
        foreach ($this->classes as $type => $classes) {
            foreach ($classes as $class) {
                if ($class['name'] == $className && $type != MainApp::$oCt->getReserve()) {
                    require_once($class['path']);
                    $obj = new $className();
                    return $obj;
                }
            }
        }
        Error::alert('reg', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find class $className!", ERR_TOP);
    }
    
    /**
     * 包含所有类文件
     */
    public function requireAllClass() {
        foreach ($this->classes as $type => $classes) {
            foreach ($classes as $class) {
                if ($type != MainApp::$oCt->getReserve()) {
                    require_once($class['path']);
                }
            }
        }
    }
  
}

?>
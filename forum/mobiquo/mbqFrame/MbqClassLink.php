<?php

defined('MBQ_IN_IT') or exit;

/**
 * classe linker
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqClassLink {
    
    private $classes = array();  /* registered classes name/path info array */
    private $infos = array();  /* classes name array,used to prevent repeat regist class */

    public function __construct() {
    }
    
    /**
     * to judge the class has been registed
     *
     * @param  String  $name  class name
     * @return  Boolean
     */
    public function hasReg($name) {
        return isset($this->classes[$name]) ? true : false;
    }
    
    /**
     * regist a class
     *
     * @param  String  $name  class name
     * @param  String  $path  class path
     */
    public function reg($name, $path) {
        if (isset($this->infos[$name])) {
            MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . 'Can not repeat regist class:'.$name.'!');
        }
        $this->classes[$name] = array();
        $this->classes[$name]['name'] = $name;
        $this->classes[$name]['path'] = $path;
        $this->infos[$name] = &$this->classes[$name];
    }
    
    /**
     * include a class
     * this method only used in frame degree and the lowest level development,please do not use this method in other place,you can use newObj() method instead.
     *
     * @param  String  $className  class name
     */
    public function includeClass($className) {
        foreach ($this->classes as $class) {
            if ($class['name'] == $className) {
                require_once($class['path']);
                return true;
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find class $className!");
    }
    
    /**
     * new an object
     *
     * @param  String  $className  class name
     * @param  Array  $p  params for create the object.
     * @return Object
     */
    public function newObj($className, $p = NULL) {
        foreach ($this->classes as $class) {
            if ($class['name'] == $className) {
                require_once($class['path']);
                if ($p) {
                    $obj = new $className($p);
                } else {
                    $obj = new $className();
                }
                return $obj;
            }
        }
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . "Can not find class $className!");
    }
  
}

?>
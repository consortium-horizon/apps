<?php
/**
 * 选择类
 * 用于各种条件下的选择条目
 * 其下的各个扩展类都以Opt开头
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class Select {
    
     /** 选择条目一维数组，例如：
      * $items['id1'] = 'name0';
      * $items['id5'] = 'name1';
      * $items['id11'] = 'name2';
      */
    public $items = array();

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 通过条目id得到条目name
     *
     * @param  Integer  $id  条目id
     * @return  Mixed  读取成功则返回name值，否则返回false
     */
    public function getNameById($id) {
        foreach ($this->items as $key => $value) {
            if ($key == $id) {
                return $value;  /* 读取成功 */
            }
        }
        return false;   /* 找不到对应的id */
    }
    
    /**
     * 通过条目name得到条目id，找不到则返回false
     *
     * @param  String  $name  条目name
     * @return  Mixed  读取成功则返回id值，否则返回false
     */
    public function getIdByName($name) {
        foreach ($this->items as $key => $value) {
            if ($value == $name) {
                return $key;    /* 读取成功 */
            }
        }
        return false;   /* 找不到对应的name */
    }
   
}




/**
 * 类的类型类
 */
Class OptClassType Extends Select {

    /**
     * 构造函数
     */
    public function __construct() {
        /* 保留类不需要被类链接器包含就可直接使用。目前保留类有：公共函数类CF、主程序类MainApp、
        选择类Select、类的类型类OptClassType、类链接器类ClassLink、Smarty类。 */
        $this->items['reserve'] = 'reserve';  /* 保留类 */
        $this->items['base'] = 'base';  /* 保证系统正常运行的基础库类 */
        $this->items['application'] = 'application'; /* 跟当前应用相关的类 */
    }
    
    /**
     * 返回保留类型的值
     */
    public function getReserve() {  return 'reserve';   }
    /**
     * 返回基础类型的值
     */
    public function getBase() {  return 'base';   }
    /**
     * 返回应用类型的值
     */
    public function getApp() {  return 'application';   }
  
}

?>

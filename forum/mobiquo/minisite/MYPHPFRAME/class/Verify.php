<?php
/**
 * 数据校验类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class Verify {

    /**
     * 构造函数
     */
    public function __construct() {
    }
    
    /**
     * 校验整数
     *
     * @param  Mixed  $data  要校验的数据
     * @return  Boolean  如果是整数则返回true，否则返回false
     */
    public function isInt($data) {
        if (is_numeric($data)) {
            $data = $data + 0;      /* 将$data装换为数字 */
            if (is_int($data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * 校验浮点数
     *
     * @param  Mixed  $data  要校验的数据
     * @return  Boolean  如果是浮点数则返回true，否则返回false
     */
    public function isFloat($data) {
        if (is_numeric($data)) {
            $data = $data + 0;      /* 将$data装换为数字 */
            if (is_int($data) || is_float($data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * 校验正整数(大于0的整数)
     *
     * @param  Mixed  $data  要校验的数据
     * @return  Boolean  如果是正整数则返回true，否则返回false
     */
    public function isPositiveInt($data) {
        if ($this->isInt($data) && $data > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 校验数组中每项的值是否都是正整数
     *
     * @param  Array  $arr  数值数组
     * @return  Boolean  如果都是正整数则返回true，否则返回false
     */
    public function isPositiveInts($arr) {
        if (!is_array($arr)) {
            return false;   /* 参数不是数组 */
        }
        foreach ($arr as $value) {
            if (!$this->isPositiveInt($value)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 校验非负整数（大于等于0的整数）
     *
     * @param  Mixed  $data  要校验的数据
     * @return  Boolean  如果是非负整数则返回true，否则返回false
     */
    public function isUnsignedInt($data) {
        if ($this->isInt($data) && $data >= 0) {
            return true;
        } else {
            return false;
        }
    }
  
}
?>
<?php
/** 
 * mysqli基本数据库类，类对象初始化时没有连接到数据库 
 * 
 * @since  2013-8-25
 * @author Wu ZeTao <578014287@qq.com>
 */
Class AppDbi {
    
    /* 数据库名 */
    var $db_name;
    /* 数据库ip地址或域名 */
    var $db_server_address;
    /* 数据库用户名 */
    var $db_user;
    /* 数据库密码 */
    var $db_password;

    /* 数据库连接标识 */
    var $link_id;
    /* $result存储mysql_query（）函数进行select，show，explain 或 describe 操作返回的资源标识符，如果查询执行不正确则返回 false。对于其它类型的 sql 语句，mysql_query() 在执行成功时返回 true，出错时返回 false。*/
    var $result;
    /* 数据库查询获得的单条纪录的结果数组 */
    var $record;
    /* 数据库记录指针，指向当前记录 */
    var $row_number;

    /**
     * 构造函数
     */
    function __construct() {
    }
    
    /**
     * 准备连接数据库（初始化相关参数）
     *
     * @param  String  $dbName  数据库名
     * @param  String  $ip  ip地址
     * @param  String  $user  用户名
     * @param  String  $pass  密码
     */
    function prepareConnect($dbName, $ip, $user, $pass) {
        $this->db_name = $dbName;
        $this->db_server_address = $ip;
        $this->db_user = $user;
        $this->db_password = $pass;
    }

    /**
     * 打开一个到数据库服务器和数据库的连接，设置并返回数据库连接标识$this->link_id
     */
    function connect() {
        $this->link_id = mysqli_connect($this->db_server_address,$this->db_user,$this->db_password) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Connect db fail!', ERR_TOP);
        $sql = "set names 'utf8'";
        mysqli_query($this->link_id, $sql) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Can not set names!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
        mysqli_select_db($this->link_id, $this->db_name) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Select db fail!', ERR_TOP);
        return $this->link_id;
    }

    /**
     * 执行sql查询，设置并返回$this->result
     * 初始化$row_number，使$row_number指向第一个记录
     *
     * @param  String  $sql  sql语句
     * @param  String  $info  报错信息
     */
    function doQuery($sql, $info = '') {
        if (!$this->link_id) {
            $this->connect();
        }
        $this->result=mysqli_query($this->link_id, $sql) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . "Query fail!$info<br>" . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
        $this->row_number=0;
        return $this->result;
    }

    /**
     * 执行事务sql查询，设置并返回$this->result
     * 初始化$row_number，使$row_number指向第一个记录
     *
     * @param  String  $sql  sql语句
     * @param  String  $info  回滚报错信息
     */
    function doAffairQuery($sql, $info) {
        if (!$this->link_id) {
            $this->connect();
        }
        $this->result=mysqli_query($this->link_id, $sql) or $this->rollback($info);
        $this->row_number=0;
        return $this->result;
    }

    /**
     * 提取并返回select查询结果集$this->result中的一行记录$this->record，
     * 并使数据库记录指针$this->row_number移向下一个记录（如果还有记录的话）
     */
    function getOneRecord($result) {    
        /* 对mysql_fetch_array()函数，如果结果中的两个或以上的列具有相同字段名，最后一列将优先。要访问同名的其它列，必须用该列的数字索引或给该列起个别名。对有别名的列，不能再用原来的列名访问其内容。 */
        //if($this->record=mysqli_fetch_array($this->result))
        if($this->record=mysqli_fetch_array($result)) 
            $this->row_number+=1;
        return $this->record;
    }

    /**
     * 移动$this->result所关联的sql结果内部的行指针到$row_number指定的行号，并用$this->row_number存储这个行号，
     * 返回$this->row_number
     * mysql_data_seek()将指定的结果标识所关联的MySQL结果内部的行指针移动到指定的行号。
     * 接着调用mysql_fetch_row* ()将返回那一行。 row_number从0开始。
     * row_number的取值范围应该从0到mysql_num_rows-1。 
     * 注:mysql_data_seek()只能和mysql_query()结合起来使用，而不能用于mysql_unbuffered_query()。 
     */
    function doSeek($result, $row_number) {     //不可直接使用$this->result，防止多次查询result冲突问题。
        //if (mysqli_data_seek($this->result,$row_number))
        if (mysqli_data_seek($result,$row_number))
            return ($this->row_number = $row_number);
        else
            Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Seek fail!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
    }

    /**
     * 返回最近一次与连接标识$this->link_id关联的INSERT，UPDATE或DELETE 查询所影响的记录行数
     */
    function getAffectedRows() {
        $affected_rows=mysqli_affected_rows($this->link_id);
        return $affected_rows;
    }

    /**
     * 返回资源标识符$this->result所标识的结果集中行的数目。此命令仅对SELECT语句有效
     */
    function getNumRows($result) {     //不可直接使用$this->result，防止多次查询result冲突问题。
        //$num_rows=mysqli_num_rows($this->result);
        $num_rows=mysqli_num_rows($result);
        return $num_rows;
    }

    /**
     * 取得结果集中字段的数目
     */
    function getNumFields($result) {   //不可直接使用$this->result，防止多次查询result冲突问题。
        //$num_fields=mysqli_num_fields($this->result);
        $num_fields=mysqli_num_fields($result);
        return $num_fields;
    }
      
    /**
     * 返回结果集中指向当前记录的数值索引号
     */
    function getRowNumber() {
        return $this->row_number;
    }

    /**
     * 返回给定的数据库连接标识$this->link_id中上一步INSERT查询中产生的AUTO_INCREMENT的ID号
     */
    function getLastInsertId() {
        $result = $this->doQuery('select last_insert_id() as last_insert_id');
        $row = $this->getOneRecord($result);
        $last_insert_id=$row['last_insert_id'];
        return $last_insert_id;
    }

    /**
     * 释放所有与资源标识符$this->result所关联的内存
     */
    function freeResult($result) {     //不可直接使用$this->result，防止多次查询result冲突问题。
        //mysqli_free_result($this->result) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Free result fail!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
        //Note: You should always free your result with mysqli_free_result(), when your result object is not needed anymore. 
        mysqli_free_result($result) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Free result fail!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
    }

    /**
     * 开始一个事务
     */
    function beginAffair() {
        if (!$this->link_id) {
            $this->connect();
        }
        $sql = 'begin';
        mysqli_query($this->link_id, $sql) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Begin affair fail!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
    }

    /**
     * 回滚并报错，$info为报错信息
     */
    function rollback($info = '') {
        $sql = 'rollback';
        mysqli_query($this->link_id, $sql) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . "Rollback fail!$info<br>" . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
    }

    /**
     * 提交事务
     */
    function commitAffair() {
        $sql = 'commit';
        mysqli_query($this->link_id, $sql) or Error::alert('db', __METHOD__ . ',line:' . __LINE__ . '.' . 'Commit affair fail!<br>' . mysqli_errno($this->link_id) . ": " . mysqli_error($this->link_id), ERR_TOP);
    }
    
    /**
     * 返回转义过的用单引号括取来的值，用于db操作
     *
     * @param  String  $value
     * @return  String
     */
    function quote($value) {
        if (!$this->link_id) {
            $this->connect();
        }
        return '\'' . mysqli_real_escape_string($this->link_id, $value) . '\'';
    }
    
    /**
     * 根据数组参数返回被逗号分隔并且每个数组元素被单引号括起来的字符串,用于sql语句中的in条件。
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
                        $sqlIn .= $this->quote($value);   /* 必须使用addslashes转义并且用单引号引起来 */
                        $flag = false;
                    } else {
                        $sqlIn .= ", ".$this->quote($value);
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
    

}

?>
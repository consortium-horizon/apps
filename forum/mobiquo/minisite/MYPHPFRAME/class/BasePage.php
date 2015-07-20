<?php

/** 
 * 翻页类 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class BasePage {

    /* 总记录数 */
    public $total_rows_num;
    /* 每页记录数 */
    public $records_num_per_page;
    /* 总页数 */
    public $total_pages_num;
    /* 当前的页面号 */
    public $current_page_num;
    /* 当前页面第一个记录在结果集中的行号 */
    public $current_page_first_record_id;
    /* 当前页面最后一个记录在结果集中的行号 */
    public $current_page_last_record_id;
    /* 前一个页面的页面号 */
    public $pre_page_num;
    /* 后一个页面的页面号 */
    public $next_page_num;
    /* 首页的页面号 */
    public $first_page_num;
    /* 最后一页的页面号 */
    public $last_page_num;
    /* 当前页的记录数组 */
    public $rows=array();
    /* 数据库类对象 */
    public $oDb;
    /* 数据库结果 */
    public $result;

    /* 构造函数。 */
    public function __construct() {
    }
    
    /** 
     * 初始化
     */
    public function init($oDb, $current_page_num=1, $records_num_per_page=15) {
        $current_page_num = (int) $current_page_num;
        $records_num_per_page = (int) $records_num_per_page;
        $current_page_num = ($current_page_num < 1) ? 1 : $current_page_num;
        $records_num_per_page = ($records_num_per_page < 1) ? 15 : $records_num_per_page;
        $this->oDb=$oDb;
        $this->result = $oDb->result;
        $this->total_rows_num=$this->oDb->getNumRows($this->result);
        $this->records_num_per_page=$records_num_per_page;
        $this->total_pages_num=ceil($this->total_rows_num/$this->records_num_per_page);
        $this->current_page_num=
            ($current_page_num>$this->total_pages_num)?$this->total_pages_num:$current_page_num;
        $this->current_page_first_record_id=$this->records_num_per_page*($this->current_page_num-1);
        $this->current_page_last_record_id=
            ($this->current_page_num<$this->total_pages_num)?($this->current_page_first_record_id+$this->records_num_per_page-1):($this->total_rows_num-1);
        $this->first_page_num=1;
        $this->last_page_num=$this->total_pages_num;
        $this->pre_page_num=($this->current_page_num>1)?($this->current_page_num-1):1;
        $this->next_page_num=
            ($this->current_page_num<$this->total_pages_num)?($this->current_page_num+1):$this->total_pages_num;
        
        $this->get_current_page_records();
    }

    /* 得到当前页面将要显示的记录。 */
    protected function get_current_page_records() {
        if ($this->total_rows_num > 0) {
            $j=0;
            for ($i=$this->current_page_first_record_id; $i<=$this->current_page_last_record_id; $i++)
            {
                $this->oDb->doSeek($this->result, $i);
                $record = $this->oDb->getOneRecord($this->result);
                $this->rows[$j]=array();
                while(list($key,$val)=each($record))
                {
                    $this->rows[$j][$key]=$val;
                }
                $j++;
            }
        }
    }

}




/* 带有上几页/下几页功能的翻页类 */
Class AppPage extends BasePage {

    /* 一排显示几个分页链接（即上几页/下几页） */
    public $linkNumPerLine;
    /* 总共的链接排数 */
    public $totalLinkLineNum;
    /* 当前链接第几排 */
    public $currLinkLineNum;
    /* 当前链接排的链接数组 */
    public $currLinkLineArray;
    /* 上几页第一页的pageNum */
    public $preLinkLineFirstPageNum;
    /* 下几页第一页的pageNum */
    public $nextLinkLineFirstPageNum;
    
    public $urls;   /* 分页链接数组 */
    
    /* 构造函数。 */
    public function __construct() {
        $this->currLinkLineArray = array();
    }
    
    /**
     * 初始化
     */
    public function init($oDb, $current_page_num=1, $records_num_per_page=15, $linkNumPerLine = 10) {
        $current_page_num = (int) $current_page_num;
        $records_num_per_page = (int) $records_num_per_page;
        $linkNumPerLine = (int) $linkNumPerLine;
        $current_page_num = ($current_page_num < 1) ? 1 : $current_page_num;
        $records_num_per_page = ($records_num_per_page < 1) ? 15 : $records_num_per_page;
        $linkNumPerLine = ($linkNumPerLine < 1) ? 10 : $linkNumPerLine;
        $this->oDb=$oDb;
        $this->result = $oDb->result;
        /* 记录总行数 */
        $this->total_rows_num=$this->oDb->getNumRows($this->result);
        /* 每页记录行数 */
        $this->records_num_per_page=$records_num_per_page;
        /* 页面总数 */
        $this->total_pages_num=ceil($this->total_rows_num / $this->records_num_per_page);
        /* 一排显示几个分页链接（即上几页/下几页） */
        $this->linkNumPerLine = $linkNumPerLine;
        /* 总共的链接排数 */
        $this->totalLinkLineNum = ceil($this->total_pages_num / $this->linkNumPerLine);

        $this->current_page_num=
            ($current_page_num>$this->total_pages_num)?$this->total_pages_num:$current_page_num;
        $this->current_page_first_record_id=$this->records_num_per_page*($this->current_page_num-1);
        $this->current_page_last_record_id= ($this->current_page_num<$this->total_pages_num)?($this->current_page_first_record_id+$this->records_num_per_page-1):($this->total_rows_num-1);
        $this->first_page_num=1;
        $this->last_page_num=$this->total_pages_num;
        $this->pre_page_num=($this->current_page_num>1)?($this->current_page_num-1):1;
        $this->next_page_num=
            ($this->current_page_num<$this->total_pages_num)?($this->current_page_num+1):$this->total_pages_num;
        
        $this->currLinkLineNum = ceil($this->current_page_num / $this->linkNumPerLine);
        /* 初始化当前链接排的链接数组 */
        if($this->currLinkLineNum > 0) {
            for($i=1;$i<=$this->linkNumPerLine;$i++) {
                $pageNum = ($this->currLinkLineNum-1)*$this->linkNumPerLine + $i;
                if($pageNum > $this->total_pages_num) {
                    break;
                } else {
                    $this->currLinkLineArray[] = $pageNum;
                }
            }
        }
        if($this->currLinkLineNum > 1) {
            $this->preLinkLineFirstPageNum = ($this->currLinkLineNum-2)*$this->linkNumPerLine + 1;
        } else {
            $this->preLinkLineFirstPageNum = 1;
        }
        if(($this->currLinkLineNum * $this->linkNumPerLine + 1) > $this->total_pages_num) {
            $this->nextLinkLineFirstPageNum = $this->total_pages_num;
        } else {
            $this->nextLinkLineFirstPageNum = $this->currLinkLineNum * $this->linkNumPerLine + 1;
        }
        
        $this->get_current_page_records();
        $this->makeUrls();
    }
    
    /**
     * 生成$this->urls
     */
    private function makeUrls() {
        $this->urls['firstPageUrl'] = $this->getPageUrl($this->first_page_num);
        $this->urls['lastPageUrl'] = $this->getPageUrl($this->last_page_num);
        $this->urls['preLinkLineFirstPageUrl'] = $this->getPageUrl($this->preLinkLineFirstPageNum);
        $this->urls['nextLinkLineFirstPageUrl'] = $this->getPageUrl($this->nextLinkLineFirstPageNum);
        $this->urls['currLinkLineUrls'] = array();
        foreach ($this->currLinkLineArray as $pageNum) {
            $this->urls['currLinkLineUrls'][$pageNum] = $this->getPageUrl($pageNum);
        }
    }
    
    /**
     * 显示分页html代码
     */
    public function echoPage() {
        if ($this->total_rows_num) {
            $ret = '
            共'.$this->total_rows_num.'条记录
            <a href="'.$this->getPageUrl($this->first_page_num).'">首页</a>&nbsp;
            <a href="'.$this->getPageUrl($this->preLinkLineFirstPageNum).'"><<</a>&nbsp;
            ';
            foreach ($this->currLinkLineArray as $pageNum) {
                $ret .= '
            <a href="'.$this->getPageUrl($pageNum).'">'.(($this->current_page_num == $pageNum)?"<b>$pageNum</b>":"$pageNum").'</a>&nbsp;
                ';
            }
            $ret .= '
            <a href="'.$this->getPageUrl($this->nextLinkLineFirstPageNum).'">>></a>&nbsp;
            <a href="'.$this->getPageUrl($this->last_page_num).'">尾页</a>&nbsp;
            ';
            echo $ret;
        }
    }
    
    /**
     * 返回指定页面的url
     *
     * @param  Integer  $pg  页码
     */
    public function getPageUrl($pg) {
        $ob = ($_GET['ob'] ? $_GET['ob'] : 'empty');
        $od = ($_GET['od'] ? $_GET['od'] : 'asc');
        //return MainApp::$oCf->makeUrl(MPF_C_APPNAME, basename($_SERVER['SCRIPT_NAME']), MainApp::$cmd, $this->makePageParamArr(MainApp::$oCf->cv($pg), $ob, $od));
        $fileName = basename($_SERVER['SCRIPT_NAME']);
        $fileName = ($fileName == 'index.php') ? '' : $fileName;
        return MainApp::$oCf->makeUrl(MPF_C_APPNAME, $fileName, MainApp::$cmd, $this->makePageParamArr(MainApp::$oCf->cv($pg), $ob, $od), true);
    }
    
    /**
     * 构造分页链接需要的参数数组
     *
     * @param  Integer  $pg  页码
     * @param  String  $ob  要排序的列名
     * @param  String  $od  排序方式名，'desc'表示降序，'asc'表示升序
     * @return  返回对应的参数数组
     */
    private function makePageParamArr($pg, $ob, $od) {
        $ret = array('pg' => $pg, 'ob' => $ob, 'od' => $od);
        foreach ($_GET as $key => $value) {
            if ($key != 'pg' && $key != 'ob' && $key != 'od' && $key != 'cmd') {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

}

?>
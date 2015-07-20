<?php

defined('MBQ_IN_IT') or exit;

/**
 * data page class
 * 
 * @since  2012-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqDataPage {
    
    public $totalNum;   /* total data num */
    public $totalUnreadNum;     /* total unread num */
    public $numPerPage; /* data number per page */
    public $totalPage;  /* total page num */
    public $curPage;    /* current page num */
    public $datas;      /* data array */
    
    public $startNum;
    public $lastNum;
    
    public function __construct() {
        $this->datas = array();
        $this->totalNum = 0;
        $this->totalUnreadNum = 0;
    }
    
    /**
     * init by start num and last num
     *
     * @param  Integer  $startNum
     * @param  Integer  $lastNum
     */
    public function initByStartAndLast($startNum, $lastNum) {
        $this->startNum = $startNum;
        $this->lastNum = $lastNum;
        $start = intval($this->startNum);
        $end = intval($this->lastNum);
        $start = empty($start) ? 0 : max($start, 0);
        $end = (empty($end) || $end < $start) ? ($start + 19) : max($end, $start);
        if ($end - $start >= 50) {
            $end = $start + 49;
        }
        $limit = $end - $start + 1;
        $page = intval($start/$limit) + 1;
        $this->startNum = $start;
        $this->numPerPage = $limit;
        $this->curPage = $page;
    }
    
    /**
     * init by page and per page
     *
     * @param  Integer  $page page number
     * @param  Integer  $perPage per page record num
     */
    public function initByPageAndPerPage($page, $perPage) {
        $page = (int) $page;
        if (!$page || $page < 0) $page = 1;
        $perPage = (int) $perPage;
        if (!$perPage || $perPage < 0) $perPage = 20;
        $this->curPage = $page;
        $this->numPerPage = $perPage;
        $this->startNum = ($page - 1) * $perPage;
        $this->lastNum = $this->startNum + $perPage - 1;
    }
    
    /**
     * init by position and per page
     *
     * @param  Integer  $position data position
     * @param  Integer  $perPage per page record num
     */
    public function initByPositionAndPerPage($position, $perPage) {
        $position = (int) $position;
        $perPage = (int) $perPage;
        if (!$perPage || $perPage < 0) $perPage = 20;
        $page = ceil($position / $perPage);
        $this->initByPageAndPerPage($page, $perPage);
    }
  
}

?>
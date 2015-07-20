<?php
require_once('./MainBase.php');

/** 
 * 首页 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MainTopic extends MainBase {
    
    public function __construct() {
        parent::__construct();
        self::selectRewrite('none');
        
        //$this->initSession();
    }
    
    /**
     * thread list
     */
    public function threadList() {
        self::$cmd = 'threadList';
        /* init */
        /* verify */
        /* acl */
        /* do */
        $param = array(
            'fid' => $_GET['fid'],
            'content' => $_GET['content'] ? $_GET['content'] : 'both',
            'page' => $_GET['page'] ? $_GET['page'] : MPF_SITE_DEFAULT_PAGE_NUM,
            'perpage' => $_GET['perpage'] ? $_GET['perpage'] : MPF_SITE_DEFAULT_PER_PAGE_NUM,
            'type' => $_GET['type'] ? $_GET['type'] : 'all',
            'prefix' => $_GET['prefix'] ? $_GET['prefix'] : 0
        );
        $oMnEtForumTopicRd = MainApp::$oClk->newObj('MnEtForumTopicRd');
        $data = $oMnEtForumTopicRd->doThreadList($param);
        self::$title = $data['forum']->forumName->oriValue;
        self::assign('data', $data);
        /* end */
        $this->setTpl('threadList.html');
    }
    
    /**
     * get thread
     */
    public function getThread() {
        self::$cmd = 'getThread';
        /* init */
        self::$title = "get thread";
        /* verify */
        /* acl */
        /* do */
        $param = array(
            'pid' => $_GET['pid'],
            'tid' => $_GET['tid'],
            'uid' => $_GET['uid'],
            'goto' => $_GET['goto'],
            'page' => $_GET['page'] ? $_GET['page'] : MPF_SITE_DEFAULT_PAGE_NUM,
            'perpage' => $_GET['perpage'] ? $_GET['perpage'] : MPF_SITE_DEFAULT_PER_PAGE_NUM,
            'order' => $_GET['order'] ? $_GET['order'] : 'asc'
        );
        $oMnEtForumTopicRd = MainApp::$oClk->newObj('MnEtForumTopicRd');
        $data = $oMnEtForumTopicRd->doGetThread($param);
        self::$title = $data['topic']->topicTitle->oriValue;
        self::assign('data', $data);
        /* end */
        $this->setTpl('getThread.html');
    }
    
    public function run() {
        switch (self::$cmd) {
            case 'threadList':
                $this->threadList();
                break;
            case 'getThread':
                $this->getThread();
                break;
            default:
                self::$oCf->pageReturn('', '', 'MainForum.php', 'forumList', '', array('status' => ERR_APP, 'info' => MainApp::$oCf->_L('cm_param_error')));
                break;
        }
    }
    
    public static function cmEnd($oMain = NULL, $opt = array()) {
        parent::cmEnd();
    }
    
}

$html = new MainTopic();
$html->run();
MainTopic::cmEnd($html);
$html->display();

?>
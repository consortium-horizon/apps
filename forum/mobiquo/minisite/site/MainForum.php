<?php
require_once('./MainBase.php');

/** 
 * 首页 
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MainForum extends MainBase {
    
    public function __construct() {
        parent::__construct();
        self::selectRewrite('none');
        
        //$this->initSession();
    }
    
    /**
     * forum list
     */
    public function forumList() {
        self::$cmd = 'forumList';
        /* init */
        self::$title = "All Forums";
        /* verify */
        /* acl */
        /* do */
        $oMnEtForumRd = MainApp::$oClk->newObj('MnEtForumRd');
        $objsMnEtForum = $oMnEtForumRd->getForumTree();
        self::assign('objsMnEtForum', $objsMnEtForum);
        /* end */
        $this->setTpl('forumList.html');
    }
    
    public function run() {
        switch (self::$cmd) {
            case 'forumList':
                $this->forumList();
                break;
            default:
                $this->forumList();
                break;
        }
    }
    
    public static function cmEnd($oMain = NULL, $opt = array()) {
        parent::cmEnd();
    }
    
}

$html = new MainForum();
$html->run();
MainForum::cmEnd($html);
$html->display();

?>
<?php
/**
 * smarty应用类
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 */
Class AppSmarty Extends Smarty {

    /**
     * 构造函数
     */
    public function __construct() {
        $this->Smarty();
        
        $this->template_dir = MainApp::$oCf->getPath(SMARTY_RUN_DIR) . "templates/";
        $this->compile_dir = MainApp::$oCf->getPath(SMARTY_RUN_DIR) . "templates_c/";
        $this->config_dir = MainApp::$oCf->getPath(SMARTY_RUN_DIR) . "configs/";
        $this->cache_dir = MainApp::$oCf->getPath(SMARTY_RUN_DIR) . "cache/";
        
        $this->caching = false; /* 通常不允许缓存，使用缓存会导致程序出错（会显示/运行缓存过的旧的模板） */
        /*
        echo $this->template_dir.'<br>';
        echo $this->compile_dir.'<br>';
        echo $this->config_dir.'<br>';
        echo $this->cache_dir.'<br>';
        */
    }
    
}

?>
<?php
/**
 * MnEtAtt init class
 * 
 * @since  2013-8-8
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MnEtAttInit Extends AppDo {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * init MnEtAtt object by record
     *
     * @param  Object  $recordAtt
     */
    public function initMnEtAttByRecord($recordAtt) {
        $oMnEtAtt = MainApp::$oClk->newObj('MnEtAtt');
        if (property_exists($recordAtt, 'id')) {
            $oMnEtAtt->attId->setOriValue($recordAtt->id);
        }
        if (property_exists($recordAtt, 'name')) {
            $oMnEtAtt->uploadFileName->setOriValue($recordAtt->name);
        }
        if (property_exists($recordAtt, 'size')) {
            $oMnEtAtt->filtersSize->setOriValue($recordAtt->size);
        }
        if (property_exists($recordAtt, 'type')) {
            $oMnEtAtt->mimeType->setOriValue($recordAtt->type);     //!!!
        }
        if (property_exists($recordAtt, 'url')) {
            $oMnEtAtt->url->setOriValue($recordAtt->url);
        }
        if (property_exists($recordAtt, 'thumbnail')) {
            $oMnEtAtt->thumbnailUrl->setOriValue($recordAtt->thumbnail);
        }
        return $oMnEtAtt;
    }
    
    /**
     * init objsMnEtAtt by records
     *
     * @param  Array  $recordsAtt
     */
    public function initObjsMnEtAttByRecords($recordsAtt) {
        $objsMnEtAtt = array();
        foreach ($recordsAtt as $recordAtt) {
            $objsMnEtAtt[] = $this->initMnEtAttByRecord($recordAtt);
        }
        return $objsMnEtAtt;
    }
    
}

?>
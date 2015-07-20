<?php

defined('MBQ_IN_IT') or exit;

/**
 * thank read class
 * 
 * @since  2012-9-24
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseRdEtThank extends MbqBaseRd {
    
    public function __construct() {
    }
    
    /**
     * return thank api data
     *
     * @param  Object  $oMbqEtThank
     * @return  Array
     */
    public function returnApiDataThank($oMbqEtThank) {
        $data = array();
        if ($oMbqEtThank->userId->hasSetOriValue()) {
            $data['userid'] = (string) $oMbqEtThank->userId->oriValue;
        }
        if ($oMbqEtThank->oMbqEtUser) {
            $data['username'] = (string) $oMbqEtThank->oMbqEtUser->getDisplayName();
        }
        return $data;
    }
    
    /**
     * return thank array api data
     *
     * @param  Array  $objsMbqEtThank
     * @return  Array
     */
    public function returnApiArrDataThank($objsMbqEtThank) {
        $data = array();
        foreach ($objsMbqEtThank as $oMbqEtThank) {
            $data[] = $this->returnApiDataThank($oMbqEtThank);
        }
        return $data;
    }
    
    /**
     * get thank objs
     *
     * @return  Mixed
     */
    public function getObjsMbqEtThank() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * init one thank by condition
     *
     * @return  Mixed
     */
    public function initOMbqEtThank() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
  
}

?>
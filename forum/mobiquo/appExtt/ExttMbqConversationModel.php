<?php
/**
 * ExttMbqConversationModel extended from ConversationModel
 * add method exttMbqGetPcs() modified from method Get().
 * modify method __construct()
 * 
 * @since  2012-11-5
 * @modified by Wu ZeTao <578014287@qq.com>
 */
class ExttMbqConversationModel extends ConversationModel {
   /**
    * Class constructor. Defines the related database table name.
    * 
    * @since 2.0.0
    * @access public
    */
   public function __construct() {
      parent::__construct();
   }
   
   /**
    * Get list of conversations.
    * 
    * Events: BeforeGet.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param int $ViewingUserID Unique ID of current user.
    * @param int $Offset Number to skip.
    * @param int $Limit Maximum to return.
    * @param array $Wheres SQL conditions.
    * @param array $mbqOpt
    $mbqOpt['convIds'] means get data by conversation ids
    * @return Gdn_DataSet SQL results.
    */
   public function exttMbqGetPcs($ViewingUserID, $Offset = '0', $Limit = '', $Wheres = '', $mbqOpt = array()) {
      //if ($Limit == '') 
         //$Limit = Gdn::Config('Conversations.Conversations.PerPage', 50);
      if (!$Offset && !$Limit) {
        $noLimit = true;
      }

      $Offset = !is_numeric($Offset) || $Offset < 0 ? 0 : $Offset;
      
      $this->ConversationQuery($ViewingUserID);
      
      if (is_array($Wheres))
         $this->SQL->Where($Wheres);
      
      $this->FireEvent('BeforeGet');
      
      if ($mbqOpt['convIds']) {
        $this->SQL->WhereIn('c.ConversationID', $mbqOpt['convIds']);
      }
      $mbqExttSqlNoLimit = $this->SQL->GetSelect();
      $oSql1 = clone $this->SQL;
      $arr['total'] = $this->SQL->Get()->NumRows();
      if ($noLimit) {
        $mbqExttSql = $mbqExttSqlNoLimit;
      } else {
        $mbqExttSql = "$mbqExttSqlNoLimit order by c.DateUpdated desc limit $Offset,$Limit";
      }
      $arr['pcs'] = $oSql1->Query($mbqExttSql);
      return $arr;
      /*
      return $this->SQL
         ->OrderBy('c.DateUpdated', 'desc')
         ->Limit($Limit, $Offset)
         ->Get();
      */
   }
   
}

?>
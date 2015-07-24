<?php if (!defined('APPLICATION')) exit();

class MarketSubscriptionsModel extends VanillaModel{
    
    public static $ByUser = array();
    public static $ByID = array();
    
    public function __construct() {
        parent::__construct('MarketSubscriptions');
    }
    
    public function Set($UserID,$SubscriptionID,$Gateway,$SubscriptionPeriod,$SubscriptionData){
        
        self::$ByID=null;
        self::$ByUser=null;
        $Subscription = $this->GetByID($SubscriptionID);
        if(!$Subscription){
            $this->SQL
            ->Insert('MarketSubscriptions',
                array(
                    'UserID'=>$UserID,
                    'SubscriptionID'=>$SubscriptionID,
                    'Gateway'=>$Gateway,
                    'ExpireDate'=>Gdn_Format::ToDateTime(strtotime('+'.$SubscriptionPeriod)),
                    'SubscriptionData'=>Gdn_Format::Serialize($SubscriptionData)
                )
                
            );
        }else{
            
            $this->SQL
            ->Put('MarketSubscriptions', 
                array(
                    'UserID'=>$UserID,
                    'Gateway'=>$Gateway,
                    'ExpireDate'=>Gdn_Format::ToDateTime(strtotime('+'.$SubscriptionPeriod)),
                    'SubscriptionData'=>Gdn_Format::Serialize($SubscriptionData)
                ), 
                array(
                    'SubscriptionID'=>$SubscriptionID
                )
            );
        }
    
    }
    
    public function GetByUser($UserID,$Gateway=null,$Expired=false){
        $Result = GetValueR($UserID.'.'.($Gateway?$Gateway:'None'), self::$ByUser,null);
        if($Result!=null)
            return $Result;
        $SQL = $this->SQL
        ->Select('ms.*')
        ->From('MarketSubscriptions ms')
        ->Where('ms.UserID',$UserID);
        
        if($Gateway)
            $SQL
            ->Where('ms.Gateway',$Gateway);
            
        $Result = $SQL
        ->Get()
        ->FirstRow();
        
        if(!$Result)
            return FALSE;

        $Result->SubscriptionData=Gdn_Format::Unserialize($Result->SubscriptionData);
        
        if(empty(self::$ByUser[$UserID]))
            self::$ByUser=array();
        
        self::$ByUser[$UserID][$Gateway]=$Result;
        
        return $Result;
    }
    
    public function GetByID($SubscriptionID){
        $Result = GetValue($SubscriptionID, self::$ByID,null);
        
        if($Result!=null)
            return $Result;
            
        $Result = $this->SQL
        ->Select('ms.*')
        ->From('MarketSubscriptions ms')
        ->Where('ms.SubscriptionID',$SubscriptionID)
        ->Get()
        ->FirstRow();
        if(!$Result)
            return FALSE;
        $Result->SubscriptionData=Gdn_Format::Unserialize($Result->SubscriptionData);
        
        
        self::$ByID[$SubscriptionID]=$Result;
        return $Result;
    }
    
    public function GetExpired($Gateway){
        $Result = $this->SQL
        ->Select('ms.*')
        ->From('MarketSubscriptions ms')
        ->Where('ms.Gateway',$Gateway)
        ->Where('ms.UserID',Gdn::Session()->UserID)
        ->Where('ms.ExpireDate<',Gdn_Format::ToDateTime())
        ->Get()
        ->FirstRow();
        if(!$Result)
            return FALSE;
        $Result->SubscriptionData=Gdn_Format::Unserialize($Result->SubscriptionData);
        return $Result;        
    }
    
}
//alias
class MarketSubscriptions extends MarketSubscriptionsModel{
  function __contruct(){
    parent::__construct();
  }
}

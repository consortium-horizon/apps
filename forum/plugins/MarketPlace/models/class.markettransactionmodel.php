<?php if (!defined('APPLICATION')) exit();;

class MarketTransactionModel extends VanillaModel{
    
    private static $Latest=null;
    private static $Meta=array();
    
    public function __construct() {
        parent::__construct('MarketTransaction');
    }
       
    public function GetLog($Limit=20,$Offset=0,$OrderBy='Date',$Sort='DESC',$Search='',$SearchCol='',$Historical=false){
        if($OrderBy!='Date')
            $OrderBy= $OrderBy=='Name' || Gdn::Structure()->Table('MarketTransaction'.($Historical?'History':''))->ColumnExists($OrderBy)?$OrderBy:'Date';
        
        $SearchCol = $SearchCol=='Name' || Gdn::Structure()->Table('MarketTransaction'.($Historical?'History':''))->ColumnExists($SearchCol)?$SearchCol:'';
        $this->SQL
        ->Select('mt.*,u.Name')
        ->From('MarketTransaction'.($Historical?'History':'').' mt')
        ->Join('User u','mt.PurchaseUserID=u.UserID');//join ok for backend, and needed for sort/search
        if(!empty($Search) && !empty($SearchCol))
            $this->SQL
                ->Like(($SearchCol=='Name'?'u.':'mt.').$SearchCol,$Search);
        $this->SQL
        ->OrderBy(($OrderBy=='Name'?'u.':'mt.').($OrderBy),(strtolower($Sort)=='asc'?'asc':'desc'))
        ->Limit($Limit,$Offset);
        $Meta=$this->SQL
        ->Get()
        ->Result();
        
        
        
        return $Meta;
    }
     
    public function Log($UserID,$ProductSlug,$TransactionID,$GateWay,$GatewayTransactionID,$Status,$Meta){
        $CacheID=implode('|',array_filter(array($UserID,$ProductSlug,$TransactionID)));
        self::$Latest[$CacheID]=null;
        $this->SQL
        ->Insert('MarketTransactionHistory',
            array(
                'PurchaseUserID'=>$UserID,
                'TransactionID'=>$TransactionID,
                'Gateway'=>$GateWay,
                'GatewayTransactionID'=>$GatewayTransactionID,
                'ProductSlug'=>$ProductSlug,
                'Status'=>$Status,
                'Date'=>Gdn_Format::ToDateTime(),
                'Meta'=>Gdn_Format::Serialize($Meta)
            )
        );
        
        if($this->GetLatest($UserID,$ProductSlug,$TransactionID,$GatewayTransactionID)){
            $this->SQL
            ->Update('MarketTransaction')
            ->Set(
                array(
                    'PurchaseUserID'=>$UserID,
                    'Status'=>$Status,
                    'Inform'=>false,
                    'Date'=>Gdn_Format::ToDateTime(),
                    'Meta'=>$Meta
                )
            )
            ->Where(
                array(
                    'PurchaseUserID'=>$UserID,
                    'TransactionID'=>$TransactionID,
                    'ProductSlug'=>$ProductSlug,
                    'GatewayTransactionID'=>$GatewayTransactionID
                )
            )
            ->Put();
            
        }else{
            $this->SQL
            ->Insert('MarketTransaction',
                array(
                    'PurchaseUserID'=>$UserID,
                    'TransactionID'=>$TransactionID,
                    'GateWay'=>$GateWay,
                    'GatewayTransactionID'=>$GatewayTransactionID,
                    'ProductSlug'=>$ProductSlug,
                    'Status'=>$Status,
                    'Date'=>Gdn_Format::ToDateTime(),
                    'Meta'=>$Meta
                )
            );
            
        }
    }
    
    public static function SetTransactionMeta($TransactionID,$Meta){
        $StoredMeta = self::GetTransactionMeta($TransactionID);
        if(empty($StoredMeta)){
            Gdn::SQL()
            ->Insert('MarketTransactionMeta',
                array(
                    'TransactionID'=>$TransactionID,
                    'Meta'=>Gdn_Format::Serialize($Meta),
                    'Date'=>Gdn_Format::ToDateTime()
                )
            );
        }else{
            Gdn::SQL()
            ->Update('MarketTransactionMeta')
            ->Set(
                array(
                    'Meta'=>Gdn_Format::Serialize($Meta),
                    'Date'=>Gdn_Format::ToDateTime()
                )
            )
            ->Where(
                array(
                    'TransactionID'=>$TransactionID,
                    
                )
            )
            ->Put();
        }
        
        self::$Meta[$TransactionID]=$Meta;
    }
    
    
    public static function GetTransactionMeta($TransactionID){

        $Meta = GetValue($TransactionID,self::$Meta);
        if(!$Meta){
            $Result = Gdn::SQL()
            ->Select('tm.*')
            ->From('MarketTransactionMeta tm')
            ->Where(
                array(
                    'tm.TransactionID'=>$TransactionID
                )
            )
            ->Get()
            ->FirstRow();
            
            if($Result)
                $Meta = $Result->Meta;
        }
            
        self::$Meta[$TransactionID]=$Meta;    

        
        return !$Meta?array():Gdn_Format::Unserialize($Meta);
    }
    
    public static function PurgeTransactionMeta(){
        
        
        if(!C('Plugins.MarketPlace.MetaPurgeLast') || strtotime('+'.C('Plugins.MarketPlace.MetaPurgeExpireIn','1 day'), C('Plugins.MarketPlace.MetaPurgeLast')) <= now()){
            Gdn::SQL()
            ->Delete('MarketTransactionMeta',
                array(
                    'Date<'=>Gdn_Format::ToDateTime(strtotime(C('Plugins.MarketPlace.MetaPurgeBefore','-2 weeks'))),
                    'Persist'=>FALSE
                )
            );
        
            SaveToConfig(array('Plugins.MarketPlace.MetaPurgeLast'=>time()));
        }
    }

    public function GetLatest($UserID,$ProductSlug=null,$TransactionID=null, $GatewayTransactionID=null){
        $CacheID=implode('|',array_filter(array($UserID,$ProductSlug,$TransactionID)));
        $Latest = GetValue($CacheID,self::$Latest);
        if(!empty($Latest))
            return $Latest;
        $this->SQL
        ->Select('mt.*')
        ->From('MarketTransaction mt')
        ->Where('mt.PurchaseUserID',$UserID);

        if($ProductSlug)
            $this->SQL
                ->Where('mt.ProductSlug',$ProductSlug);

        if($TransactionID)
            $this->SQL
                ->Where('mt.TransactionID',$TransactionID);
                
        if($GatewayTransactionID)
            $this->SQL
                ->Where('mt.GatewayTransactionID',$GatewayTransactionID);

        $Latest=$this->SQL
        ->Get()
        ->FirstRow();
        self::$Latest[$CacheID]=$Latest;

        return $Latest;

    }
     
    public function DismissInform($UserID,$Date,$TransactionID){
        $this->SQL
        ->Update('MarketTransaction')
        ->Set(
            array(
                'Inform'=>TRUE
            )
        )
        ->Where(
            array(
                'PurchaseUserID'=>$UserID,
                'Date'=>$Date,
                'TransactionID'=>$TransactionID            
            )
        )
        ->Put();
    }


}
//alias
class MarketTransaction extends MarketTransactionModel{
  function __contruct(){
    parent::__construct();
  }
}

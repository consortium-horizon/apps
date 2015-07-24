<?php if (!defined('APPLICATION')) exit();

class MarketProductModel extends VanillaModel{
    public function __construct() {
        parent::__construct('MarketProduct');
    }
    
    public function Set($Name,$Description,$ProductType,$PriceDenominations,$Meta,$EnabledGateways){
        

        $Product = $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp')
        ->Where('Name',$Name)
        ->Get()
        ->FirstRow();
        if(!$Product){
            $this->SQL
            ->Insert('MarketProduct',
                array(
                    'Name'=>$Name,
                    'Slug'=>Gdn_Format::Url(strtolower($Name)),
                    'Description'=>$Description,
                    'ProductType'=>$ProductType,
                    'PriceDenominations'=>Gdn_Format::Serialize($PriceDenominations),
                    'Meta'=>!empty($Meta)?Gdn_Format::Serialize($Meta):null,
                    'EnabledGateways'=>Gdn_Format::Serialize($EnabledGateways)
                )
                
            );
        }else{
            
            $this->SQL
            ->Put('MarketProduct', 
                array(
                    'Description'=>$Description,
                    'Slug'=>Gdn_Format::Url(strtolower($Name)),
                    'ProductType'=>$ProductType,
                    'PriceDenominations'=>Gdn_Format::Serialize($PriceDenominations),
                    'Meta'=>!empty($Meta)?Gdn_Format::Serialize($Meta):null,
                    'EnabledGateways'=>Gdn_Format::Serialize($EnabledGateways)
                ), 
                array(
                    'Name'=>$Name
                )
            );
        }
    
    }
    
    public function GetAll($Limit=5, $Offset=0){
        return $this->Get($Limit, $Offset, null, true, true);
    }
    
    public function GetSaleable($Limit=5, $Offset=0){
        return $this->Get($Limit, $Offset, null, true, false);
    }
    
    public function Get($Limit=5, $Offset=0,$ProductType=null, $LinkProductTypes=true, $ShowAll=false){
        $SQL = $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp');
        
        //magic to get available Product Types
        if(!$ProductType && $LinkProductTypes){
            $Plgn = Gdn::PluginManager()->GetPluginInstance('MarketPlace');
            if($Plgn){
                $ProductTypes = empty($Plgn->API()->ProductTypes) ? array() : array_keys($Plgn->API()->ProductTypes);
                if($ShowAll){
                    $SQL->Select(
                        'mp.ProductType IN('.
                            join(',',empty($ProductTypes) ? array("TRUE") : array_map(array($SQL->Database->Connection(),'quote'),$ProductTypes)).
                        '),TRUE,FALSE',
                        'IF',
                        'HasProductType'
                    );
                }else{
                    if(empty($ProductTypes)){
                        return array();
                    }
                    $ProductType = $ProductTypes;
                }
            }
        }
        
        if($ProductType){
            if(!is_array($ProductType))
                $ProductType=array($ProductType);
            $SQL
            ->WhereIn('mp.ProductType',$ProductType);
        }
        return $SQL
        ->OrderBy('mp.ShowOrder')
        ->Limit($Limit,$Offset)
        ->Get()
        ->Result();
    }
    
    public function GetCount(){
        return $this->SQL
        ->Select('Count(mp.Name) CountProducts')
        ->From('MarketProduct mp')
        ->Get()
        ->FirstRow()
        ->CountProducts;
    }
    
    public function GetBySlug($Slug,$ProductType=null){
        $SQL = $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp');
        if($ProductType){
            if(!is_array($ProductType))
                $ProductType=array($ProductType);
            $SQL
            ->WhereIn('mp.ProductType',$ProductType);
        }
        return $SQL
        ->Where('mp.Slug',$Slug)
        ->Get()
        ->FirstRow();
    }
    
    public function GetBySlugs($Slugs){
        
        return $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp')
        ->WhereIn('mp.Slug',$Slugs)
        ->Get()
        ->Result();
    }
    
    function DeleteBySlug($Slug){
        return $this->SQL
        ->Delete('MarketProduct',
            array(
                'Slug'=>$Slug
            )
        );
    }
    
    public function SwapOrder($Slug, $Direction='up'){
        
        $Current= $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp')
        ->Where('mp.Slug', $Slug)
        ->Get()
        ->FirstRow();
        
        if(!$Current)
            return;
        
        $Next= $this->SQL
        ->Select('mp.*')
        ->From('MarketProduct mp')
        ->Where('mp.ShowOrder'.($Direction=='down'?'>':'<'), $Current->ShowOrder)
        ->OrderBy('mp.ShowOrder', ($Direction=='down'?'asc':'desc'))
        ->Limit(1,0)
        ->Get()
        ->FirstRow();
        
        if(!$Next)
            return;
        
        $this->SQL
        ->Update('MarketProduct')
        ->Set('ShowOrder',$Next->ShowOrder)
        ->Where('ProductID',$Current->ProductID)
        ->Put();
        
        $this->SQL
        ->Update('MarketProduct')
        ->Set('ShowOrder',$Current->ShowOrder)
        ->Where('ProductID',$Next->ProductID)
        ->Put();
    }
     
}
//alias
class MarketProduct extends MarketProductModel{
  function __contruct(){
    parent::__construct();
  }
}

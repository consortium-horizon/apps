<?php if (!defined('APPLICATION')) exit(); ?>
<div class="ProductListingItem">
<?php
    $Product=$this->Data['MarketProduct'];
    $TransactionID=$this->Data['TransactionID'];
    $Description = Gdn_Format::Html($Product->Description);
    $PriceDenominations = $Product->PriceDenominations;
    $PriceDenom=array();
    $MetaID=0;
    foreach($PriceDenominations As $PDI => $PDV)
        $PriceDenom[]= '<span class="Price">'.sprintf('%01.2f',$PDV).'</span> <span class="Currency">'.Gdn_Format::Text(T($PDI)).'</span>';
    $PriceDenom=join('<br />',$PriceDenom);
    $Meta = $Product->Meta;
    $MetaValues=array();
    foreach($Meta As $MI => $MV){
        $Edit=false;
        if(substr($MV,-4)=='/any'){
            $MV=substr($MV,0,-4);
            $Edit=true;
        }
        
        $MetaID++;
        if(!in_array($MI,GetValueR($Product->ProductType.'.Options.HideMeta',$this->MarketPlace->Plgn->API()->ProductTypes, array())))
            $MetaValues[]='<span class="MetaIndex '.($Edit?' EditMeta':'').'" id="Meta_'.$MetaID.'_'.$MI.'" >'.Gdn_Format::Text(T($MI)).':</span> <span class="MetaValue'.($Edit?' EditMeta':'').'">'.Gdn_Format::Text($MV).'</span>';
    }
    $MetaValues=join('<br />',$MetaValues);
?>
<h1 class="ProductName"><?php echo $Product->Name; ?></h1>
    <div class="Message Errors"></div>
    <ul class="DataList ProductData">
        <li class="ProductDecription"><?php echo $Description; ?></li>
        <li class="ProductMeta"><?php echo $MetaValues; ?></li>
        <li class="ProductPayButton Box">
<?php 
    include($this->MarketPlace->Plgn->Utility()->ThemeView('buttons'));
    echo DrawButtons($this,$PriceDenominations,$Product,$TransactionID);
?>
        </li>
    </ul>
</div>

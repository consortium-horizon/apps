<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo sprintf(T('Products in %s'),C('Plugins.MarketPlace.StoreName','My Store'));?></h1>
<div class="ProductListings">
<?php
foreach($this->Data['MarketProducts'] As $ProductI=>$Product){
    $Description = Gdn_Format::Html($Product->Description);
    $PriceDenominations = Gdn_Format::Unserialize($Product->PriceDenominations);
    $PriceDenom=array();
    foreach($PriceDenominations As $PDI => $PDV){
        $PriceDenom[]= '<span class="Price">'.sprintf('%01.2f',$PDV).'</span> <span class="Currency">'.Gdn_Format::Text(T($PDI)).'</span>';
    }
    $PriceDenom=join(', ',$PriceDenom);
    $Meta = Gdn_Format::Unserialize($Product->Meta);
    $MetaValues=array();
    foreach($Meta As $MI => $MV){
        if(substr($MV,-4)=='/any')
            $MV=T('any');

        if(!in_array($MI,GetValueR($Product->ProductType.'.Options.HideMeta',$this->MarketPlace->Plgn->API()->ProductTypes, array())))
            $MetaValues[]='<span class="MetaIndex">'.Gdn_Format::Text(T($MI)).':</span> <span class="MetaValue">'.Gdn_Format::Text($MV).'</span>';
    }
    $MetaValues=join('<br />',$MetaValues);
?>

<div class="ProductListing">
<h2 class="ProductName"><?php echo Anchor($Product->Name,C('Plugins.MarketPlace.StoreURI','store').'/'.C('Plugins.MarketPlace.ProductURI','item').'/'.$Product->Slug); ?></h2>
    <ul class="DataList ProductData">
        <li class="ProductDecription"><?php echo $Description; ?></li>
        <li class="ProductMeta"><?php echo $MetaValues; ?></li>
        <li class="ProductPriceDenom"><?php echo '<span class="PriceList">'.T('Price:').'</span> '. $PriceDenom; ?></li>
        <li class="ProductBuyNow"><?php echo Anchor(T('Buy Now'),C('Plugins.MarketPlace.StoreURI','store').'/'.C('Plugins.MarketPlace.ProductURI','item').'/'.$Product->Slug,array('class'=>'Button')); ?></li>        
    </ul>
</div>
<div class="ProductListingClear"></div>
<?php
}
?>
</div>

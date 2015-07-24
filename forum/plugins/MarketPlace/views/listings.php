<?php if (!defined('APPLICATION')) exit(); ?>
<style>
.MarketButton{
    margin-top:5px!important; 
}

form span{
    display:inline!important;
}

.ProductListings{
    margin:20px;
    font-size:12px;
}

.ProductListings table th, .ProductListings table td {
    padding: 6px 2px;
}

fieldset{
    border:1px solid #CCCCCC;
    width:350px;
    padding:5px;
    margin-bottom:6px;
}

label{
    display:block;
}

label.Inline{
    display:inline;
}

.Tabs ul{
    margin-bottom:20px;
}

.Tabs ul li {
    display: inline;
    margin: 2px;
    padding: 5px;
    
}
.Tabs ul li .SmallButton{
    text-align: center;
}
tr{
    color:#000;
}
tr.Disabled{
    color:#aaaaaa;
}
</style>
<div class="ProductListings">
<h2><?php echo T('Product Listings'); ?></h2>
<table>
    <tr>
        <th><?php echo T('Name') ?></th>
        <th><?php echo T('Description'); ?></th>
        <th><?php echo T('Product Type'); ?></th>
        <th><?php echo T('Price Denominations'); ?></th>
        <th><?php echo T('Meta'); ?></th>
        <th><?php echo T('Edit'); ?></th>
        <th><?php echo T('Delete'); ?></th>
        <th><?php echo T('Order'); ?></th>
    </tr>
<?php
foreach($this->Data['MarketProducts'] As $Product){
    $Description = Gdn_Format::Text($Product->Description);
    $PriceDenominations = Gdn_Format::Unserialize($Product->PriceDenominations);
    $PriceDenom=array();
    foreach($PriceDenominations As $PDI => $PDV)
        $PriceDenom[]= $PDV.' '.$PDI;
    $PriceDenom=join('<br />',$PriceDenom);
    $Meta = Gdn_Format::Unserialize($Product->Meta);
    $MetaValues=array();
    foreach($Meta As $MI => $MV)
        $MetaValues[]=$MI.' '.$MV;
    $MetaValues=join('<br />',$MetaValues);
    $Disabled = !$Product->HasProductType;
?>
    <tr <?php echo $Disabled ? 'class="Disabled" ':''; ?>>
        <td><?php echo $Disabled ? Gdn_Format::Text($Product->Name) : Anchor($Product->Name,C('Plugins.MarketPlace.StoreURI','store').'/'.C('Plugins.MarketPlace.ProductURI','item').'/'.$Product->Slug); ?></td>
        <td><?php echo substr($Description,0,100).(strlen($Description)>100?'...':''); ?></td>
        <td><?php echo $Product->ProductType; ?></td>
        <td><?php echo $PriceDenom; ?></td>
        <td><?php echo $MetaValues; ?></td>
        <td><?php echo $Disabled ? T('[Disabled]') : Anchor(T('Edit'),'#AddEditProduct',array('class'=>'EditProduct Button SmallButton','id'=>'Edit_'.$Product->Slug)); ?></td>
        <td><?php echo $Disabled ? T('[Disabled]') : Anchor(T('Delete'),'/settings/marketplace/'.$Product->Slug.'/delete',array('class'=>'DeleteProduct Button SmallButton')); ?></td>
        <td><?php 
            echo Anchor(T('&uarr;'),Url('/settings/marketplace').'/'.$Product->Slug.'/up/?r='.rawurldecode(Url('',true)),array('class'=>'UpProduct Button SmallButton'));
            echo Anchor(T('&darr;'),Url('/settings/marketplace').'/'.$Product->Slug.'/down/?r='.rawurldecode(Url('',true)),array('class'=>'DownProduct Button SmallButton'));
        ?></td>
    </tr>
<?php
}
?>
</table>
<?php echo $this->Pager->Render(); ?>
</div>
<?php
    echo $this->Form->Open(array('id'=>'AddEditProduct'));
    if($this->Form->GetValue('Task')=='AddEditProduct')
        echo $this->Form->Errors();
    $this->Form->AddHidden('Task','AddEditProduct');
    echo $this->Form->Hidden('Task',array('value'=>'AddEditProduct'));
?>
<div class="Configuration">
   <div class="ConfigurationForm">
    <ul>
        <li>
            <h2><?php echo T('Add/Edit Product'); ?></h2>
            <?php echo $this->Form->Label('Name'); ?>
        </li>
        <li>
            <?php
            echo $this->Form->TextBox('Name');
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Description'); ?>
        </li>
        <li>
            <?php
            echo $this->Form->TextBox('Description',array('MultiLine'=>TRUE));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Product Type'); ?>
        </li>
        <li>
            <?php
            echo $this->Form->Dropdown('ProductType',array_combine(array_keys($this->Data['ProductTypes']),array_keys($this->Data['ProductTypes'])));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Price'); ?>
        </li>
        <li class="PriceLine">
            <?php
            echo '<span>'.T('Amount').' </span>';
            echo $this->Form->TextBox('Amount[]',array('class'=>'InputBox Amount'));
            echo ' <span>'.T('Denomination').' </span>';
            echo $this->Form->Dropdown('Currency[]',array(''=>''),array('class'=>'InputBox Currency'));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Meta'); ?>
        </li>
        <li class="MetaLine">
            <?php
            //echo '<span>'.T('Meta Name').' </span>';
            echo $this->Form->TextBox('MetaName[]',array('class'=>'InputBox MetaName'));
            //echo ' <span>'.T('Meta Value').' </span>';
            echo ' <span>'.T(' = ').' </span>';
            echo $this->Form->TextBox('MetaValue[]',array('class'=>'InputBox MetaValue'));
            //echo ' <span style="visibility:hidden">'.T('User Variable').' </span>';
            echo ' <span style="visibility:hidden">'.T('Set by User').' </span>';
            echo $this->Form->Dropdown('MetaAny[]',array(T('Off'),T('On')), array('class'=>'MetaAny','readonly'=>'readonly','style'=>'visibility:hidden'));
            ?>
        </li>
        <li>
            <?php echo $this->Form->Label('Payment Gateways'); ?>
        </li>
        <li>
        <?php
        $EnabledGateways = $this->Form->GetValue('Gateway');
        foreach($this->Data['Gateways'] As $Gateway){
            $Enabaled = GetValue($Gateway,$EnabledGateways,1);
            echo '<span>'.T($Gateway).' </span>';
            echo '<select id="Form_Gateway'.$Gateway.'" name="Form/Gateways['.$Gateway.']" class="GatewayDrop" value="'.$Enabaled.'"><option value="1">'.T('On').'</option><option value="0">'.T('Off').'</option></select>';
            echo '<span> </span>';
        }
        ?>
        </li>
        <li>
            <?php echo $this->Form->Button('Add/Edit',array('class'=>'SmallButton MarketButton')); ?>
        </li>
    </ul>
   </div>
</div>
<?php
    echo $this->Form->Close();

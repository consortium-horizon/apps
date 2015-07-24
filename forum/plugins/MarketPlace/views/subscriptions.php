<?php if (!defined('APPLICATION')) exit(); 
if ($this->Data['UserSubscription']) {
?>
<ul class="DataList Subscriptions">
<li class="Item SubscriptionTitles">
<div class="ItemContent">
<div class="Item"><?php echo T('Subscription') ?></div>
<div class="NextTerm"><?php echo T('Next Term') ?></div>
<div class="Status"> <span class="Status"><?php echo T('Status') ?></span></div>
<div class="Type"><?php echo T('Type') ?></div>
<div class="Ops"></div>
<div class="Clear"></div>
</div>
</li>
<?php
    $UserSubscription = $this->Data['UserSubscription'];
    $Buttons = '';

    $SubscriptionData = $UserSubscription->SubscriptionData;
        
    foreach($SubscriptionData As $TransactionID =>$SubscriptionLine){
        if($SubscriptionLine['Status']=='Canceled' || $SubscriptionLine['Status']=='Failed'){
            $Buttons = Anchor('Renew',C('Plugins.MarketPlace.StoreURI','store').'/'.strtolower($UserSubscription->Gateway).'trans/subscription/renew/'.$UserSubscription->SubscriptionID.'/'.$TransactionID, array('class'=>'SubscriptionButton Button SmallButton'));
        }else{
            $Buttons = Anchor('Cancel',C('Plugins.MarketPlace.StoreURI','store').'/'.strtolower($UserSubscription->Gateway).'trans/subscription/cancel/'.$UserSubscription->SubscriptionID.'/'.$TransactionID, array('class'=>'SubscriptionButton Button SmallButton'));
            
        }
?>
<li class="Item Subscription">
<div class="ItemContent">
<div class="Item"><?php echo $SubscriptionLine['Item'] ?></div>
<div class="NextTerm"><?php echo $SubscriptionLine['Status']=='Active' ? Gdn_Format::Date($UserSubscription->ExpireDate) : '&nbsp;'; ?></div>
<div class="Status"><?php echo $SubscriptionLine['Status'] ? $SubscriptionLine['Status']  : '&nbsp;'  ?></div>
<div class="Type"><?php echo sprintf('%01.2f %s (%s recurring)',$SubscriptionLine['Price'],$SubscriptionLine['Currency'], $SubscriptionLine['Meta']['Period']) ?></div>
<div class="Ops"><span class="Buttons"><?php echo $Buttons; ?></span></div>
<div class="Clear"></div>
</div>
</li>
<?php
    }
?>
</ul>
<?php
//echo $this->Pager->Render();
} else {
   echo '<div class="Empty">'.T('You do not have any Subscriptions yet.').'</div>';
}

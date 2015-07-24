<?php if (!defined('APPLICATION')) exit();
class PayPalGateway{
    public static function PayPalButton($Sender,$TransactionID,$Product,$Currency,$Price){
        $PayPal=C('Plugins.MarketPlace.Gateway.PayPal');
        $Account = GetValue('Account',$PayPal)?GetValue('Account',$PayPal):C('Plugins.PremiumAccounts.PayPalAccount');
        $AccountType = GetValue('AccountType',$PayPal)?GetValue('AccountType',$PayPal):C('Plugins.PremiumAccounts.AccountType') ;
        $ReturnComplete = GetValue('ReturnComplete',$Sender->MarketPlace->ProductTypes[$Product->ProductType]['Options']);
        $VariableMeta = MarketPlaceAPI::VariableMeta($Sender,$Product,'PayPal');
        $TransMeta = MarketTransactionModel::GetTransactionMeta($TransactionID);
        $QuantityName = GetValue('Quantity',$Sender->MarketPlace->Gateways['PayPal']['Options']['VariableMeta']);
        $Subscription = FALSE;
        $SubscriptionPeriod = GetValue('Period', $Product->Meta);
        
        ?>
        <form name="_xclick"  action="<?php echo 'https://www.'.(($AccountType)!=='Live'?'sandbox.':'').'paypal.com/cgi-bin/webscr'; ?>" method="post">
        <div>
        <input type="hidden" name="cmd" value="_xclick<?php $Subscription?'-subscriptions':'';?>" />
        <input type="hidden" name="business" value="<?php echo $Account; ?>">
        <input type="hidden" name="currency_code" value="<?php echo $Currency; ?>">
        <?php if($Subscription){ ?>
            <input type="hidden" name="a3" value="<?php echo sprintf('%01.2f',$Price); ?>">
            <input type="hidden" name="p3" value="<?php echo floor(strtotime($SubscriptionPeriod)/(60*60*24)); ?>">
            <input type="hidden" name="t3" value="D">
            <input type="hidden" name="src" value="1">
            <input type="hidden" name="sra" value="1">
        <?php } else { ?>
            <input type="hidden" name="amount" value="<?php echo sprintf('%01.2f',$Price); ?>">
        <?php } ?>
        <?php
            $Quantity=1;
            foreach($VariableMeta['Fields'] As $FieldN=>$FieldV){
                $FieldV = GetValue($FieldN,$TransMeta,$FieldV);
                if($FieldN=='Quantity'){
                    $Quantity=$FieldV;
                    echo '<input type="hidden" class="Quantity" name="'.($QuantityName?$QuantityName:$FieldN).'" value="'.$FieldV.'" />';
                }else{
                    echo '<input type="hidden" name="'.$FieldN.'" value="'.$FieldV.'" />';
                }
            }
        ?>
        <input type="hidden" name="item_name" value="<?php echo Gdn_Format::Text($Product->Slug);?>">
        <input type="hidden" name="item_number" value="<?php echo $TransactionID; ?>">
        <input type="hidden" name="no_shipping" value="<?php echo !GetValue('shipping',$Product->Meta)?1:0;?>">
        <input type="hidden" name="notify_url" value="<?php echo MarketPlaceAPI::TransUrl('PayPal',Gdn::Session()->UserID,$Product,$TransactionID); ?>">
        <input type="hidden" name="return" value="<?php echo $ReturnComplete?Url($ReturnComplete,TRUE):MarketPlaceAPI::TransUrl('PayPal',Gdn::Session()->UserID,$Product,$TransactionID,'complete'); ?>">
        <input type="hidden" name="cancel_return" value="<?php echo MarketPlaceAPI::TransUrl('PayPal',Gdn::Session()->UserID,$Product,$TransactionID,'cancel'); ?>">
        <input type="hidden" name="cbt" value="<?php echo T('Return to Account') ?>">
        <input type="hidden" name="rm" value="1">
        <?php
            echo '<p class="PriceDesc">'.sprintf(T('%01.2f %s','<span class="Price">%01.2f</span> <span class="Currency">%s</span>'),$Price*$Quantity,$Currency).'</p>';
        ?>
        <input type="image" src="<?php echo SmartAsset('/plugins/MarketPlace/design/buynow.gif',true); ?>" style="padding: 5px 10px; border-bottom-width: 1px; height: 36px;" class="Button" border="0" name="submit" alt="PayPal -- The safer, easier way to pay online.">
        </div>
        </form>
        <?php        
    }
    
    
    
    public static function PayPalNotify($Sender,$UserID,$Product,$TransactionID){
        $PaypalPayment = new PayPalPayment();
        return $PaypalPayment->CheckIPN($UserID,$Product,$TransactionID);
    }
}

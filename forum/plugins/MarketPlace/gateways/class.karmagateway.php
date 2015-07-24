<?php if (!defined('APPLICATION')) exit();
class KarmaGateway{
    public static function KarmaButton($Sender,$TransactionID, $Product,$Currency,$Price){
        echo $Sender->Form->Open(array('action'=>MarketPlaceAPI::TransUrl('Karma',Gdn::Session()->UserID,$Product,$TransactionID)));
        $VariableMeta = MarketPlaceAPI::VariableMeta($Sender,$Product,'Karma');
        $Subscription = GetValue('Subscription', $Product->Meta)==T('On');
        $SubscriptionPeriod = Getvalue('Period', $Product->Meta);
        $TransMeta = MarketTransactionModel::GetTransactionMeta($TransactionID);
        $Quantity=1;
        foreach($VariableMeta['Fields'] As $FieldN=>$FieldV){
            $FieldV = GetValue($FieldN,$TransMeta,$FieldV);
            $Sender->Form->AddHidden($FieldN,$FieldV);
            if($FieldN=='Quantity'){
                $Quantity=$FieldV;
                echo $Sender->Form->Hidden($FieldN,array('value'=>$FieldV,'class'=>'Quantity'));
            }else{
                echo $Sender->Form->Hidden($FieldN,array('value'=>$FieldV));
            }
        }
        if($Subscription){
            echo '<p class="PriceDesc">'.sprintf(T('%01.2fKarmas','<span class="Price">%01.2f</span> <span class="Currency">Karma</span> <span class="Recurring">(Recurring)</span>'),$Price*$Quantity).'</p>';
        }else{
            echo '<p class="PriceDesc">'.sprintf(T('%01.2f Karma','<span class="Price">%01.2f</span> <span class="Currency">Karma</span>'),$Price*$Quantity).'</p>';
        }
        echo $Sender->Form->Button(T('Spend Karma'),array('id'=>'Karma','name'=>'Karma'));
        echo $Sender->Form->Close();
    }
    
    public static function KarmaProcess($Sender,$UserID,$Product,$TransactionID){
        $PriceDenom=Gdn_Format::Unserialize($Product->PriceDenominations);
        $Price = GetValue('Karma',$PriceDenom);
        if(!$Price) return array('status'=>'error','errormsg'=>T('You can\'t use this payment method.'));
        if($UserID != Gdn::Session()->UserID)
            return array('status'=>'error','errormsg'=>T('User mismatch'));
        if(!$UserID) return array('status'=>'denied');
        $Quantity = MarketPlaceAPI::GetQuantity($TransactionID);
        $KarmaBank=new KarmaBankModel($UserID);
        $Balance = $KarmaBank->GetBalance();
        $Price = $Quantity*$Price;
        $TransactionLog = new MarketTransactionModel();
        $Meta = Gdn_Format::Unserialize($Product->Meta);
        $Subscription = GetValue('Subscription', $Meta)==T('On');
        $SubscriptionPeriod = trim(GetValue('Period', $Meta));
        if($Subscription){
            $MarketSubscriptionsModel = new MarketSubscriptionsModel();
            $UserSubscription = $MarketSubscriptionsModel->GetByUser($UserID);
            $TransactionMeta = MarketTransactionModel::GetTransactionMeta($TransactionID);
            $Renew = GetValueR('SubscriptionData.'.$TransactionID,$UserSubscription) && $UserSubscription->Gateway=='Karma';
            $OldTransaction = current(GetValue('SubscriptionData',$UserSubscription));
            $Overwrite = FALSE;
            if($OldTransaction['Status']=='Canceled'){
                $Overwrite = TRUE;
                $UserSubscription->SubscriptionData = NULL;
            }
            if(empty($UserSubscription) || $Renew || $Overwrite){
                    $SubscriptionData = $UserSubscription->SubscriptionData ? $UserSubscription->SubscriptionData : array();
                    $SubscriptionData[$TransactionID]['Meta']=$TransactionMeta;
                    $SubscriptionData[$TransactionID]['Item']=$Product->Slug;
                    $SubscriptionData[$TransactionID]['Price']=$Price;
                    $SubscriptionData[$TransactionID]['Currency']='Karma';
                    //$SubscriptionData[$TransactionID]['PeriodExpire']=Gdn_Format::ToDateTime(strtotime('+'.$SubscriptionPeriod));
                    $Quantity = GetValue('Quantity',$TransactionMeta,1);
                    if($Renew || $Overwrite){
                        $SubscriptionID = $UserSubscription->SubscriptionID;
                    }else{
                        $SubscriptionID = MarketPlaceAPI::GenerateID();
                    }
                    if($Balance->Balance>=$Price){
                        $Type=$User->Name.' Subscribes+to '.($Quantity>1?$Quantity.' ':'').$Product->Name.' ('.($SubscriptionPeriod).' recurring) in '.C('Plugins.MarketPlace.StoreName','Store');
                        if(!$KarmaBank->CheckForCollissions($Type,-$Price,-$Price)){
                            $GatewayTransactionID=$KarmaBank->Transaction($Type,-$Price,-$Price);
                            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Karma', $PlanID,'payment_complete', 'payment_complete:'.'payment_complete', 'payment_complete:'. implode('|',array('Price->'.$Price,'Currency->Karma')));    
                            $SubscriptionData[$TransactionID]['Status'] = 'Active';
                            $MarketSubscriptionsModel->Set($UserID,$SubscriptionID,'Karma',$SubscriptionPeriod,$SubscriptionData);
                        }else{
                            return array('status'=>'error','errormsg'=>T('Transaction is locked to prevent duplicates'));
                        }
                        
                    }else{
                        if($Renew){
                            $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Karma', $PlanID,'payment_failed', 'payment_failed: Not enought funds');
                            $SubscriptionID[$TransactionID]['Status'] = 'Failed';
                            $MarketSubscriptionsModel->Set($UserID,$SubscriptionID,'Karma','0 months',$SubscriptionData);
                        }
                        return array('status'=>'error','errormsg'=>T('You do not have enough funds pay for the subscription.'));
                    }
                    return array('status'=>'success');
            }else{                
                return array('status'=>'error', 'errormsg'=>T('Only a single supscription plan per user is currently supported.'));
            }
        }
        if($Balance->Balance>=$Price){
                $Type=$User->Name.' Buys '.($Quantity>1?$Quantity.' ':'').$Product->Name.' in '.C('Plugins.MarketPlace.StoreName','Store');
                if(!$KarmaBank->CheckForCollissions($Type,-$Price,-$Price)){
                    $GatewayTransactionID=$KarmaBank->Transaction($Type,-$Price,-$Price);
                    $TransactionLog->Log($UserID,$Product->Slug,$TransactionID, 'Karma', $GatewayTransactionID,'payment_complete', 'payment_complete:'. implode('|',array('Price->'.$Price,'Currency->Karma')));
                }else{
                    return array('status'=>'error','errormsg'=>T('Transaction is locked to prevent duplicates'));
                }
            return array('status'=>'success');
        }else{
            return array('status'=>'error','errormsg'=>T('You do not have enough funds to complete the transaction.'));
        }
    }
    
    public static function KarmaSubscription($Sender){
        $Opperation = strtolower(GetValue(2,$Sender->RequestArgs));
        $PlanID = GetValue(3,$Sender->RequestArgs);
        $TransactionID = GetValue(4,$Sender->RequestArgs);
        $MarketSubscriptionsModel = new MarketSubscriptionsModel();
        $UserSubscription = $MarketSubscriptionsModel->GetByID($PlanID);
        $SubscriptionData = GetValueR('SubscriptionData.'.$TransactionID,$UserSubscription);
        if($UserSubscription){
            if($Opperation == 'cancel'){
                $UserSubscription->SubscriptionData[$TransactionID]['Status']='Canceled';
                $MarketSubscriptionsModel->Set($UserSubscription->UserID,$PlanID,'Karma','0 months',$UserSubscription->SubscriptionData);
            }else if($Opperation == 'renew'){
                if($UserSubscription->SubscriptionData[$TransactionID]['Status']!='Canceled'){
                    $UserSubscription->SubscriptionData[$TransactionID]['Status']='Canceled';
                    $MarketSubscriptionsModel->Set($UserSubscription->UserID,$PlanID,'Karma','0 months',$UserSubscription->SubscriptionData);
                }
                $MarketProductModel = new MarketProductModel();
                $Product = $MarketProductModel->GetBySlug($SubscriptionData['Item']);
                return $Sender->MarketPlace->ProcessTrans($Sender,$UserSubscription->UserID,$Product,$TransactionID,'KarmaGateway::KarmaProcess');
            }
        }
        Redirect('/profile/subscriptions');
    }
    
    public static function KarmaSubscriptionCheck($MarketPlace){
        $HasSubscription = FALSE;
        foreach($MarketPlace->ProductTypes As $Product){
            if(GetValueR('Options.Subscription',$Product)){
                $HasSubscription = TRUE;
                break;
            }
        }
        
        if(!$HasSubscription) return;
            
        $MarketSubscriptionsModel = new MarketSubscriptionsModel();
        $ExpiredSubscriptions = $MarketSubscriptionsModel->GetExpired('Karma');
        if(!$ExpiredSubscriptions) return;
        $ProductSlugs=array();
        foreach($ExpiredSubscriptions->SubscriptionData As $TransactionID => $SubscriptionLine){
            $ProductSlugs[]=$SubscriptionLine['Item'];
        }
        $MarketProductModel = new MarketProductModel();
        $Products = $MarketProductModel->GetBySlugs($ProductSlugs);
        foreach($ExpiredSubscriptions->SubscriptionData As $TransactionID => $SubscriptionLine){
            foreach($Products As $Product){
                if($SubscriptionLine['Item']==$Product->Slug && $SubscriptionLine['Status']=='Active'){
                    return $MarketPlace->ProcessTrans($Sender,$ExpiredSubscriptions->UserID,$Product,$TransactionID,'KarmaGateway::KarmaProcess');
                }
            }
        }
    }
    
    public static function KarmaLog($Sender,$KarmaTransactionID){
        $Sender->SetData('Trans',KarmaBankModel::GetTransaction($KarmaTransactionID));
        $Sender->View=$Sender->MarketPlace->Plgn->Utility()->ThemeView('karmalog');
        $Sender->Render();
    }
}

<?php if (!defined('APPLICATION')) exit();
class StripeGateway{
 
    public static function StripeButton($Sender,$TransactionID,$Product,$Currency,$Price){
        $Url= Url(C('Plugins.MarketPlace.StoreURI').'/stripe');
        $VariableMeta = MarketPlaceAPI::VariableMeta($Sender,$Product,'Stripe');
        $Subscription = GetValue('Subscription', $Product->Meta)==T('On');
        $SubscriptionPeriod = Getvalue('Period', $Product->Meta);
        echo $Sender->Form->Open(array('action'=>$Url));
        $Sender->Form->AddHidden('TransactionID',$TransactionID);
        echo $Sender->Form->Hidden('TransactionID',array('value'=>$TransactionID));
        
        $CurrencyFormatted = GetValue($Currency,$Sender->MarketPlace->Plgn->API()->Gateways['Stripe']['Currencies'],'usd');
        
        $Sender->Form->AddHidden('Currency',$CurrencyFormatted);
        echo $Sender->Form->Hidden('Currency',array('value'=>$CurrencyFormatted));
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
        $Sender->Form->AddHidden('ProductSlug',Gdn_Format::Text($Product->Slug));
        echo $Sender->Form->Hidden('ProductSlug',array('value'=>Gdn_Format::Text($Product->Slug)));
        if($Subscription){
            $Sender->Form->AddHidden('SubscriptionPeriod',Gdn_Format::Text($SubscriptionPeriod));
            echo $Sender->Form->Hidden('SubscriptionPeriod',array('value'=>Gdn_Format::Text($SubscriptionPeriod)));
            $Sender->Form->AddHidden('Subscription',1);
            echo $Sender->Form->Hidden('Subscription',array('value'=>1));
        }
        if($Subscription){
            echo '<p class="PriceDesc">'.sprintf(T('%01.2f %s','<span class="Price">%01.2f</span> <span class="Currency">%s</span> <span class="Recurring">(Recurring)</span>'),$Price*$Quantity,$Currency).'</p>';
        }else{
            echo '<p class="PriceDesc">'.sprintf(T('%01.2f %s','<span class="Price">%01.2f</span> <span class="Currency">%s</span>'),$Price*$Quantity,$Currency).'</p>';
        }
        echo $Sender->Form->Button(T('Stripe Payments'),array('id'=>'Stripe','name'=>'Stripe','type'=>'image','style'=>'padding: 2px 5px; border-bottom-width: 1px; height: 42px;', 'src'=>SmartAsset('/plugins/MarketPlace/design/stripe.png',true)));
        echo $Sender->Form->Close();
    }

    public static function StripeProcess($Sender,$UserID,$Product,$TransactionID){
        Stripe::setApiKey(C('Plugins.MarketPlace.Gateway.Stripe.PrivateKey'));
        $PriceDenom = Gdn_Format::Unserialize($Product->PriceDenominations);
        $Quantity = MarketPlaceAPI::GetQuantity($TransactionID);
        $Currency = GetValue('Currency',$_POST);
        $Price = floatval(GetValue(GetValue($Currency,array_flip($Sender->MarketPlace->Plgn->API()->Gateways['Stripe']['Currencies']),'USD'),$PriceDenom))*$Quantity;
        $TransactionLog = new MarketTransactionModel();
        if(GetValue('stripeToken',$_POST)){
            $Meta = Gdn_Format::Unserialize($Product->Meta);
            $Subscription = GetValue('Subscription', $Meta)==T('On');
            $SubscriptionPeriod = trim(GetValue('Period', $Meta));

            try{
                if($Subscription){
                    $MarketSubscriptionsModel = new MarketSubscriptionsModel();
                    $UserSubscription = $MarketSubscriptionsModel->GetByUser($UserID);
                    $TransactionMeta = MarketTransactionModel::GetTransactionMeta($TransactionID);
                    $Renew = GetValueR('SubscriptionData.'.$TransactionID,$UserSubscription) && $UserSubscription->Gateway=='Stripe';
                    $User = Gdn::UserModel()->GetID($UserID);
                    $OldTransaction = current(GetValue('SubscriptionData',$UserSubscription));
                    if($OldTransaction['Status']=='Canceled'){                        
                        $Overwrite = TRUE;
                        $UserSubscription->SubscriptionData = NULL;
                    }
                    if(empty($UserSubscription) || $Renew || $Overwrite){
                        $SubscriptionData = $UserSubscription->SubscriptionData ? $UserSubscription->SubscriptionData : array();
                        $SubscriptionData[$TransactionID]['Meta']=$TransactionMeta;
                        $SubscriptionData[$TransactionID]['Item']=$Product->Slug;
                        $SubscriptionData[$TransactionID]['Price']=$Price;
                        $SubscriptionData[$TransactionID]['Currency']=GetValue($Currency,array_flip($Sender->MarketPlace->Plgn->API()->Gateways['Stripe']['Currencies']),'USD');
                        //$SubscriptionData[$TransactionID]['PeriodExpire']=Gdn_Format::ToDateTime(strtotime('+'.$SubscriptionPeriod));
                        $Quantity = GetValue('Quantity',$TransactionMeta,1);
                        if($Renew || $Overwrite){
                            $SubscriptionID = $UserSubscription->SubscriptionID;
                        }else{
                            $SubscriptionID = MarketPlaceAPI::GenerateID();
                        }
                        
                        if($SubscriptionData[$TransactionID]['CustomerID']){
                            try{
                                $Customer = Stripe_Customer::retrieve($SubscriptionData[$TransactionID]['CustomerID']);                                 
                            }catch(Stripe_Error $e){
        
                            }
                            if($Customer->deleted){
                                $Customer = Stripe_Customer::create(array(
                                        "card" => GetValue('stripeToken',$_POST),
                                        "email" => $User->Email
                                    )
                                );
                            }
                        }else{                        
                            $Customer = Stripe_Customer::create(array(
                                    "card" => GetValue('stripeToken',$_POST),
                                    "email" => $User->Email
                                )
                            );
                        }
                        $SubscriptionData[$TransactionID]['CustomerID']=$Customer->id;
                        try{
                            Stripe_Plan::create(array(
                                    "amount" => $Price*100,
                                    "interval" => "month",
                                    "name" => "{$_SERVER["HTTP_HOST"]} Subscription:".$Product->Slug."x{$Quantity} ({$SubscriptionPeriod})",
                                    "currency" => $Currency,
                                    "id" => $SubscriptionID
                                )
                            );
                            
                        }catch(Stripe_Error $e){
                            var_dump($e->getMessage());
                        }
                            $Customer->updateSubscription(array(
                                    "plan" => $SubscriptionID
                                )
                            );
                        $SubscriptionData[$TransactionID]['Status']='Pending';
                        $MarketSubscriptionsModel->Set($UserID,$SubscriptionID,'Stripe',$SubscriptionPeriod,$SubscriptionData);
                    }else{                
                        return array('status'=>'error', 'errormsg'=>T('Only a single supscription plan per user is currently supported.'));
                    }
                    $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $SubscriptionID,'subscription_pending', 'subscription_pending:'.$Product->Slug."x{$Quantity} ({$SubscriptionPeriod})");    
                    return array('status'=>'pending');
                }else{
                    
                    $Params = array(
                        "amount" => intval(floatval($Price)*100),
                        "currency" => $Currency,
                        "card" => GetValue('stripeToken',$_POST),
                        "description" => $Product->Slug.'x'.$Quantity
                    );
                    $Charge = Stripe_Charge::create($Params);
                }
            }catch(Stripe_Error $e){
                if(stripos($e->getMessage(),'You cannot use a stripe token more than once')!==FALSE)
                    return array('status'=>'error','errormsg'=>T('You may stripe thave refreshed the page, you can\'t submit the same transaction twice'));
                return array('status'=>'error','errormsg'=>T($e->getMessage()));
            }
            $ChargeObj = $Charge;
            $Charge= $Charge->__toArray();
            unset($Params['card']);
            //$TransactionLog = new MarketTransactionModel();
            $GatewayTransactionID=$Charge['id'];
            $Log=$TransactionLog->GetLatest($UserID,$Product->Slug,$TransactionID,$GatewayTransactionID);
            if($Log && $Log->Status=='payment_complete'){
                //$User = Gdn::UserModel()->GetID($UserID);
                
                StripeEmail::PaymentReicept($User->Email,$ChargeObj);
                return array('status'=>'complete');
            }
            if(GetValue("refunded",$Charge)!=false){
                return array('status'=>'error','errormsg'=>T('The payment was refunded'));
            }
            if(GetValue("failure_message",$Charge)!=null){
                return array('status'=>'error','errormsg'=>T(GetValue("failure_message",$Charge)));
            }
            if(GetValue("disputed",$Charge)!=false){
                return array('status'=>'error','errormsg'=>T('The payment is being disputed'));
            }
            if(GetValue("paid",$Charge)!=true){
                return array('status'=>'error','errormsg'=>T('Payment is not complete'));
            }
            $Mismatch =array();
            $PaymentComp= array();
            foreach ($Params As $ParamI =>$ParamV){
                if(trim(GetValue($ParamI,$Charge))!=trim($ParamV)){
                    $Mismatch[$ParamI]=$ParamI.'->'.trim($Charge[$ParamI]).' doesn\'t match '.$ParamV;
                }
            }
            foreach($Charge as $PaymentI =>$PaymentV)
                    $PaymentComp[$PaymentI]=$PaymentI.'->'.(is_array($PaymentV) || is_object($PaymentV) ? serialize($PaymentV) : $PaymentV);
            

            if(empty($Mismatch)){//Completed
                $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $GatewayTransactionID,'payment_complete', 'payment_complete:'. implode('|',$PaymentComp));    
                return array('status'=>'success');
            }else{
                $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $GatewayTransactionID,'payment_invalid', 'payment_mismatch:'.implode('|',$Mismatch));
                return array('status'=>'error','errormsg'=>T('The payment is invalid'.implode('|',$Mismatch)));
            }
                
        }
    }
    
    public static function StripeWebhooks($Sender){
        Stripe::setApiKey(C('Plugins.MarketPlace.Gateway.Stripe.PrivateKey'));
        $Body = @file_get_contents('php://input');
        //LogMessage(__FILE__,__LINE__,'MarketPlace','StripeWebhooks', $Body);
        
        $WebHooksObj = json_decode($Body);
        if($WebHooksObj->type=='plan.deleted' || $WebHooksObj->type=='customer.subscription.deleted'){
            return;
        }else if($WebHooksObj->type=='invoice.created' || $WebHooksObj->type=='invoice.payment_succeeded' || $WebHooksObj->type=='invoice.payment_failed'){
            $Invoice = $WebHooksObj->data->object;
            $Plan = $Invoice->lines->subscriptions[0]->plan;
            $PlanID = $Plan->id;
            $CustomerID = $Invoice->customer;
            $InvoiceID = $Invoice->id;
        }else{
            return;
        }
        
        $MarketSubscriptionsModel = new MarketSubscriptionsModel();
        $TransactionLog = new MarketTransactionModel();
        $UserSubscription = $MarketSubscriptionsModel->GetByID($PlanID);
        if(!$UserSubscription)
            return;
        $UserID = $UserSubscription->UserID;
        $User = Gdn::UserModel()->GetID($UserID);
        if($WebHooksObj->type=='invoice.created'){
            StripeEmail::InvoicePaymentImminent($User->Email,$Invoice);
            return;
        }
        $SubscriptionData = $UserSubscription->SubscriptionData;
        $MarketProductModel = new MarketProductModel();
        $Slugs=array();
        foreach($SubscriptionData As $SubscriptionLine)
            $Slugs[]=$SubscriptionLine['Item'];
        $Products=$MarketProductModel->GetBySlugs($Slugs);
        $ProductList =array();
        foreach($Products As $Product){
            $ProductList[$Product->Slug] = $Product;
        }
        
        if($WebHooksObj->type=='invoice.payment_succeeded'){
            
            foreach($SubscriptionData As $TransactionID => $SubscriptionLine){
                
                $Product = $ProductList[$SubscriptionLine['Item']];
                $Meta=Gdn_Format::Unserialize($Product->Meta);
                if(GetValue('Subscription',$Meta)!=T('On'))
                    continue;
                
                $PriceDenom = Gdn_Format::Unserialize($Product->PriceDenominations);
                $Quantity = MarketPlaceAPI::GetQuantity($TransactionID);
                $Cuurency = $SubscriptionLine['Currency'];
                $Price = floatval(GetValue($Cuurency,$PriceDenom))*$Quantity;
                $SubscriptionPeriod = trim(Getvalue('Period',$Meta));
                $Params = array(
                    "amount" => $Price*100,
                    "currency" => GetValue($Currency,$Sender->MarketPlace->Plgn->API()->Gateways['Stripe']['Currencies'],'usd'),
                    "interval" => "month",
                    "name" => "{$_SERVER["HTTP_HOST"]} Subscription:".$Product->Slug."x{$Quantity} ({$SubscriptionPeriod})"
                );
                $Mismatch =array();
                $PaymentComp= array();
                foreach ($Params As $ParamI =>$ParamV){
                    if(trim(GetValue($ParamI,$Plan))!=trim($ParamV)){
                        $Mismatch[$ParamI]=$ParamI.'->'.trim(GetValue($ParamI,$Plan)).' doesn\'t match '.$ParamV;
                    }
                }
                
                foreach($Invoice as $PaymentI =>$PaymentV)
                        $PaymentComp[$PaymentI]=$PaymentI.'->'.trim($PaymentV);
            
                
                
                if($SubscriptionPeriod!='1 month')
                    $Mismatch['period']='Period->'.$SubscriptionPeriod.' doesn\'t match 1 month';
                
                if(empty($Mismatch)){//Completed

                    $Payload = $Sender->MarketPlace->Plgn->API()->ProductTypes[$Product->ProductType]['Callback'];
                    
                    if($Payload){
                        $Complete = call_user_func($Payload,$UserID,$Product,$TransactionID);
                    }
                    
                    $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $PlanID,'payment_complete', 'payment_complete:'. implode('|',$PaymentComp));    
                    $SubscriptionData[$TransactionID]['Status'] = 'Active';
                    try{
                        StripeEmail::InvoicePaymentReicept($User->Email,$Invoice);
                    }catch(Stripe_Error $e){
                        
                    }
                    
                    
                    
                }else{
                    $SubscriptionData[$TransactionID]['Status'] = 'Failed';
                    try{
                        StripeEmail::InvoicePaymentFailed($User->Email,$Invoice);
                    }catch(Stripe_Error $e){
                        
                    }
                    $SubscriptionPeriod='0 months';
                    $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $PlanID,'payment_invalid', 'payment_mismatch:'.implode('|',$Mismatch));
                    
                }

            }
            
            $MarketSubscriptionsModel->Set($UserID,$PlanID,'Stripe',$SubscriptionPeriod,$SubscriptionData);
        }else if($WebHooksObj->type=='invoice.payment_failed'){
            
            foreach($SubscriptionData As $TransactionID => $SubscriptionLine){
                $Product = $ProductList[$SubscriptionLine['Item']];
                $Meta=Gdn_Format::Unserialize($Product->Meta);
                
                if(GetValue('Subscription',$Meta)!=T('On'))
                    continue;
                
                $SubscriptionPeriod = Getvalue('Period',$Meta);

                $PaymentFail=array();
                foreach($Invoice as $PaymentI =>$PaymentV)
                        $PaymentFail[$PaymentI]=$PaymentI.'->'.trim($PaymentV);
                    
                $TransactionLog->Log($UserID,$Product->Slug, $TransactionID, 'Stripe', $PlanID,'payment_failed', 'payment_failed:'.implode('|',$PaymentFail));
                $Payload = GetValueR('Options.SubscriptionCancelCallback',$Sender->MarketPlace->Plgn->API()->ProductTypes[$Product->ProductType]);
                try{
                    StripeEmail::InvoicePaymentFailed($User->Email,$Invoice);
                }catch(Stripe_Error $e){
                    
                }
                $SubscriptionData[$TransactionID]['Status'] = 'Failed';
                if($Payload){
                    $Failed = call_user_func($Payload,$UserID,$Product,$TransactionID);
                }
                
            }
            
            $MarketSubscriptionsModel->Set($UserID,$PlanID,'Stripe','0 months',$SubscriptionData);
        }
    }
    
    public static function StripeSubscription($Sender){
        $Opperation = strtolower(GetValue(2,$Sender->RequestArgs));
        $PlanID = GetValue(3,$Sender->RequestArgs);
        $TransactionID = GetValue(4,$Sender->RequestArgs);
        Stripe::setApiKey(C('Plugins.MarketPlace.Gateway.Stripe.PrivateKey'));
        $MarketSubscriptionsModel = new MarketSubscriptionsModel();
        $UserSubscription = $MarketSubscriptionsModel->GetByID($PlanID);
        $SubscriptionData = GetValueR('SubscriptionData.'.$TransactionID,$UserSubscription);
        if($UserSubscription){
            if($Opperation == 'cancel'){
                if($UserSubscription->SubscriptionData[$TransactionID]['Status']!='Canceled'){
                    try{
                        $Plan = Stripe_Plan::retrieve($PlanID);
                        $Plan->delete();
                        $Customer = Stripe_Customer::retrieve($SubscriptionData['CustomerID']);
                        $Customer->cancelSubscription();
                    }catch(Stripe_Error $e){
                    }
                }
                $UserSubscription->SubscriptionData[$TransactionID]['Status']='Canceled';
                $MarketSubscriptionsModel->Set($UserSubscription->UserID,$PlanID,'Stripe','0 months',$UserSubscription->SubscriptionData);
                Redirect('/profile/subscriptions');
            }else if($Opperation == 'renew'){
                if($UserSubscription->SubscriptionData[$TransactionID]['Status']!='Canceled'){
                    try{
                        $Plan = Stripe_Plan::retrieve($PlanID);
                        $Plan->delete();
                        $Customer = Stripe_Customer::retrieve($SubscriptionData['CustomerID']);
                        $Customer->cancelSubscription();
                    }catch(Stripe_Error $e){
                    }
                    $UserSubscription->SubscriptionData[$TransactionID]['Status']='Canceled';
                    $MarketSubscriptionsModel->Set($UserSubscription->UserID,$PlanID,'Stripe','0 months',$UserSubscription->SubscriptionData);
                }
                $MarketProductModel = new MarketProductModel();
                $Product = $MarketProductModel->GetBySlug($SubscriptionData['Item']);
                $Quantity = GetValueR('Meta.Quantity',$SubscriptionData,1);
                $Period = trim(GetValueR('Meta.Period',$SubscriptionData,'1 month'));
                $Currency = GetValueR('Currency',$SubscriptionData,'USD');
                $CurrencyFormatted = GetValue($Currency,$Sender->MarketPlace->Plgn->API()->Gateways['Stripe']['Currencies'],'usd');
                $Sender->Form->FormValues(array('Currency'=>$CurrencyFormatted , 'Quantity'=>$Quantity, 'ProductSlug'=>$SubscriptionData['Item'],'TransactionID'=>$TransactionID,'Subscription'=>1,'SubscriptionPeriod'=>$Period,'TransientKey'=>Gdn::Session()->TransientKey()));
                Gdn::Request()->RequestMethod('post');
                $Sender->MarketPlace->Plgn->UI()->Controller_Stripe($Sender);
            }
        }
    }
    
}

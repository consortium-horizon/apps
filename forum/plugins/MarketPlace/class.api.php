<?php if (!defined('APPLICATION')) exit();
abstract class MarketPlaceAPIDomain extends MarketPlaceUtilityDomain {
  
    private $WorkerName = 'API';

    public function CalledFrom(){
        return $this->WorkerName;
    }

    public function API(){
        $WorkerName = $this->WorkerName;
        if(!GetValue($WorkerName, $this->Workers)){
            $WorkerClass = $this->GetPluginIndex().$WorkerName;
            $this->LinkWorker($WorkerName,$WorkerClass);
        }
        return $this->Workers[$WorkerName];
    }

    /* backwards compatibility */
    public function RegisterProductType($Name,$Description,$Options,$PreCondCallback,$PayloadCallback){
        $this->API()->RegisterProductType($Name,$Description,$Options,$PreCondCallback,$PayloadCallback);
    }

    public function RegisterPaymentGateway($Name,$Currencies,$FormCallback,$NotifyCallback,$Options){
        $this->API()->RegisterPaymentGateway($Name,$Currencies,$FormCallback,$NotifyCallback,$Options);
    }
}

class MarketPlaceAPI {
  

    public $ProductTypes=array();
    public $Gateways=array();
    public $Currencies=array();
    
    public function Init(){ 
        
        //MarketTransactionModel::PurgeTransactionMeta();
        
        $Options=array(
            'VariableMeta'=>array('Quantity'=>'Quantity'),
            'LogCallback'=>'KarmaGateway::KarmaLog',
            'SubscriptionCallback' => 'KarmaGateway::KarmaSubscription',
            'CronCallback' => 'KarmaGateway::KarmaSubscriptionCheck',
            'Subscription' => TRUE
        );
        $this->RegisterPaymentGateway('Karma',array('Karma'=>'Karma'),'KarmaGateway::KarmaButton','KarmaGateway::KarmaProcess',$Options);
        
        if(C('Plugins.PremiumAccounts.PayPalAccount') || C('Plugins.MarketPlace.Gateway.PayPal')){
            $Options = array(
                'VariableMeta'=>array('Quantity'=>'quantity'),
                'ChargeLogURL'=>'https://www.'.(C('Plugins.MarketPlace.Gateway.PayPal.Account')!=='Live'?'sandbox.':'').'paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s'
            );
            $CurrencyList=array('AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','SGD','SEK','CHF','TWD','THB','TRY','USD');
            $this->RegisterPaymentGateway('PayPal',array_combine($CurrencyList,$CurrencyList),'PayPalGateway::PayPalButton','PayPalGateway::PayPalNotify',$Options);
        }
        
        if(C('Plugins.MarketPlace.Gateway.Stripe')){
            $Options = array(
                'VariableMeta'=>array('Quantity'=>'Quantity'),
                'ChargeLogURL'=>'https://manage.stripe.com/#payments/%s',
                'WebhooksCallback' => 'StripeGateway::StripeWebhooks',
                'SubscriptionCallback' => 'StripeGateway::StripeSubscription',
                'Subscription' => TRUE
            );
            $this->RegisterPaymentGateway('Stripe',array('USD'=>'usd', 'GBP'=>'gbp', 'CAD'=> 'cad'),'StripeGateway::StripeButton','StripeGateway::StripeProcess',$Options);
        }
        
        
        $this->Plgn->FireEvent('LoadMarketPlace');
        
        $this->GatewayCron();

    }
    
    public function GatewayCron(){
        foreach($this->Gateways As $Gateway){
            $CronCallback = GetValueR('Options.CronCallback',$Gateway);
            if(!$CronCallback) continue;
            call_user_func($CronCallback,$this);
        }
    }
  
    
    public function RegisterPaymentGateway($Name,$Currencies,$FormCallback,$NotifyCallback,$Options=array()){
        if(GetValue($Name,array_change_key_case($this->Gateways))){
            throw Exception(sprintf(T('Gateway %s already exists'),$Name));
        }
        
        $this->Currencies=array_unique(array_merge($this->Currencies,array_keys($Currencies)));
        sort($this->Currencies);
        $this->Gateways[$Name]=array(
            'FormCallback'=>$FormCallback,
            'NotifyCallback'=>$NotifyCallback,
            'Currencies'=>$Currencies,
            'ChargeLogURL'=>GetValue('ChargeLogURL',$Options),
            'Options'=>$Options
        );
    }
    
    public function RegisterProductType($Name,$Description,$Options,$PreCondCallback,$PayloadCallback){
        if(GetValue('Subscription',$Options)){
            $Options['Meta'][]='Subscription';
            $Options['HideMeta'][]='Subscription';
            $Options['ValidateMeta']['Subscription']=array(T('Off'),T('On'));
        }
        $this->ProductTypes[$Name]=array(
            'Description'=>$Description,
            'Options'=>$Options,
            'Callback'=>$PayloadCallback,
            'PreCondCallback'=>$PreCondCallback
        );
    }
    
    //helper functions
    
    public function GenerateID(){
        return md5(uniqid());
    }
    
    public static function TransUrl($GatwayName, $UserID,$Product,$TransactionID, $Operation='notify'){
        $TransUrl= Url(C('Plugins.MarketPlace.StoreURI').'/'.strtolower($GatwayName).'trans/'.$UserID.'/'.Gdn_Format::Url(is_string($Product)?$Product:$Product->Slug),TRUE);
        return $Operation=='notify'? $TransUrl.'/'.$TransactionID:$TransUrl.'/'.$Operation.'/'.$TransactionID;
    }
    
    public static function VariableMeta($Sender,$Product,$Gateway){
        $Variable =array('Fields'=>array(),'Params'=>array());
        $GW = $Sender->MarketPlace->Plgn->API()->Gateways[$Gateway];
        foreach($Product->Meta As $MetaN => $MetaV){
            if(substr($MetaV,-4)=='/any'){
                if(GetValue('VariableMeta',$GW['Options']) && GetValue($MetaN,$GW['Options']['VariableMeta'])){
                    $Variable['Fields'][$MetaN]=substr($MetaV,0,-4);
                }else{
                    $Variable['Params'][$MetaN]=substr($MetaV,0,-4);
                }
            }    
        }
        return $Variable;
    }
    
    public static function GetQuantity($TransactionID, $Check = TRUE){
        $TransactionMeta=MarketTransactionModel::GetTransactionMeta($TransactionID);
        $Quantity = intval(GetValue('Quantity',$TransactionMeta));
        if($Check){
            $Form = new Gdn_Form();
            $FormValues = $Form->FormValues();
            $FormValues = empty($FormValues)?$_POST:$FormValues;
            return $Quantity && $Quantity==intval(GetValue('Quantity',$FormValues)) ? $Quantity :1;
        }else{
            return $Quantity;
        }
    }
    
    public static function GetQuantityRemote($TransactionID,$QuantityName='Quantity'){
        $TransactionMeta=MarketTransactionModel::GetTransactionMeta($TransactionID);
        $Quantity = intval(GetValue('Quantity',$TransactionMeta));
        return $Quantity && $Quantity==intval(GetValue($QuantityName,$_POST)) ? $Quantity :1;
    }
        
  
    public function ValidateMeta(&$FormValues,$ProductModel,$ProductType, $AnyCheck=FALSE){
        foreach($FormValues['MetaName'] As $MetaNameI=>$MetaName){
            if(!empty($MetaName) && $FormValues['MetaValue'][$MetaNameI]!=""){
                $FormValues['Meta'][$MetaName]=$FormValues['MetaValue'][$MetaNameI];
                if(array_key_exists($MetaName,$this->Plgn->API()->ProductTypes[$ProductType]['Options']['ValidateMeta'])){
                    $Validate = $this->Plgn->API()->ProductTypes[$ProductType]['Options']['ValidateMeta'][$MetaName];
                    if(is_array($Validate)){
                        if(!in_array($FormValues['MetaValue'][$MetaNameI],$Validate)){
                            $ProductModel->Validation->AddValidationResult('Meta'.$MetaName, sprintf(T('\'%s\' not a valid %s'),$FormValues['MetaValue'][$MetaNameI],$MetaName));
                        }
                        
                        $ValidationFunc='';
                    }else{
                        $ValidationFunc = 'Validate'.$Validate;
                    }
                    if($ValidationFunc && !$ValidationFunc($FormValues['MetaValue'][$MetaNameI],$ProductModel))
                        $ProductModel->Validation->AddValidationResult('Meta'.$MetaName, sprintf(T('\'%s\' not a valid %s'),$FormValues['MetaValue'][$MetaNameI],$MetaName));
                    
                }
                if($AnyCheck && $FormValues['MetaAny'][$MetaNameI] && in_array($MetaName,$this->Plgn->API()->ProductTypes[$ProductType]['Options']['VariableMeta'])){
                    $FormValues['Meta'][$MetaName].='/any';
                }
                
            }    
        }
    }
    
    
}

<?php if (!defined('APPLICATION')) exit();
abstract class MarketPlaceUIDomain extends MarketPlaceSettingsDomain {
  
    private $WorkerName = 'UI';

    public function CalledFrom(){
        return $this->WorkerName;
    }

    public function UI(){
        $WorkerName = $this->WorkerName;
        if(!GetValue($WorkerName, $this->Workers)){
            $WorkerClass = $this->GetPluginIndex().$WorkerName;
            $this->LinkWorker($WorkerName,$WorkerClass);
        }
        return $this->Workers[$WorkerName];
    }
    
}


class MarketPlaceUI {
  
    /*Marketplace Controllers*/
    
    public function MarketPlace_Controller($Sender, $Args) {
        if(substr(strtolower(GetValue(0,$Args)),-5)!=='trans')
            $Sender->Permission('Plugins.MarketPlace.UseStore');
        if(C('Plugins.MarketPlace.ForceSSL'))
            ForceSSL();
        $ProductURI = C('Plugins.MarketPlace.ProductURI','item');
        if(strtolower(GetValue(0,$Args))==$ProductURI)
            $Sender->RequestArgs[0]='product';
        if(strtolower($ProductURI)!='product' && strtolower(GetValue(0,$Args))=='product')
            throw NotFoundException();
        if(substr(strtolower(GetValue(0,$Args)),-5)=='trans'){
            $Sender->RequestArgs[0]='trans';
            $Sender->TransType = substr(strtolower(GetValue(0,$Args)),0,-5);
        }
        $Sender->AddCssFile('marketplace.css','plugins/MarketPlace');
        $this->Plgn->Utility()->MiniDispatcher($Sender);
    }
    

    
    public function Controller_Index($Sender){

        $Sender->Permission('Garden.SignIn.Allow');
        if(GetValue(0,$Sender->RequestArgs) && !preg_match('`^p[0-9]+$`i',strtolower(GetValue(0,$Sender->RequestArgs))))
            throw NotFoundException();
        $MarketProductModel = new MarketProductModel();
        list($Offset, $Limit) = OffsetLimit(array_key_exists(0,$Sender->RequestArgs)?$Sender->RequestArgs[0]:0,C('Plugins.MarketPlace.PageLimit',10));
        $Sender->Offset=$Offset;
        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
        $Sender->Pager->MoreCode = '>>';
        $Sender->Pager->LessCode = '<<';
        $Sender->Pager->ClientID = 'Pager';
        $Sender->Pager->Configure(
            $Sender->Offset,
            $Limit,
            $MarketProductModel->GetCount(),
            C('Plugins.MarketPlace.StoreURI','store').'/{Page}'
        );
        $Sender->MarketPlace=$this->Plgn->API();
        $Sender->SetData('MarketProducts',$MarketProductModel->GetSaleable($Limit,$Offset));
        $Sender->View = $this->Plgn->Utility()->ThemeView('store');
        $Sender->Render();
    }
    
    public function Controller_Product($Sender){
        $Sender->Permission('Garden.SignIn.Allow');
        $Slug = Gdn_Format::Url(GetValue(1,$Sender->RequestArgs));
        $Delete = Gdn_Format::Url(GetValue(2,$Sender->RequestArgs));
        $MarketProductModel = new MarketProductModel();
        $Product = $MarketProductModel->GetBySlug($Slug,array_keys($this->Plgn->API()->ProductTypes));
        if(!$Product)
            throw NotFoundException();
        $Product->PriceDenominations=Gdn_Format::Unserialize($Product->PriceDenominations);
        $Product->Meta=Gdn_Format::Unserialize($Product->Meta);
        $Product->EnabledGateways=Gdn_Format::Unserialize($Product->EnabledGateways);
        $Sender->SetData('MarketProduct',$Product);
        $Sender->MarketPlace=$this->Plgn->API();
        $Sender->AddJsFile('store.js','plugins/MarketPlace');
        $Sender->AddDefinition('ConfirmText',T('Do you wish to purchase this item?'));
        $Sender->AddDefinition('EditWord',T('Edit'));
        $Sender->AddDefinition('SaveWord',T('Save'));
        $TransactionID=$this->Plgn->API()->GenerateID();
        $Sender->SetData('TransactionID',$TransactionID);
        $Sender->AddDefinition('TransactionID',$TransactionID);
        $Sender->AddDefinition('MetaTrans',$this->Plgn->API()->TransUrl('', Gdn::Session()->UserID,$Product,'', 'meta'));
        $Sender->View = $this->Plgn->Utility()->ThemeView('product');
        $Sender->Render();
    }
    
    public function Controller_Type($Sender){
        $Sender->Permission('Garden.SignIn.Allow');
        if(!GetValue(1,$Sender->RequestArgs) || (GetValue(2,$Sender->RequestArgs) && !preg_match('`^p[0-9]+$`i',strtolower(GetValue(2,$Sender->RequestArgs)))))
            throw NotFoundException();
        $MarketProductModel = new MarketProductModel();
        list($Offset, $Limit) = OffsetLimit(array_key_exists(2,$Sender->RequestArgs)?$Sender->RequestArgs[2]:0,C('Plugins.MarketPlace.PageLimit',10));
        $Sender->Offset=$Offset;
        $Products = $MarketProductModel->Get($Limit,$Offset,GetValue(1,$Sender->RequestArgs));
        if(count($Products)==1){
            $Product = current($Products);
            Redirect(C('Plugins.MarketPlace.StoreURI','store').'/item/'.$Product->Slug);
        }
        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
        $Sender->Pager->MoreCode = '>>';
        $Sender->Pager->LessCode = '<<';
        $Sender->Pager->ClientID = 'Pager';
        $Sender->Pager->Configure(
            $Sender->Offset,
            $Limit,
            $MarketProductModel->GetCount(),
            C('Plugins.MarketPlace.StoreURI','store').'/{Page}'
        );
        $Sender->MarketPlace=$this->Plgn->API();
        $Sender->SetData('MarketProducts',$Products);
        $Sender->View = $this->Plgn->Utility()->ThemeView('store');
        $Sender->Render();
    }
    
    public function Controller_Trans($Sender){
        $TransactionID = GetValue(3,$Sender->RequestArgs);
        
        $Hook = GetValue(1,$Sender->RequestArgs);
        
        if($Hook == 'subscription'){
            $TransType=$Sender->TransType;
            $Gateway = GetValue($TransType,array_change_key_case($this->Plgn->API()->Gateways));
            $SubscriptionCallback = GetValueR('Options.SubscriptionCallback',$Gateway);
            if(!$SubscriptionCallback)
                return;
            $Sender->MarketPlace=$this->Plgn->API();
            call_user_func($SubscriptionCallback,$Sender);
        }else if($Hook == 'webhooks'){
            $TransType=$Sender->TransType;

            $Gateway = GetValue($TransType,array_change_key_case($this->Plgn->API()->Gateways));
            $WebhooksCallback = GetValueR('Options.WebhooksCallback',$Gateway);
            if(!$WebhooksCallback)
                return;
            $Sender->MarketPlace=$this->Plgn->API();
            call_user_func($WebhooksCallback,$Sender);
            
        }else if(isset($_POST) && $TransactionID && preg_match('`^[a-fA-F\d]{32}$`',$TransactionID)){
            $UserID = GetValue(1,$Sender->RequestArgs);
            $Slug = GetValue(2,$Sender->RequestArgs);
            $TransType=$Sender->TransType;

            $Gateway = GetValue($TransType,array_change_key_case($this->Plgn->API()->Gateways));
            
            if(!$Gateway){
                $this->TransError($Sender,array('status'=>'error','errormsg'=>T('This payment method could not be found')));
                return;
            };
            $NotifyCallback = $Gateway['NotifyCallback'];
            $MarketProductModel = new MarketProductModel();
            $Product = $MarketProductModel->GetBySlug($Slug);             
            $Sender->MarketPlace=$this->Plgn->API();
            return $this->ProcessTrans($Sender,$UserID,$Product,$TransactionID,$NotifyCallback);
        }else{
            $Message='';
            $UserID = GetValue(1,$Sender->RequestArgs);
            $Slug = GetValue(2,$Sender->RequestArgs);
            $Operation=strtolower(GetValue(3,$Sender->RequestArgs));
            $TransactionID=GetValue(4,$Sender->RequestArgs);
            $MarketProductModel = new MarketProductModel();
            $Product = $MarketProductModel->GetBySlug($Slug);             
            switch($Operation){
                case 'cancel':
                    $Message=T('The transaction was cancelled');
                    break;
                case 'complete':
                    $TransactionLog = new MarketTransactionModel();
                    $Log=$TransactionLog->GetLatest($UserID,$Product->Slug,$TransactionID);

                    if($Log && $Log->Status=='payment_complete'){
                        $Message=T('Thank you for you payment, your product or service should be delivered shortly, if not already available.');
                    }else{
                        $Message=T('Thank you for you payment, we await the payment clearing');
                    }
                    break;
                    
                case 'meta':
                    $this->SetTransMeta($Sender);
                    exit;
                default:
                    if(strtolower(GetValue(1,$Sender->RequestArgs))=='log')
                        $this->GetLog($Sender,$Sender->TransType,GetValue(2,$Sender->RequestArgs));
                    else
                        $this->TransError($Sender,array('status'=>'error','errormsg'=>T('Invalid transaction')));
                    exit;
            }
            $Sender->SetData('TransMessage',$Message);
            $Sender->View = $this->Plgn->Utility()->ThemeView('trans');
            $Sender->Render();
        }
    }
    
    public function ProcessTrans($Sender,$UserID,$Product,$TransactionID,$NotifyCallback){
        $PreCondCallback=$this->Plgn->API()->ProductTypes[$Product->ProductType]['PreCondCallback'];
        if($PreCondCallback){
            $PreCond=call_user_func($PreCondCallback,$UserID,$Product);
            if($PreCond['status']!='pass'){
                $this->TransError($Sender,$PreCond);
                return;
            }
        }
        
        $Trans = call_user_func($NotifyCallback,$Sender,$UserID,$Product,$TransactionID);
        $ReturnComplete = GetValue('ReturnComplete',$this->Plgn->API()->ProductTypes[$Product->ProductType]['Options']);
        $ReturnSubscription = GetValue('ReturnSubscription',$this->Plgn->API()->ProductTypes[$Product->ProductType]['Options']);
        if($ReturnSubscription){
            $Args = Gdn::Dispatcher()->ControllerArguments();
            if(GetValue(2,$Args)=='renew' && GetValue(1,$Args)=='subscription'){
                $ReturnComplete = $ReturnSubscription;
            }else{
                $MarketSubscriptionsModel = new MarketSubscriptionsModel();
                $UserSubscription = $MarketSubscriptionsModel->GetByUser($UserID);
                $SubscriptionItem = GetValueR('SubscriptionData.'.$TransactionID,$UserSubscription);
                if($SubscriptionItem)
                    $ReturnComplete = $ReturnSubscription;
            }
        }
        if($Trans['status']=='success'){
            $Payload = $this->Plgn->API()->ProductTypes[$Product->ProductType]['Callback'];;
            $Complete = call_user_func($Payload,$UserID,$Product,$TransactionID);
            if($Complete['status']=='success'){
                if(GetValue('silent',$Complete))
                    exit;
                if($ReturnComplete)
                    Redirect($ReturnComplete);
            }else{
                if(GetValue('silent',$Complete))
                    exit;
                $this->TransError($Sender,$Complete);
                return;
            }
        }else if($Trans['status']=='pending'){
            if(GetValue('silent',$Complete))
                exit;
            if($ReturnComplete)
                Redirect($ReturnComplete);
        }else{
            $this->TransError($Sender,$Trans);
            return;
        }
    }
    
    public function TransError($Sender,$Trans){
        $Trans=(object)$Trans; 
        $Message='';
        switch($Trans->status){
            case 'error':
                $Message=$Trans->errormsg;
                break;
            case 'denied':
                throw PermissionException();
                break;
            default:
                $Message=T('An unknown error has occurred');
                break;
        }
        $Sender->SetData('TransError',$Message);
        $Sender->View = $this->Plgn->Utility()->ThemeView('transerror');
        $Sender->Render();
    }
    
    public function GetLog($Sender,$TransType,$TransactionID){
        $Gateway = GetValue($TransType,array_change_key_case($this->Plgn->API()->Gateways));
        $Sender->MarketPlace=$this->Plgn->API();
        $LogCallback = GetValue('LogCallback',$Gateway['Options']);
        call_user_func($LogCallback,$Sender,$TransactionID);
    }
    
    public function SetTransMeta($Sender){

        if($Sender->Form->IsPostBack() != False){
            $FormValues = $Sender->Form->FormValues();
            $MarketProductModel = new MarketProductModel();
            $ProductSlug=GetValue(2,$Sender->RequestArgs);
            $Product = $MarketProductModel->GetBySlug($ProductSlug);
            $Product->Meta=Gdn_Format::Unserialize($Product->Meta);
            $Product->EnabledGateways=Gdn_Format::Unserialize($Product->EnabledGateways);
            $TransactionID = $FormValues['TransactionID'];
            $MetaNames = $FormValues['MetaName'];
            $MetaValues = $FormValues['MetaValue'];
            
            if(!$TransactionID)
                die(json_encode(array('status'=>'error', 'errormsg'=>T('No TransactionID'))));

            
            $Meta = array();
            $this->Plgn->API()->ValidateMeta($FormValues, $MarketProductModel, $Product->ProductType);
            $MarketProductModel->Validation->Validate($FormValues);
            $Sender->Form->SetValidationResults($MarketProductModel->Validation->Results());
            if (count($MarketProductModel->Validation->Results()) == 0) {
                foreach($MetaNames As $MetaNameI => $MetaName){
                    $Meta[$MetaName]=$MetaValues[$MetaNameI];
                }
                
                MarketTransactionModel::SetTransactionMeta($TransactionID, $Meta);
                
                include($this->Plgn->Utility()->ThemeView('buttons'));
                
                $PriceDenominations = Gdn_Format::Unserialize($Product->PriceDenominations);
                $Sender->MarketPlace=$this->Plgn->API();
                die(json_encode(array('status'=>'success', 'buttons'=>DrawButtons($Sender,$PriceDenominations,$Product,$TransactionID))));
            }else{
                die(json_encode(array('status'=>'error', 'errormsg'=>$Sender->Form->Errors())));
            }
        }
    }
    
    
    public function Controller_Stripe($Sender){
        if($Sender->Form->IsPostBack() == False)
            throw NotFoundException();
   
        $FormValues = $Sender->Form->FormValues();
        $Sender->AddDefinition('StipeKey',C('Plugins.MarketPlace.Gateway.Stripe.PublicKey'));
        $Sender->AddJsFile('stripe.js','plugins/MarketPlace');
        $Sender->AddCssFile('stripe.css','plugins/MarketPlace');
        $Sender->Head->AddScript('https://js.stripe.com/v1');
        $TransactionID=$FormValues['TransactionID'];
        
        $Sender->SetData('TransUrl',MarketPlaceAPI::TransUrl('Stripe',Gdn::Session()->UserID,$Sender->Form->GetValue('ProductSlug'),$TransactionID));
        
        $Suffix = '';
        if(GetValue('Subscription',$FormValues)){
            $Suffix='subscription';
        }
        $Sender->View = $this->Plgn->Utility()->ThemeView('stripe'.$Suffix);
        $Sender->Render();
    }    

    
    /*End Marketplace Controllers*/
    
    /* Profile Subscriptions*/
    
    public function Profile_Subscriptions_Controller($Sender){
        if(!Gdn::Session()->IsValid())
            Redirect('/entry/signin?Target='.urlencode($Sender->SelfUrl));
        $Sender->GetUserInfo(Gdn::Session()->UserID, Gdn::Session()->User->Name);
        
        
        $Sender->SetTabView('Subscriptions', $this->Plgn->Utility()->ThemeView('subscriptions'), 'Profile', 'Dashboard');
        $Sender->AddCssFile('subscriptions.css','plugins/MarketPlace');
        $MarketSubscriptionsModel = new MarketSubscriptionsModel();
        $Sender->SetData('UserSubscription',$MarketSubscriptionsModel->GetByUser(Gdn::Session()->UserID));
        
        $Sender->Render();
    }
    
    
    public function Profile_AddProfileTabs($Sender){
        $Sender->AddProfileTab('Subscriptions','profile/subscriptions',
                        'Subscriptions',T('Subscriptions'));
    }
    
    /* End Profile Subscriptions*/
    
    /*Marketplace Menu item*/
    public function MarketPlaceLink($Sender) {
        if ($Sender->Menu && Gdn::Session()->IsValid() && Gdn::Session()->CheckPermission('Plugins.MarketPlace.UseStore')) {
            $Sender->Menu->AddLink('Store', T(C('Plugins.MarketPlace.StoreName')), '/'.C('Plugins.MarketPlace.StoreURI'));
        }
    }
    /*Marketplace Menu item*/
    
    
}

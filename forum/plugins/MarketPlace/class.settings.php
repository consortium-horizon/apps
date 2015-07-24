<?php if (!defined('APPLICATION')) exit();
abstract class MarketPlaceSettingsDomain extends MarketPlaceAPIDomain {
  
  private $WorkerName = 'Settings';
  
  public function CalledFrom(){
    return $this->WorkerName;
  }
  
  public function Settings(){
    $WorkerName = $this->WorkerName;
    if(!GetValue($WorkerName, $this->Workers)){
    $WorkerClass = $this->GetPluginIndex().$WorkerName;

      $this->LinkWorker($WorkerName,$WorkerClass);
    }
    return $this->Workers[$WorkerName];
  }
}

class MarketPlaceSettings {
    public function Settings_MenuItems($Sender) {
        $Menu = $Sender->EventArguments['SideMenu'];
        $Menu->AddItem('MarketPlace', T('Marketplace'),FALSE, array('class' => 'Reputation'));
        $Menu->AddLink('MarketPlace', T('Settings'), 'settings/marketplace', 'Garden.Settings.Manage');
        $Menu->AddLink('MarketPlace', T('Product Listings'), 'settings/marketplace/listings', 'Garden.Settings.Manage');
        $Menu->AddLink('MarketPlace', T('Transaction Log'), 'settings/transactionlog', 'Garden.Settings.Manage');
        if(!C('Garden.DashboardMenu.Sort')){
            //resort PremiumAccounts menu bellow Users
            $Items = array_keys($Menu->Items);
            $PK = array_search('MarketPlace',$Items);
            $PA = array_splice($Items,$PK,1);
            $UK = array_search('Users',$Items);
            array_splice($Items,$UK+1,0,$PA);
            $MItems=array();
            foreach ($Items As $Item)
                $MItems[$Item]=$Menu->Items[$Item];
            $Menu->Items=$MItems;
            $Menu->Sort=$Items;
        }
    }
    
    public function Settings_Controller($Sender){
        $Sender->Permission('Garden.Settings.Manage');
        $MarketProductModel = new MarketProductModel();
        $Opperation = strtolower(GetValue(1,$Sender->RequestArgs));
        if($Opperation=='delete'){
            $Slug = Gdn_Format::Url(GetValue(0,$Sender->RequestArgs));
            if(GetValue('Subscription',$FormValues['Meta'])==$Slug)
                SaveToConfig('Plugins.MarketPlace.Subscription', false);
            $MarketProductModel->DeleteBySlug($Slug);
            Redirect('/settings/marketplace/listings');
        }else if($Opperation=='up'){
            $MarketProductModel->SwapOrder(GetValue(0,$Sender->RequestArgs),'up');
            Redirect(GetValue('r',$_GET));
        }else if($Opperation=='down'){
            $MarketProductModel->SwapOrder(GetValue(0,$Sender->RequestArgs),'down');
            Redirect(GetValue('r',$_GET));
        }
        $Sender->Form->InputPrefix='Form';
        if($Sender->Form->IsPostBack() != False){
            $FormValues = $Sender->Form->FormValues();
            $Settings=array();
            switch($FormValues['Task']){
                case 'StoreConfig':
                    foreach ($FormValues As $FormIndex => $FormValue)
                        if(!in_array($FormIndex,array('Task','TransientKey','hpt','Save')))
                            $Settings['Plugins.MarketPlace.'.$FormIndex]=$FormValue;
                    if(!$Sender->Form->ErrorCount()){
                        Gdn::Router()->DeleteRoute('^'.C('Plugins.MarketPlace.StoreURI','store').'(/.*)?$');
                        SaveToConfig($Settings);
                        Gdn::Router()->SetRoute('^'.C('Plugins.MarketPlace.StoreURI','store').'(/.*)?$','vanilla/marketplace$1','Internal');
                    }
                    break;
                case 'AddEditProduct':
                    
                    $MarketProductModel->Validation->AddRule('Name','Required');
                    $MarketProductModel->Validation->AddRule('PriceDenominations', 'RequiredArray',T('You need to add some Price Denominations'));
                    $MarketProductModel->DefineSchema();
                    foreach ($FormValues['Amount'] As $AmountI=>$Amount){
                        if(!empty($Amount) && GetValue($AmountI,$FormValues['Currency'])){
                            if(! is_numeric($Amount))
                                $MarketProductModel->Validation->AddValidationResult('Amount'.$Amount,sprintf(T('\'%s\' is not a Decimal Amount'),$Amount));
                        }
                    }
                    $HasGateways=false;
                    foreach($FormValues['Gateways'] As $Gateway => $Enabled){
                        if(GetValue($Gateway,$this->Plgn->API()->Gateways) && $Enabled){
                            $HasGateways=true;
                            break;
                        }
                    }
                    
                    if(!$HasGateways){
                        $MarketProductModel->Validation->AddValidationResult('GatewayMissing',T('You need at least one active Gateway.'));
                    }
                    
                    $FormValues['PriceDenominations'] =array();
                    
                    foreach ($FormValues['Currency'] As $CurrencyI => $Currency){
                        if(!empty($Currency) && GetValue($CurrencyI,$FormValues['Amount'])){
                            $FormValues['PriceDenominations'][$Currency]=number_format(GetValue($CurrencyI,$FormValues['Amount']),2,'.','');
                            if(!in_array($Currency,$this->Plgn->API()->Currencies))
                                $MarketProductModel->Validation->AddValidationResult('Currency'.$Currency,sprintf(T('\'%s\' not a valid Currency'),$Currency));
                        }
                    }
                    
                    $FormValues['Meta']=array();
                    
                    $this->Plgn->API()->ValidateMeta($FormValues,$MarketProductModel,$FormValues['ProductType'],TRUE);
                    $PrePass = GetValue('PrePass',$this->Plgn->API()->ProductTypes[$FormValues['ProductType']]['Options']);
                    
                    if($PrePass){
                        $FormValues = call_user_func($PrePass,$Sender,$MarketProductModel,$FormValues);
                    }
                    
                    foreach($this->Plgn->API()->ProductTypes[$FormValues['ProductType']]['Options']['RequiredMeta'] As $MetaName){
                        if(!array_key_exists($MetaName,$FormValues['Meta']))
                            $MarketProductModel->Validation->AddValidationResult('Meta'.$MetaName,sprintf(T('\'%s\' Meta is required'),$MetaName));
                    }
                    
                    if(GetValue('Subscription',$FormValues['Meta'])=='On' && C('Plugins.MarketPlace.Subscription') && C('Plugins.MarketPlace.Subscription')!=Gdn_Format::Url(strtolower($FormValues['Name']))){
                        $MarketProductModel->Validation->AddValidationResult('Meta'.$MetaName,sprintf(T('Currently only a single subscription product is possible.'),'Subscription'));
                    }else if(GetValue('Subscription',$FormValues['Meta'])=='On' && trim(GetValue('Period',$FormValues['Meta']))!='1 month'){
                        $MarketProductModel->Validation->AddValidationResult('Meta'.$MetaName,sprintf(T('Currently only 1 month recurring subscriptions are available.'),'Period'));
                        
                    }    
                    
                    $FormValuesTemp = $FormValues;
                    
                    $FormValuesTemp['PriceDenominations'] = Gdn_Format::Serialize($FormValues['PriceDenominations']);
                    $FormValuesTemp['Meta'] = Gdn_Format::Serialize($FormValues['Meta']);
                                                
                    $MarketProductModel->Validation->Validate($FormValuesTemp);
                    $Sender->Form->SetValidationResults($MarketProductModel->Validation->Results());
                    if (count($MarketProductModel->Validation->Results()) == 0) {
                        if(in_array('Subscription',$this->Plgn->API()->ProductTypes[$FormValues['ProductType']]['Options']['RequiredMeta'])){

                            
                            if(GetValue('Subscription',$FormValues['Meta'])=='On' && (!C('Plugins.MarketPlace.Subscription') || C('Plugins.MarketPlace.Subscription')==Gdn_Format::Url(strtolower($FormValues['Name'])))){
                                SaveToConfig('Plugins.MarketPlace.Subscription', Gdn_Format::Url(strtolower($FormValues['Name'])));
                                
                            }else if(GetValue('Subscription',$FormValues['Meta'])=='Off' && C('Plugins.MarketPlace.Subscription')==Gdn_Format::Url(strtolower($FormValues['Name']))){
                                SaveToConfig('Plugins.MarketPlace.Subscription', false);
                            }else{
                                unset($FormValues['Meta']['Subscription']);
                            }
                        }
                        $MarketProductModel->Set($FormValues['Name'],$FormValues['Description'],$FormValues['ProductType'],$FormValues['PriceDenominations'],$FormValues['Meta'],$FormValues['Gateways']);
                        Redirect('/settings/marketplace/listings');

                    }
                    $Sender->Form->SetData($FormValues);
                    break;
                
                case 'PayPalSettings':
                    $Validation = new Gdn_Validation();
         
                    $Validation->AddRule('Account','regex:`^[A-Z0-9]{13}$`');
                    $Validation->ApplyRule('Account', 'Account', 'You must enter a valid PayPal Account ID');
                    $Validation->Validate($FormValues);
                    $Sender->Form->SetValidationResults($Validation->Results());
                    $FormValues = $Sender->Form->FormValues();
                    if(!$Sender->Form->ErrorCount()){
                        foreach ($FormValues As $FormIndex => $FormValue)
                            if(strpos($FormIndex,'SubscriptionPeriod')===FALSE && !in_array($FormIndex,array('Task','Save','TransientKey','hpt')))
                                $Settings['Plugins.MarketPlace.Gateway.PayPal.'.$FormIndex]=$FormValue;
                        SaveToConfig($Settings);            
                    }
                    break;
                case 'StripeSettings':
                    $Validation = new Gdn_Validation();
         
                    /*$Validation->AddRule('PrivateKey','regex:`^[a-zA-Z0-9]{32}$`');
                    $Validation->ApplyRule('PrivateKey', 'PrivateKey', 'You must enter a valid Stripe API Private key');
                    $Validation->AddRule('PublicKey','regex:`^pk_[a-zA-Z0-9]{29}$`');
                    $Validation->ApplyRule('PublicKey', 'PublicKey', 'You must enter a valid Stripe API Public key');*/
                    $Validation->Validate($FormValues);
                    $Sender->Form->SetValidationResults($Validation->Results());
                    $FormValues = $Sender->Form->FormValues();
                    if(!$Sender->Form->ErrorCount()){
                        foreach ($FormValues As $FormIndex => $FormValue)
                            if(strpos($FormIndex,'SubscriptionPeriod')===FALSE && !in_array($FormIndex,array('Task','Save','TransientKey','hpt')))
                                $Settings['Plugins.MarketPlace.Gateway.Stripe.'.$FormIndex]=$FormValue;
                        SaveToConfig($Settings);            
                    }
                    break;
            }
            
        }
        list($Offset, $Limit) = OffsetLimit(array_key_exists(1,$Sender->RequestArgs)?$Sender->RequestArgs[1]:0,C('Plugins.MarketPlace.AdminPageLimit',5));
        $Sender->Offset=$Offset;
        $Sender->AddSideMenu();
        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
        $Sender->Pager->MoreCode = '>>';
        $Sender->Pager->LessCode = '<<';
        $Sender->Pager->ClientID = 'Pager';
        $Sender->Pager->Configure(
            $Sender->Offset,
            $Limit,
            $MarketProductModel->GetCount(),
            strtolower(GetValue(0,$Sender->RequestArgs))=='listings'?'settings/marketplace/listings/{Page}':'settings/marketplace/{Page}'
        );
        $Sender->SetData('MarketProducts',$MarketProductModel->GetAll($Limit,$Offset));
        $Sender->AddDefinition('ProductTypes',json_encode($this->Plgn->API()->ProductTypes));
        $Sender->AddDefinition('Currencies',json_encode($this->Plgn->API()->Currencies));
        $Sender->AddDefinition('ItemURL',C('Plugins.MarketPlace.StoreURI','store').'/'.C('Plugins.MarketPlace.ProductURI','item'));
        $Sender->SetData('ProductTypes',$this->Plgn->API()->ProductTypes);
        $Sender->SetData('Gateways',array_keys($this->Plgn->API()->Gateways));
        
        $Sender->SetData('Description', T($this->Plgn->PluginInfo['Description']));
        $Sender->AddJsFile('marketplace.js','plugins/MarketPlace');
    
        if(strtolower(GetValue(0,$Sender->RequestArgs))=='listings'){
            $Sender->SetData('Title', T('Product Listings'));
            $Sender->Render($this->Plgn->Utility()->ThemeView('listings'));
        }else{
            $Sender->SetData('Title', T('Marketplace Settings'));
            $Sender->Render($this->Plgn->Utility()->ThemeView('settings'));
        }
    }
    
    public function TransactionLog_Controller($Sender){
        $Sender->Permission('Garden.Settings.Manage');
        if($Sender->DeliveryMethod() == DELIVERY_METHOD_JSON){
            $Page = GetValue('Page',$_GET);
            $Total = GetValue('rp',$_GET);
            if(!$Page) $Page = 1;
            $Limit = 20;
            $Offset = ($Page - 1) * $Limit;
            $SortOrder = GetValue('sortorder',$_GET, 'Date');
            $SortName = GetValue('sortname',$_GET, 'DESC');
            $Search = GetValue('query',$_GET,'');
            $SearchCol = GetValue('qtype',$_GET,'');
            $History = GetValue('history',$_GET, false);
            if($SearchCol=='PurchaseUserID')
                $SearchCol='Name';
            if($SortName=='UserID')
                $SortName='Name';

            $PremiumLog = new MarketTransactionModel();
            $Log = $PremiumLog->GetLog($Limit,$Offset,$SortName,$SortOrder,$Search,$SearchCol,$History);

            $Rows=array();
            foreach ($Log As $Item)
                $Rows[]=array('cell'=>$Item);
            $Sender->Rows = $Rows;
            $Data=array(
                'page'=>$Page,
                'total'=> empty($Rows)?0:$Total,
                'rows'=>$Rows,
                'get'=> Gdn::Structure()->Table('MarketTransaction')->ColumnExists($SearchCol)
                );
             exit(json_encode($Data));

        }else{
            $Sender->AddSideMenu();
            $Sender->SetData('Title', T('Transaction Log'));
            $Sender->SetData('Description', T('Master log of transactions'));
            $Sender->AddCssFile('flexigrid.css','plugins/MarketPlace/library/flexigrid/css');
            $Sender->AddJsFile('flexigrid.js','plugins/MarketPlace/library/flexigrid/js');
            $this->MarketPlace = $this;
            foreach($this->Plgn->API()->Gateways As $GatewayN=>$Gateway){
                if(GetValue('ChargeLogURL',$Gateway['Options']))
                    $Sender->AddDefinition($GatewayN.'URL',GetValue('ChargeLogURL',$Gateway['Options']));
                if(GetValue('LogCallback',$Gateway['Options']))
                    $Sender->AddDefinition($GatewayN.'URL',Url(C('Plugins.MarketPlace.StoreURI').'/'.strtolower($GatewayN).'trans/log/%s'));
            }
            $Sender->Render($this->Plgn->Utility()->ThemeView('translog'));

        }
    }
  
}

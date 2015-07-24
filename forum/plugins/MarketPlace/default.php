<?php if (!defined('APPLICATION')) exit();
// Define the plugin:
$PluginInfo['MarketPlace'] = array(
   'Name' => 'MarketPlace',
   'Description' => "Product, store and gateway system",
   'SettingsUrl' => '/dashboard/settings/marketplace',
   'Version' => '0.2.4b',
   'RequiredPlugins'=> array('KarmaBank'=> '0.9.7.1b'),
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'RegisterPermissions' => array('Plugins.MarketPlace.UseStore'),
   'Author' => 'Paul Thomas',
   'AuthorEmail' => 'dt01pqt_pt@yahoo.com',
   'AuthorUrl' => 'http://www.vanillaforums.org/profile/x00'
);



function MarketPlaceLoad($Class){
    $Match = array();
    if(preg_match('`^MarketPlace(.*)`',$Class,$Match)){
        $File = strtolower(preg_replace('`Domain$`','',$Match[1]));
        include_once(PATH_PLUGINS.DS.'MarketPlace'.DS.'class.'.$File.'.php');
    }
}

spl_autoload_register('MarketPlaceLoad');

MarketPlaceUtility::InitLoad();

MarketPlaceUtility::RegisterLoadMap('`^.*Gateway$`','gateways','class.{$Matches[0]}.php');
MarketPlaceUtility::RegisterLoadMap('`^Stripe$`','gateways/stripe-php/lib','Stripe.php',FALSE);
MarketPlaceUtility::RegisterLoadMap('`^(IpnChecker|PaypalPayment)$`','gateways/paypal','class.{$Matches[0]}.php');
MarketPlaceUtility::RegisterLoadMap('`^.*Email$`','email','class.{$Matches[0]}.php');

class MarketPlace extends MarketPlaceUIDomain{
  
    /* Marketplace UI*/

    public function VanillaController_MarketPlace_Create($Sender, $Args){
        $this->UI()->MarketPlace_Controller($Sender, $Args);
    }
  
    /* subscriptions */
    public function ProfileController_Subscriptions_Create($Sender){
        $this->UI()->Profile_Subscriptions_Controller($Sender);
    }
  
    public function ProfileController_AddProfileTabs_Handler($Sender){
        $this->UI()->Profile_AddProfileTabs($Sender);
    }
  
  
    /* Settings */
    public function Base_GetAppSettingsMenuItems_Handler($Sender){
        $this->Settings()->Settings_MenuItems($Sender);
    }
    public function SettingsController_MarketPlace_Create($Sender){
        $this->Settings()->Settings_Controller($Sender);
    }
    public function SettingsController_TransactionLog_Create($Sender){
        $this->Settings()->TransactionLog_Controller($Sender);
    }
  
    /* End Marketplace UI*/
    
    /*Marketplace Menu item*/
    public function Base_Render_Before($Sender) {
        $this->UI()->MarketPlaceLink($Sender);
    }
    /*Marketplace Menu item*/
    
    /*Load Marketplace */
    public function Base_BeforeControllerMethod_Handler($Sender) {
        $this->API()->Init();
    }
    /*End Load Marketplace */
    
    public function Base_BeforeDispatch_Handler($Sender){
        $this->Utility()->HotLoad();
    }
  
    public function Base_BeforeLoadRoutes_Handler($Sender, &$Args){
        $this->Utility()->DynamicRoute($Args['Routes'],'^'.C('Plugins.MarketPlace.StoreURI','store').'(/.*)?$','vanilla/marketplace$1','Internal', TRUE, C('Plugins.MarketPlace.StoreURI'));
    }
    
    /* prevent block of transactions and notifications like private community does */
    public function Base_BeforeBlockDetect_Handler($Sender,$Args){
        $Sender->EventArguments['BlockExceptions']['/'.C('Plugins.MarketPlace.StoreURI').'\/[a-zA-Z0-9_\-]*trans(\/.*)?$/']=Gdn_Dispatcher::BLOCK_NEVER;
    }
    
    public function Setup() {
        $this->Utility()->HotLoad(TRUE);
    }
    
    public function PluginSetup(){
        Gdn::Structure()
        ->Table('MarketProduct')
        ->Column('ProductID','varchar(100)',false,array('key'))
        ->Column('Name','varchar(100)',false,array('key'))
        ->Column('Slug','varchar(100)',false,array('key','unique'))
        ->Column('Description','text')
        ->Column('ProductType','varchar(100)')
        ->Column('PriceDenominations','text')
        ->Column('Meta','text',null)
        ->Column('EnabledGateways','text',null)
        ->Column('ShowOrder','int(11)',0)
        ->Set();
        
        Gdn::Structure()
        ->Table('MarketProduct')
        ->PrimaryKey('ProductID')
        ->Set();
        
        Gdn::Structure()
        ->Table('MarketTransaction')
        ->Column('TransactionID','char(32)',false,'primary')
        ->Column('GatewayTransactionID','varchar(100)',null)
        ->Column('Gateway','varchar(100)')
        ->Column('ProductSlug','varchar(100)')
        ->Column('PurchaseUserID','int(11)')
        ->Column('Status','varchar(100)')
        ->Column('Inform','int(4)',0)
        ->Column('Date','datetime',FALSE, 'key')
        ->Column('Meta','text',null)
        ->Set();
        
        Gdn::Structure()
        ->Table('MarketTransactionHistory')
        ->Column('TransactionID','char(32)',FALSE,'index')
        ->Column('GatewayTransactionID','varchar(100)',null)
        ->Column('Gateway','varchar(100)')
        ->Column('ProductSlug','varchar(100)')
        ->Column('PurchaseUserID','int(11)')
        ->Column('Status','varchar(100)')
        ->Column('Date','datetime',FALSE)
        ->Column('Meta','text',null)
        ->Set();
        
        Gdn::Structure()
        ->Table('MarketTransactionMeta')
        ->Column('TransactionID','char(32)',FALSE,'primary')
        ->Column('Date','datetime',FALSE, 'key')
        ->Column('Meta','text')
        ->Column('Persist','int(4)',0)
        ->Set();
        
        Gdn::Structure()
        ->Table('MarketSubscriptions')
        ->Column('SubscriptionID','varchar(255)',FALSE, 'primary')
        ->Column('UserID','int(11)',FALSE,'index')
        ->Column('Gateway','varchar(100)')
        ->Column('ExpireDate','datetime')
        ->Column('SubscriptionData','text')
        ->Set();
        
        if(C('Plugins.PremiumAccounts.PayPalAccount'))
            SaveToConfig(
                array(
                  'Plugins.MarketPlace.Gateway.PayPal.Account'=>C('Plugins.PremiumAccounts.PayPalAccount'),
                  'Plugins.MarketPlace.Gateway.PayPal.AccountType'=>C('Plugins.PremiumAccounts.AccountType')
                )
            );
    }
}

<?php if (!defined('APPLICATION')) exit();
 
$PluginInfo['Tapatalk'] = array(
   'Name' => 'Tapatalk',
   'Description' => 'Tapatalk Plugin for Vanilla 2',
   'Version' => 'vn20_1.5.1',
   'SettingsUrl' => '/plugin/tapatalk',
   'SettingsPermission' => 'Garden.Settings.Manage',
   'Author' => "Tapatalk",
   'AuthorEmail' => 'admin@tapatalk.com',
   'AuthorUrl' => 'http://tapatalk.com',
   'MobileFriendly' => true
);

class TapatalkPlugin extends Gdn_Plugin {
    
    public function __construct() {
    }
    
    /*
    public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
        $Sender->AddJsFile('/mobiquo/appExtt/ExttMbqBeforeRunDetectJs.php');
        $Sender->AddJsFile('/mobiquo/tapadetect.js');
    }
    */
    
    public function Base_Render_Before($Sender) {
        if (defined('MBQ_IN_IT')) return;   //filter mobiquo folder
        if (($_REQUEST['p'] && strpos($_REQUEST['p'], '/dashboard') === 0) || $Sender->MasterView == 'admin') { //filter the backend page
            return;
        }
        $isSsl = false;
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 1){  //Apache
            $isSsl = true;
        }elseif(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){ //IIS
            $isSsl = true;
        }elseif(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){ //other
            $isSsl = true;
        }
        $protocol = $isSsl ? 'https' : 'http';
        $phpSelf = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $base = $protocol.'://'.$_SERVER['SERVER_NAME'].preg_replace('/index\.php.*/i', '', $phpSelf);
        $filePath = dirname(__FILE__).'/../../mobiquo/custom/customSmartbanner.php';
        if (is_file($filePath)) {
            require_once($filePath);
            /* init MbqSmartbanner from backend settings start */
            MbqSmartbanner::$TAPATALKDIR = C('Plugin.Tapatalk.tapatalk_directory');
            MbqSmartbanner::$API_KEY = C('Plugin.Tapatalk.tapatalk_push_key');
            MbqSmartbanner::$APP_ADS_ENABLE = C('Plugin.Tapatalk.tapatalk_full_banner');
            MbqSmartbanner::$APP_BANNER_MESSAGE = C('Plugin.Tapatalk.app_banner_message');
            MbqSmartbanner::$APP_IOS_ID = C('Plugin.Tapatalk.app_ios_id');
            MbqSmartbanner::$APP_ANDROID_ID = C('Plugin.Tapatalk.app_android_id');
            MbqSmartbanner::$APP_KINDLE_URL = C('Plugin.Tapatalk.app_kindle_url');
            /* init MbqSmartbanner from backend settings end */
            MbqSmartbanner::$functionCallAfterWindowLoad = 1;
            $functionCallAfterWindowLoad = MbqSmartbanner::$functionCallAfterWindowLoad;
    		if (MbqSmartbanner::$IS_MOBILE_SKIN) $is_mobile_skin = MbqSmartbanner::$IS_MOBILE_SKIN;
    		if (MbqSmartbanner::$APP_IOS_ID) $app_ios_id = MbqSmartbanner::$APP_IOS_ID;
    		if (MbqSmartbanner::$APP_ANDROID_ID) $app_android_id = MbqSmartbanner::$APP_ANDROID_ID;
    		if (MbqSmartbanner::$APP_KINDLE_URL) $app_kindle_url = MbqSmartbanner::$APP_KINDLE_URL;
    		if (MbqSmartbanner::$APP_BANNER_MESSAGE) $app_banner_message = MbqSmartbanner::$APP_BANNER_MESSAGE;
    		MbqSmartbanner::$APP_FORUM_NAME = C('Garden.Title');
    		$app_forum_name = MbqSmartbanner::$APP_FORUM_NAME;
            MbqSmartbanner::$APP_LOCATION_URL = 'tapatalk://'.preg_replace('/http[s]?\:\/\/(.*?)/i', '$1', $base).'?location=index';
            $app_location_url = MbqSmartbanner::$APP_LOCATION_URL;
            MbqSmartbanner::$BOARD_URL = substr($base, 0, strlen($base) - 1);       //!
            $board_url = MbqSmartbanner::$BOARD_URL;
            $tapatalk_dir = MbqSmartbanner::$TAPATALKDIR;
            MbqSmartbanner::$TAPATALKDIR_URL = $base.MbqSmartbanner::$TAPATALKDIR;
            $tapatalk_dir_url = MbqSmartbanner::$TAPATALKDIR_URL;
            $app_ads_enable = MbqSmartbanner::$APP_ADS_ENABLE ? true : false;
            $api_key = MbqSmartbanner::$API_KEY;
            if (file_exists($tapatalk_dir . '/smartbanner/head.inc.php'))
                require_once($tapatalk_dir . '/smartbanner/head.inc.php');
            //header code
            $Sender->Head->AddString($app_head_include);
            $Sender->Head->AddString('
            <script type="text/javascript" language="Javascript">
            jQuery(document).ready(function($){
                if (typeof("tapatalkDetect") == "function")
                    tapatalkDetect();
            })
            </script>
            ');
        }
    }
    
    /*
    public function Base_Render_Before($Sender) {
        //$forumTitle = C('Garden.Title');
        //$forumTitle = Gdn::Config('Garden.Title');
        if (($_REQUEST['p'] && strpos($_REQUEST['p'], '/dashboard') === 0) || $Sender->MasterView == 'admin') { //filter the backend page
            return;
        }
        $Sender->AddCssFile('/mobiquo/smartbanner/appbanner.css');
        $Sender->AddJsFile('/mobiquo/appExtt/ExttMbqBeforeRunSmartbanner.php');
        $Sender->AddJsFile('/mobiquo/smartbanner/appbanner.js');
        $Sender->Head->AddString('
        <script type="text/javascript" language="Javascript">
        jQuery(document).ready(function($){
            tapatalkDetect();
        })
        </script>
        ');
    }
    */
    
    /*
    public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
        //$forumTitle = C('Garden.Title');
        //$forumTitle = Gdn::Config('Garden.Title');
        $Sender->AddCssFile('/mobiquo/smartbanner/appbanner.css');
        $Sender->AddJsFile('/mobiquo/appExtt/ExttMbqBeforeRunSmartbanner.php');
        $Sender->AddJsFile('/mobiquo/smartbanner/appbanner.js');
        $Sender->Head->AddString('
        <script type="text/javascript" language="Javascript">
        jQuery(document).ready(function($){
            tapatalkDetect();
        })
        </script>
        ');
    }
    */
    
    /*
    public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
        $filePath = dirname(__FILE__).'/../../mobiquo/custom/customSmartbanner.php';
        if (is_file($filePath)) {
            require_once($filePath);
            $Sender->AddCssFile('/mobiquo/smartbanner/appbanner.css');
            MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME = C('Garden.Title');
            $str = '';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_IS_MOBILE_SKIN)
            $str .= 'var is_mobile_skin = '.MbqSmartbanner::$MBQ_SMARTBANNER_IS_MOBILE_SKIN.';';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_IOS_ID)
            $str .= 'var app_ios_id = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_IOS_ID.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_ANDROID_URL)
            $str .= 'var app_android_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_ANDROID_URL.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_KINDLE_URL)
            $str .= 'var app_kindle_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_KINDLE_URL.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_BANNER_MESSAGE)
            $str .= 'var app_banner_message = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_BANNER_MESSAGE.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME)
            $str .= 'var app_forum_name = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_FORUM_NAME.'";';
            if (MbqSmartbanner::$MBQ_SMARTBANNER_APP_LOCATION_URL)
            $str .= 'var app_location_url = "'.MbqSmartbanner::$MBQ_SMARTBANNER_APP_LOCATION_URL.'";';
            //since the added string is always behind the added js file,so it is useless for the smartbanner js code
            $Sender->Head->AddString('
            <script type="text/javascript" language="Javascript">
            '.$str.'
            </script>
            ');
            $Sender->AddJsFile('/mobiquo/smartbanner/appbanner.js');
        }
    }
    */
    
   public function PluginController_Tapatalk_Create($Sender) {
      $Sender->Title('Tapatalk Plugin');
      $Sender->AddSideMenu('plugin/tapatalk');
      $Sender->Form = new Gdn_Form();
      $this->Dispatch($Sender, $Sender->RequestArgs);
   }
   
   public function Controller_Index($Sender) {
      $Sender->Permission('Vanilla.Settings.Manage');
      
      $Sender->SetData('PluginDescription',$this->GetPluginKey('Description'));
		
      $Validation = new Gdn_Validation();
      $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
      $ConfigurationModel->SetField(array(
         'Plugin.Tapatalk.tapatalk_iar_registration_options' => 1,
         'Plugin.Tapatalk.tapatalk_iar_registration_url' => 'entry/register',
         'Plugin.Tapatalk.tapatalk_iar_usergroup_assignment' => 0,  //0 means invalid
         'Plugin.Tapatalk.tapatalk_directory' => 'mobiquo',
         'Plugin.Tapatalk.tapatalk_push_key' => '',
         'Plugin.Tapatalk.tapatalk_full_banner' => 1,
         'Plugin.Tapatalk.app_banner_message' => '',
         'Plugin.Tapatalk.app_ios_id' => '',
         'Plugin.Tapatalk.app_android_id' => '',
         'Plugin.Tapatalk.app_kindle_url' => ''
      ));
      
      // Set the model on the form.
      $Sender->Form->SetModel($ConfigurationModel);
   
      // If seeing the form for the first time...
      if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
         // Apply the config settings to the form.
         $Sender->Form->SetData($ConfigurationModel->Data);
	  } else {
         $ConfigurationModel->Validation->ApplyRule('Plugin.Tapatalk.tapatalk_iar_registration_options', array('Required', 'Integer'));
         $ConfigurationModel->Validation->ApplyRule('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment', array('Required', 'Integer'));
         $ConfigurationModel->Validation->ApplyRule('Plugin.Tapatalk.tapatalk_directory', 'Required');
         $ConfigurationModel->Validation->ApplyRule('Plugin.Tapatalk.tapatalk_full_banner', array('Required', 'Integer'));
         
         $Saved = $Sender->Form->Save();
         if ($Saved) {
            $Sender->StatusMessage = T("Your changes have been saved.");
         }
      }
      
      $Sender->Render($this->GetView('settingsView.php'));
   }
   
   public function Base_GetAppSettingsMenuItems_Handler($Sender) {
      $Menu = &$Sender->EventArguments['SideMenu'];
      $Menu->AddLink('Add-ons', 'Tapatalk', 'plugin/tapatalk', 'Garden.AdminUser.Only');
   }

   public function Setup() {
      SaveToConfig('Plugin.Tapatalk.tapatalk_iar_registration_options', 1);
      SaveToConfig('Plugin.Tapatalk.tapatalk_iar_registration_url', 'entry/register');
      SaveToConfig('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment', 0); //0 means invalid
      SaveToConfig('Plugin.Tapatalk.tapatalk_directory', 'mobiquo');
      SaveToConfig('Plugin.Tapatalk.tapatalk_push_key', '');
      SaveToConfig('Plugin.Tapatalk.tapatalk_full_banner', 1);
      SaveToConfig('Plugin.Tapatalk.app_banner_message', '');
      SaveToConfig('Plugin.Tapatalk.app_ios_id', '');
      SaveToConfig('Plugin.Tapatalk.app_android_id', '');
      SaveToConfig('Plugin.Tapatalk.app_kindle_url', '');
   }
   
   public function OnDisable() {
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_iar_registration_options');
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_iar_registration_url');
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment');
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_directory');
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_push_key');
      RemoveFromConfig('Plugin.Tapatalk.tapatalk_full_banner');
      RemoveFromConfig('Plugin.Tapatalk.app_banner_message');
      RemoveFromConfig('Plugin.Tapatalk.app_ios_id');
      RemoveFromConfig('Plugin.Tapatalk.app_android_id');
      RemoveFromConfig('Plugin.Tapatalk.app_kindle_url');
   }
	
}

?>

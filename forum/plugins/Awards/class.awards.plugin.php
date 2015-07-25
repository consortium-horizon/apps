<?php if(!defined('APPLICATION')) exit();

/* Copyright 2013 Diego Zanella (support@pathtoenlightenment.net)
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 3, as
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   GPL3: http://www.gnu.org/licenses/gpl-3.0.txt
*/

// File awards.defines.php must be included by manually specifying the whole
// path. It will then define some shortcuts for commonly used paths, such as
// AWARDS_PLUGIN_LIB_PATH, used just below.
require(PATH_PLUGINS . '/Awards/lib/awards.defines.php');
// AWARDS_PLUGIN_LIB_PATH is defined in awards.defines.php.
require(AWARDS_PLUGIN_LIB_PATH . '/awards.validation.php');

// Define the plugin:
$PluginInfo['Awards'] = array(
	'Name' => 'Awards Plugin',
	'Description' => 'Awards Plugin for Vanilla Forums',
	'Version' => '13.12.18',
	'RequiredApplications' => array('Vanilla' => '2.0'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => array('Logger' => '12.10.28',
														 'AeliaFoundationClasses' => '13.04.26',
														 ),
	'HasLocale' => FALSE,
	'MobileFriendly' => TRUE,
	'SettingsUrl' => '/plugin/awards/settings',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'D.Zanella',
	'AuthorEmail' => 'diego@pathtoenlightenment.net',
	'AuthorUrl' => 'http://dev.pathtoenlightenment.net',
	'RegisterPermissions' => array('Plugins.Awards.Manage',),
);

class AwardsPlugin extends Gdn_Plugin {
	// @var Logger The Logger used by the class.
	private $_Log;

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}

	/* @var array default lists the applications in which the Award assignments
	 * will be processed.
	 */
	private $_DefaultAllowedApplications = array(
		'vanilla',
		'conversations',
	);

	/* @var array Lists the applications in which the Award assignments will be
	 * processed. This will allow the processing to happen only in the frontend,
	 * without slowing down the Dashboard.
	 */
	private $_AllowedApplications = array(
		'vanilla',
		'conversations',
	);

	// @var string The Route Code to be used when registering the Activity Types for the plugin.
	const AWARD_ROUTECODE = 'award';
	// @var string Name of the Activity Type to use when a User earns an Award
	const ACTIVITY_AWARDEARNED = 'AwardEarned';
	// @var string Name of the Activity Type to use when an Award is revoked
	const ACTIVITY_AWARDREVOKED = 'AwardRevoked';

	/* @var array Keeps a list of the available Award Actvities. Used mainly to
	 * recognise, amongst the Activity entries, the ones related to the Awards.
	 */
	private $AwardActivities = array(
		self::ACTIVITY_AWARDEARNED,
		self::ACTIVITY_AWARDREVOKED,
	);

	/**
	 * Set Validation Rules related to Configuration Model.
	 *
	 * @param Gdn_Validation $Validation The Validation that is (or will be)
	 * associated to the Configuration Model.
	 *
	 * @return void
	 */
	protected function _SetConfigModelValidationRules(Gdn_Validation $Validation) {
		$Validation->AddRule('PositiveInteger', 'function:ValidatePositiveInteger');

		$Validation->ApplyRule('Plugin.Awards.MinSearchLength', 'Required', T('Please specify a value for Minimum Search Length.'));
		$Validation->ApplyRule('Plugin.Awards.MinSearchLength', 'PositiveInteger', T('Minimum Search Length must be a positive Integer.'));
	}


	/**
	 * Returns an instance of a Class and stores it as a property of this class.
	 * The function follows the principle of lazy initialization, instantiating
	 * the class the first time it's requested.
	 *
	 * @param string ClassName The Class to instantiate.
	 * @param array Args An array of Arguments to pass to the Class' constructor.
	 * @return object An instance of the specified class.
	 * @throws An Exception if the specified class does not exist.
	 */
	private function GetInstance($ClassName) {
		$FieldName = '_' . $ClassName;
		$Args = func_get_args();
		// Discard the first argument, as it is the Class Name, which doesn't have
		// to be passed to the instance of the Class
		array_shift($Args);

		if(empty($this->$FieldName)) {
			$Reflect  = new ReflectionClass($ClassName);

			$this->$FieldName = $Reflect->newInstanceArgs($Args);
		}

		return $this->$FieldName;
	}

	protected function GetAvailableApplications() {
		$Result = array();
		$ApplicationManager = new Gdn_ApplicationManager();
		foreach($ApplicationManager->AvailableVisibleApplications() as $ApplicationID => $ApplicationInfo) {
			$Result[$ApplicationID] = GetValue('Name', $ApplicationInfo, $ApplicationID);
		}
    return $Result;
	}

	/**
	 * Returns an instance of RulesManager.
	 *
	 * @return RulesManager An instance of RulesManager.
	 * @see AwardsPlugin::GetInstance()
	 */
	public function RulesManager() {
		return $this->GetInstance('AwardRulesManager');
	}

	/**
	 * Returns an instance of AwardsManager.
	 *
	 * @return AwardsManager An instance of AwardsManager.
	 * @see AwardsPlugin::GetInstance()
	 */
	public function AwardsManager() {
		return $this->GetInstance('AwardsManager');
	}

	/**
	 * Returns an instance of AwardClassesManager.
	 *
	 * @return AwardClassesManager An instance of AwardClassesManager.
	 * @see AwardsPlugin::GetInstance()
	 */
	public function AwardClassesManager() {
		return $this->GetInstance('AwardClassesManager');
	}

	/**
	 * Returns an instance of UserAwardsManager.
	 *
	 * @return UserAwardsManager An instance of UserAwardsManager.
	 * @see AwardsPlugin::GetInstance()
	 */
	public function UserAwardsManager() {
		return $this->GetInstance('UserAwardsManager');
	}

	/**
	 * Plugin constructor
	 *
	 * This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
	 * one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
	 * as it runs every page load and could slow down your forum.
	 */
	public function __construct() {
		parent::__construct();

		$this->_AllowedApplications = C('Plugin.Awards.AllowedApplications', $this->_DefaultAllowedApplications);
		// Instantiate specialised Controllers
		//$this->RulesManager();
		//$this->AwardsManager();
	}

	/**
	 * Processes all the Awards available to current User, eventually assigning
	 * one or more to him.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	 private function ProcessAwards(Gdn_Controller $Sender) {
		$this->AwardsManager()->ProcessAwards($this, $Sender);
	}

	/**
	 * Base_Render_Before Event Handler.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function Base_Render_Before(Gdn_Controller $Sender) {
		// Files for frontend
		$Sender->AddCssFile('awards.css', 'plugins/Awards/design/css');
		$Sender->AddJsFile('awards.js', 'plugins/Awards/js');
		// Common files
		$Sender->AddCssFile('awardclasses.css', 'plugins/Awards/design/css');
	}

	/**
	 * Base_AfterBody Event Handler.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function Base_AfterBody_Handler(Gdn_Controller $Sender) {
		// Files for frontend
		if(InArrayI($Sender->Application, $this->_AllowedApplications)) {
			// Process (and assign) Awards
			$this->ProcessAwards($Sender);
		}
	}

	/**
	 * Base_CommentInfo event Handler.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 * @param array Args An array of Arguments passed to the Controller.
	 */
	public function Base_CommentInfo_Handler($Sender, $Args) {
		// If Ranks Plugin is not available, display User's Score based on the Awards
		// he earned
		if(!Gdn::PluginManager()->CheckPlugin('Ranks')) {
			$Post = GetValue('Object', $Args);
			$UserID = GetValue('InsertUserID', $Post);

			$this->UserAwardsManager()->DisplayUserAwardsScore($this, $Sender, $UserID);
		}
	}

	/**
	 * Create a method called "Awards" on the PluginController
	 *
	 * @param $Sender Sending controller instance
	 */
	public function PluginController_Awards_Create($Sender) {
		/*
		 * If you build your views properly, this will be used as the <title> for your page, and for the header
		 * in the dashboard. Something like this works well: <h1><?php echo T($this->Data['Title']); ?></h1>
		 */
		$Sender->Title($this->GetPluginKey('Name'));
		$Sender->AddSideMenu('plugin/awards');

		// If your sub-pages use forms, this is a good place to get it ready
		$Sender->Form = new Gdn_Form();

		/*
		 * Note: When the URL is accessed without parameters, Controller_Index() is called. This is a good place
		 * for a dashboard settings screen.
		 */
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}

	/**
	 * Renders the Plugin's default (index) page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Index($Sender) {
		$this->Controller_AwardsPage($Sender);
	}

	/**
	 * Renders the Settings page.
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Controller_Settings($Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_GENERALSETTINGS_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		$Sender->SetData('PluginDescription', $this->GetPluginKey('Description'));

		$Validation = new Gdn_Validation();
		$this->_SetConfigModelValidationRules($Validation);

		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			// Set default configuration values
			'Plugin.Awards.MinSearchLength' => AWARDS_PLUGIN_MINSEARCHLENGTH,
			'Plugin.Awards.AllowedApplications' => $this->_DefaultAllowedApplications,
		));

		// Set the model on the form.
		$Sender->Form->SetModel($ConfigurationModel);

    $Sender->SetData('AvailableApplications', $this->GetAvailableApplications());

		// If seeing the form for the first time...
		if($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Apply the config settings to the form.
			$Sender->Form->SetData($ConfigurationModel->Data);
		}
		else {
			$Saved = $Sender->Form->Save();
			if($Saved) {
				$Sender->StatusMessage = T('Your changes have been saved.');
			}
		}

		$Sender->Render($this->GetView('awards_generalsettings_view.php'));
	}

	/**
	 * Renders the Status page.
	 *
	 * @param object Sender Sending controller instance
	 */
	public function Controller_Status($Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_STATUS_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		// Stores the list of directories that should be writable
		$RequiredWritableDirs = array(
			PATH_ROOT . '/' . AWARDS_PLUGIN_AWARDS_PICS_PATH,
			PATH_ROOT . '/' . AWARDS_PLUGIN_AWARDCLASSES_PICS_PATH,
			dirname(AWARDS_PLUGIN_AWARDCLASSES_CSS_FILE),
			AWARDS_PLUGIN_EXPORT_PATH,
			PATH_UPLOADS . '/' . AWARDS_PLUGIN_IMPORT_PATH,
		);

		$Sender->SetData('RequiredWritableDirs', $RequiredWritableDirs);

		$Sender->Render($this->GetView('awards_status_view.php'));
	}

	/**
	 * Add a link to the dashboard menu
	 *
	 * By grabbing a reference to the current SideMenu object we gain access to its methods, allowing us
	 * to add a menu link to the newly created /plugin/Awards method.
	 *
	 * @param $Sender Sending controller instance
	 */
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = $Sender->EventArguments['SideMenu'];

		// Unless another sort order is defined, put Awards menu directly below Users
		if(!C('Garden.DashboardMenu.Sort')) {
			// Extract the menu from the item immediately after "Users" to the end
			$UsersMenuIdx = array_search('Users', array_keys($Menu->Items));
			$AfterUsers = array_splice($Menu->Items, $UsersMenuIdx);
    }

		// Add Plugin's menu items
		$Menu->AddItem('Awards', T('Awards'), 'Plugins.Awards.Manage', array('class' => 'Reputation'));
		$Menu->AddLink('Awards',
									 T('General Settings'),
									 AWARDS_PLUGIN_GENERALSETTINGS_URL,
									 'Plugins.Awards.Manage');
		$Menu->AddLink('Awards',
									 T('Award Classes'),
									 AWARDS_PLUGIN_AWARDCLASSES_LIST_URL,
									 'Plugins.Awards.Manage');
		$Menu->AddLink('Awards',
									 T('Awards'),
									 AWARDS_PLUGIN_AWARDS_LIST_URL,
									 'Plugins.Awards.Manage');
		$Menu->AddLink('Awards',
									 T('Export'),
									 AWARDS_PLUGIN_EXPORT_URL,
									 'Plugins.Awards.Manage');
		$Menu->AddLink('Awards',
									 T('Import'),
									 AWARDS_PLUGIN_IMPORT_URL,
									 'Plugins.Awards.Manage');

		// If AfterUsers is defined, it means that the menu was spliced and it must
		// now be restored, by appending the previously removed items
		if(isset($AfterUsers)) {
			$Menu->Items = array_merge($Menu->Items, $AfterUsers);
		}
	}

	/**
	 * Renders the Award Classes List page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardClassesList($Sender) {
		$this->AwardClassesManager()->AwardClassesList($this, $Sender);
	}

	/**
	 * Renders the page to Add/Edit an Award Class.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardClassAddEdit($Sender) {
		$this->AwardClassesManager()->AwardClassAddEdit($this, $Sender);
	}

	/**
	 * Renders the page to Clone an Award Class.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardClassClone($Sender) {
		$this->AwardClassesManager()->AwardClassClone($this, $Sender);
	}

	/**
	 * Renders the page that allows to delete an Award Class.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardClassDelete($Sender) {
		$this->AwardClassesManager()->AwardClassDelete($this, $Sender);
	}

	/**
	 * Handler of event AwardsPlugin::ConfigChanged().
	 *
	 * @param Gdn_Pluggable Sender The object which fired the event.
	 */
	public function AwardsPlugin_ConfigChanged_Handler(Gdn_Pluggable $Sender) {
		$this->AwardClassesManager()->GenerateAwardClassesCSS($Sender);
	}

	/**
	 * Renders the Awards List page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardsList($Sender) {
		$this->AwardsManager()->AwardsList($this, $Sender);
	}

	/**
	 * Renders the Awards Frontend List page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardsPage($Sender) {
		// Add the module with the list of configurd Award Classes
		$Sender->AddModule(new AwardClassesModule());

		$this->AwardsManager()->AwardsPage($this, $Sender);
	}

	/**
	 * Renders the page to Add/Edit an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardAddEdit($Sender) {
		$this->AwardsManager()->AwardAddEdit($this, $Sender);
	}

	/**
	 * Renders the page to Clone an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardClone($Sender) {
		$this->AwardsManager()->AwardClone($this, $Sender);
	}

	/**
	 * Renders the page that allows to delete an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardDelete($Sender) {
		$this->AwardsManager()->AwardDelete($this, $Sender);
	}

	/**
	 * Renders the page that allows to assign an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardAssign($Sender) {
		$this->AwardsManager()->AwardAssign($this, $Sender);
	}

	/**
	 * Renders the page that allows to export Awards and Award Classes.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Export($Sender) {
		$this->AwardsManager()->Export($this, $Sender);
	}

	/**
	 * Renders the page that allows to import Awards and Award Classes.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Import($Sender) {
		$this->AwardsManager()->Import($this, $Sender);
	}

	/**
	 * Renders the Awards Leaderboard page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_Leaderboard($Sender) {
		// Add the module with the list of configurd Award Classes
		$Sender->AddModule(new AwardClassesModule());

		$this->UserAwardsManager()->AwardsLeaderboard($this, $Sender);
	}


	/**
	 * Returns a list of Users based on a search query.
	 *
	 * @param object Sender Sending controller instance.
	 * @param array Args An array containing the list of arguments passed with the
	 * URL.
	 */
	public function UserController_Search_Create($Sender, $Args) {
		$Sender->Permission('Plugins.Awards.Manage');
		// Extract the search query from the URL
		$SearchString = GetValue(0, $Args);
		//var_dump($SearchString);

		if(strlen($SearchString) < C('Plugin.Awards.MinSearchLength', AWARDS_PLUGIN_MINSEARCHLENGTH)) {
			$Sender->SetData('Error', sprintf(T('Search string must be at least %d characters long'), 2));
		}
		else {
			$UserModel = new UserModel();
			$Users = $UserModel->SQL
				->Select('U.UserID')
				->Select('U.Name', '', 'UserName')
				->Select('U.Email', '', 'EmailAddress')
				->From('User U')
				->Where('U.Deleted', 0)
				->Where('U.Name Like', '%' . $SearchString . '%')
				->OrderBy('U.Name')
				->Limit(15)
				->Get()
				->ResultObject();
			//var_dump($Users);
			$Sender->SetData('Users', $Users);
		}

		// Return the data as JSON
		$Sender->DeliveryMethod(DELIVERY_METHOD_JSON);
		$Sender->DeliveryType(DELIVERY_TYPE_DATA);

		// Render the View
		$Sender->Render();
	}

	/**
	 * Returns a list of Users based on a search query, indicating if any of them
	 * already received a specific Award.
	 *
	 * @param object Sender Sending controller instance.
	 * @param array Args An array containing the list of arguments passed with the
	 * URL.
	 */
	public function UserController_SearchWithAward_Create($Sender, $Args) {
		$Sender->Permission('Plugins.Awards.Manage');
		// Extract the search query from the URL
		$SearchString = GetValue(0, $Args);
		$AwardID = GetValue(1, $Args);

		$Result = true;
		if(empty($AwardID) || !is_numeric($AwardID)) {
			$Sender->SetData('Error', sprintf(T('Award ID is required.')));
			$Result = false;
		}

		if(strlen($SearchString) < C('Plugin.Awards.MinSearchLength', AWARDS_PLUGIN_MINSEARCHLENGTH)) {
			$Sender->SetData('Error', sprintf(T('Search string must be at least %d characters long.'), 2));
			$Result = false;
		}

		if($Result === true) {
			$UserModel = new UserModel();
			$Users = $UserModel->SQL
				->Select('U.UserID')
				->Select('U.Name', '', 'UserName')
				->Select('U.Email', '', 'EmailAddress')
				->Select('VAUAL.AwardID', '', 'AwardID')
				->Select('VAUAL.DateAwarded', '', 'DateAwarded')
				->Select('VAUAL.Recurring', '', 'Recurring')
				->From('User U')
				->LeftJoin('v_awards_userawardslist VAUAL',
									 '(VAUAL.UserID = U.UserID) AND (VAUAL.AwardID = ' . (int)$AwardID . ')')
				->Where('U.Deleted', 0)
				->Where('U.Name Like', '%' . $SearchString . '%')
				->OrderBy('U.Name')
				->Limit(15)
				->Get()
				->ResultObject();
			//var_dump($Users);
			$Sender->SetData('Users', $Users);
		}

		// Return the data as JSON
		$Sender->DeliveryMethod(DELIVERY_METHOD_JSON);
		$Sender->DeliveryType(DELIVERY_TYPE_DATA);

		// Render the View
		$Sender->Render();
	}

	/**
	 * Enables/disabled an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardEnable($Sender) {
		$this->AwardsManager()->AwardEnable($this, $Sender);
	}

	/**
	 * Renders the Award Info page, containing the details of an Award.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_AwardInfo($Sender) {
		// Add the module with the list of Awards earned by current User
		$Sender->AddModule($this->LoadUserAwardsModule($Sender));

		$this->AwardsManager()->AwardInfo($this, $Sender);
	}

	/**
	 * Renders the Awards Rules List page.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_RulesList($Sender) {
		$this->RulesManager()->RulesList($this, $Sender);
	}

	/**
	 * Returns a formatted list of files from a folder. Used by the Server Side
	 * File Browser.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function Controller_BrowseDir($Sender) {
		$Sender->Permission('Plugins.Awards.Manage');

		$Directory = $Sender->Request->GetValue('dir');
		// Remove trailing directory separator
		$Directory = preg_replace('/(\\\\|\/)$/', '', $Directory);

		// Specify which directories can be browsed. Anything above them is off limits
		$RootDirs = array(PATH_UPLOADS);

		$FileBrowser = new FileBrowser(PATH_UPLOADS, $RootDirs);
		$Files = $FileBrowser->GetFiles($Directory, true);
		if($Files === null) {
			$this->Log()->error('Invalid directory requested, no data returned.');
			return '';
		}
		natcasesort($Files);

		//var_dump($Files);die();
		$Result = array();
		// Build the HTML required by the jQueryFileTree plugin
		foreach($Files as $File) {
			$FileExt = pathinfo($File, PATHINFO_EXTENSION);
			$FileBaseName = basename($File);
			$FileLink = Anchor(htmlentities($FileBaseName),
												 '#',
												 '',
												 array('rel' => htmlentities($Directory . '/' . $FileBaseName)));
			if(is_dir($File)) {
				$Result[] = Wrap($FileLink,
												 'li',
												 array('class' => 'directory collapsed'));
			}
			else {
				$Result[] = Wrap($FileLink,
												 'li',
												 array('class' => 'file ext_' . $FileExt));
			}
		}
		echo Wrap(implode('', $Result),
							'ul',
							array('class' => 'jqueryFileTree'));
	}

	/**
	 * Renders the User Awards List page (in the Dashboard).
	 *
	 * @param object Sender Sending controller instance.
	 */
	//public function Controller_UserAwardsList($Sender) {
	//	$Sender->SetData('CurrentPath', AWARDS_PLUGIN_USERAWARDS_LIST_URL);
	//	// Prevent non authorised Users from accessing this page
	//	$Sender->Permission('Plugins.Awards.Manage');
	//
	
	//
	//	$Sender->Render($this->GetView('awards_userawardslist_view.php'));
	//}

	/**
	 * ProfileController_Render_Before event handler.
	 *
	 * @param object Sender Sending controller instance.
	 */
	public function ProfileController_Render_Before($Sender, $Args) {
		/* Load the module that will render the User Awards List widget and add it
		 * to the modules list
		 */
		$Sender->AddModule($this->LoadUserAwardsModule($Sender));
	}

	/**
	 * Loads and configures the User Award Module, which will generate the HTML
	 * for the User Awards widget.
	 *
 	 * @param Gdn_Controller Sender Sending controller instance.
 	 * @return UserAwardsModule An instance of the module.
 	 */
	private function LoadUserAwardsModule($Sender) {
		// If a User ID is specified explicitly, take that one. If not, take currently logged in User
		$UserID = isset($Sender->User->UserID) ? $Sender->User->UserID : Gdn::Session()->UserID;

		$UserAwardsModule = new UserAwardsModule($Sender);
		$UserAwardsModule->LoadData($UserID);
		return $UserAwardsModule;
	}

	/**
	 * Adds the Activity Types used by the plugin. They ares used to notify Users
	 * of earned and revoked Awards.
	 */
	private function AddAwardsActivityTypes() {
		// "Award earned" Activity Type
		Gdn::SQL()->Replace('ActivityType',
												array('AllowComments' => '0',
															// RouteCode is just a keyword which will be transformed into a link
															// to the Award on the Activities page
															'RouteCode' => self::AWARD_ROUTECODE,
															// Send notifications when Awards are earned
															'Notify' => '1',
															// Make Award activity public
															'Public' => '1',
															// Message showing "You earned the XYZ Award
															'ProfileHeadline' => '%3$s earned the %8$s Award.',
															// Message showing "User earned the XYZ Award
															'FullHeadline' => '%1$s earned the %8$s Award.'),
												array('Name' => self::ACTIVITY_AWARDEARNED), TRUE);
	}

	/**
	 * Deletes the Activity Types used by the plugin.
	 */
	private function RemoveAwardsActivityTypes() {
		Gdn::SQL()->Delete('ActivityType', array('Name' => 'AwardEarned'));
	}

	/**
	 * MenuModule_BeforeToString event handler.
	 * Adds Awards-related menu entries to the main menu.
	 *
 	 * @param Gdn_Controller Sender Sending controller instance.
 	 */
	public function MenuModule_BeforeToString_Handler($Sender) {
		// Link to Awards Page
		$Sender->AddLink('Awards',
										 T('Awards'),
										 AWARDS_PLUGIN_AWARDS_PAGE_URL,
										 false,
										 array('class' => 'AwardsMenu'),
										 array()
										 );
		// Link to Awards Leaderboard Page
		$Sender->AddLink('Awards',
										 T('Awards Leaderboard'),
										 AWARDS_PLUGIN_LEADERBOARD_PAGE_URL,
										 false,
										 array('class' => 'SubMenuItem'),
										 array()
										 );
	}

	/**
	 * ActivityModel_AfterActivityQuery Event Handler.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function ActivityModel_AfterActivityQuery_Handler($Sender) {
		$BaseURL = Url('/', true);

		// Add the data related to the Awards
		$Sender->SQL
			// For the Awards Notifications, field Route contains the ID of the Award
			->LeftJoin('Awards AWDS', '(t.RouteCode = \'' . self::AWARD_ROUTECODE . '\') AND (AWDS.AwardID = a.Route)')
			->LeftJoin('AwardClasses AWCS', '(AWCS.AwardClassID = AWDS.AwardClassID)')
			->Select('AWCS.AwardClassName')
			->Select('AWCS.AwardClassCSSClass')
			->Select('AWDS.AwardImageFile', 'COALESCE(CONCAT(\'' . $BaseURL . '\', %s), au.Photo)', 'ActivityPhoto')
			->Select('AWDS.AwardName', 'COALESCE(%s, t.RouteCode)', 'RouteCode')
			->Select('AWDS.AwardID', 'COALESCE(CONCAT(\'' . AWARDS_PLUGIN_AWARD_INFO_URL . '/\', %s), a.Route)', 'Route');
	}

	/**
	 * Intercept rendering of the Activity to alter the styles when it's time to
	 * display an Awards.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function Base_BeforeActivity_Handler($Sender) {
		$Activity = &$Sender->EventArguments['Activity'];
		$CssClass = &$Sender->EventArguments['CssClass'];

		if(InArrayI($Activity->ActivityType, $this->AwardActivities)) {
			$CssClass .= ' AwardActivity ' . $Activity->AwardClassCSSClass;
		}
	}

	/**
	 * ProfileController_AfterPreferencesDefined Event Handler.
	 * Adds Awards notification options to User's Preferences screen.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	public function ProfileController_AfterPreferencesDefined_Handler($Sender) {
		$Sender->Preferences['Notifications']['Email.' . self::ACTIVITY_AWARDEARNED] = T('Notify me of earned Awards.');
		$Sender->Preferences['Notifications']['Popup.' . self::ACTIVITY_AWARDEARNED] = T('Notify me of earned Awards.');
	}

	/**
	 * Plugin setup
	 *
	 * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen,
	 * and is a great place to perform one-time setup tasks, such as database structure changes,
	 * addition/modification ofconfig file settings, filesystem changes, etc.
	 */
	public function Setup() {
		// Set up the plugin's default values for Notification
		SaveToConfig('Preferences.Email.' . self::ACTIVITY_AWARDEARNED, 0);
		SaveToConfig('Preferences.Popup.' . self::ACTIVITY_AWARDEARNED, 1);

		// Miscellaneous default settings
		SaveToConfig('Plugin.Awards.MinSearchLength', AWARDS_PLUGIN_MINSEARCHLENGTH);
		SaveToConfig('Plugin.Awards.AllowedApplications', $this->_DefaultAllowedApplications);

		// Set up the Activity Types related to the Awards
		$this->AddAwardsActivityTypes();

		// Create shortcut Route to Awards Plugin pages
		Gdn::Router()->SetRoute('^awards(/?.*)$',
														AWARDS_PLUGIN_BASE_URL . '$1',
														'Internal');

		// Create Database Objects needed by the Plugin
		require('install/awards.schema.php');
		AwardsSchema::Install();
	}

	/**
	 * Plugin cleanup on Disable.
	 */
	public function OnDisable() {
		// Remove the Routes created by the Plugin.
		Gdn::Router()->DeleteRoute('^awards(/?.*)$');
	}

	/**
	 * Plugin cleanup on Remove.
	 */
	public function CleanUp() {
		// Remove Plugin's configuration parameters
		RemoveFromConfig('Preferences.Email.' . self::ACTIVITY_AWARDEARNED);
		RemoveFromConfig('Preferences.Popup.' . self::ACTIVITY_AWARDEARNED);
		RemoveFromConfig('Plugin.Awards.MinSearchLength');

		$this->RemoveAwardsActivityTypes();

		require('install/awards.schema.php');
		AwardsSchema::Uninstall();
	}
}

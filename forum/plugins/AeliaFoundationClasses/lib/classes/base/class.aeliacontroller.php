<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

use \Gdn;
use \Aelia\AFC\Definitions;

/**
 * Base Controller class. It implements a few features which can be reused by
 * descendant controllers.
 */
class Controller extends \Gdn_Controller {
	// @var Logger The Logger used by the class.
	private $_Log;

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = \LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}

   /**
    * Checks that the user has the specified permissions.
    *
    * @param mixed Permission A permission or array of permission names required to access this resource.
    * @param bool FullMatch If $Permission is an array, $FullMatch indicates if all permissions specified are required. If false, the user only needs one of the specified permissions.
    * @param string $JunctionTable The name of the junction table for a junction permission.
    * @param in $JunctionID The ID of the junction permission.
    */
	protected function CheckPermission($Permission, $FullMatch = TRUE, $JunctionTable = '', $JunctionID = '') {
		$Session = \Gdn::Session();
		return $Session->IsValid() && $Session->CheckPermission($Permission, $FullMatch, $JunctionTable, $JunctionID);
	}

	/**
	 * Class constructor.
	 *
	 * @return AeliaBaseController
	 */
	public function __construct() {
		parent::__construct();
		$this->Form = new \Aelia\Form();
	}

	/**
	 * Factory method.
	 *
	 * @return \Aelia\Controller
	 */
	public static function Factory() {
		$Class = get_called_class();
		return new $Class();
	}

	/**
	 * Sets the HTTP status code if the received request was sent via Ajax.
	 *
	 * @param int HttpCode The HTTP code to set.
	 * @param string Message The message to return with the HTTP code.
	 */
	protected function SetAjaxHttpCode($HttpCode, $Message = null) {
		if($this->IsAjaxRequest()) {
			$this->StatusCode($HttpCode, $Message);
		}
	}

	/**
	 * Returns an exception. The error message and HTTP code are extracted from
	 * the exception passed as a parameter.
	 *
	 * @param Exception Exception The exception to return.
	 */
	protected function ReturnException(\Exception $Exception) {
		if($this->IsAjaxRequest()) {
			$this->ErrorMessage($Exception->getMessage());
			$this->SetAjaxHttpCode($Exception->getCode());
		}
		else {
			$this->RenderException($Exception);
			exit;
		}
	}

	/**
	 * Returns a "404-Not found" error.
	 */
	protected function ReturnNotFound() {
		$Exception = NotFoundException();
		$this->ReturnException($Exception);
	}

	/**
	 * Returns a "Permission denied" error.
	 */
	protected function ReturnPermissionDenied() {
		if(!Gdn::Session()->IsValid() && $this->DeliveryType() == DELIVERY_TYPE_ALL) {
			Redirect('/entry/signin?Target=' . urlencode($this->SelfUrl));
		}
		else {
			Gdn::Dispatcher()->Dispatch('DefaultPermission');
			exit();
		}
	}

	/**
	 * Sets several parameters that will make the controller run within the
	 * dashboard template.
	 */
	protected function InitializeForDashboard() {
		$this->Head = new \HeadModule($this);

		// Set view and CSS to the dashboard ones
		$this->AddCssFile('admin.css');
		$this->MasterView = 'admin';
	}

	/**
	 * This is a good place to include JS, CSS, and modules used by all methods of
	 * this controller. Always called by dispatcher before controller's requested
	 * method.
	 */
	public function Initialize() {
		if(!method_exists($this, $this->OriginalRequestMethod)) {
			$this->Log()->Debug(sprintf(T('Invalid request method: "%s".'), $this->OriginalRequestMethod));
			$this->ReturnNotFound();
		}

		parent::Initialize();
	}

	/**
	 * Ensures that information and error messages are passed to the view to be
	 * rendered, then calls the standard rendering method.
	 */
	protected function Render() {
		// Determine if user should be redirected, or if the page for current
		// controller action should be rendered
		if(!empty($this->RedirectDestination)) {
			$this->Log()->trace(sprintf(T('RedirectLog', 'Redirecting user to URL "%s".'),
																	$this->RedirectDestination));
			$MessagesKey = $this->StashMessages();
			$RedirectDestination = $this->AddMessagesKey($this->RedirectDestination, $MessagesKey);
			Redirect($RedirectDestination);
		}
		else {
			$this->LoadStashedMessages();
		}

		// If the call was made via Ajax, add the information and error messages
		// to the result
		if($this->DeliveryType() == DELIVERY_TYPE_DATA) {
			if(!empty($this->_InformMessages)) {
				$this->SetData('InformMessages', $this->_InformMessages);
			}
			if(!empty($this->_ErrorMessages)) {
				$this->SetData('ErrorMessages', $this->_ErrorMessages);
			}
		}
		parent::Render();
	}

	/**
	 * Build and add the Dashboard's side navigation menu.
	 *
	 * @param string CurrentUrl Used to highlight correct route in menu.
	 */
	public function AddSideMenu($CurrentUrl = FALSE) {
		if(!$CurrentUrl) {
			$CurrentUrl = strtolower($this->SelfUrl);
		}

		// Only add to the assets if this is not a view-only request
		if($this->_DeliveryType == DELIVERY_TYPE_ALL) {
			// Configure SideMenu module
			$SideMenu = new \SideMenuModule($this);
			$SideMenu->HtmlId = '';
			$SideMenu->HighlightRoute($CurrentUrl);
			$SideMenu->Sort = C('Garden.DashboardMenu.Sort');

			// Hook for adding to menu
			$this->EventArguments['SideMenu'] = $SideMenu;
			$this->FireEvent('GetAppSettingsMenuItems');

			// Add the module
			$this->AddModule($SideMenu, 'Panel');
		}
	}

	/**
	 * Returns the value of a cookie stored using BaseController::SetCookie().
	 *
	 * @param string Name The cookie name.
	 * @param mixed DefaultValue The default value to return if cookie is not found.
	 * @return mixed
	 * @see \Aelia\AFC\Controller::SetCookie()
	 */
	protected function GetCookie($Name, $DefaultValue = null) {
		$Value = GetValue($Name, $_COOKIE, $DefaultValue);
		if(!empty($Value)) {
			$Value = json_decode($Value);
		}

		return $Value;
	}

	/**
	 * Deletes a cookie.
	 *
	 * @param string Name The cookie name.
	 */
	protected function DeleteCookie($Name) {
		$this->SetCookie($Name, null, time() - 3600);
	}

	/**
	 * Stores an arbitrary object in a cookie. The main difference between this
	 * method and the standard set_cookie() is that it allows to store object and
	 * arrays, which are converted to JSON.
	 *
	 * @param string Name The cookie name.
	 * @param mixed Value The object to store.
	 * @param int $Expiration The cookie expiration timestamp, in seconds since
	 * Unix epoch.
	 * @link http://php.net/manual/en/function.setcookie.php
	 */
	protected function SetCookie($Name, $Value, $Expiration = 0, $HTTPOnly = true, $Secure = false, $Path = null, $Domain = null) {
		if(!is_numeric($Expiration)) {
			throw new InvalidArgumentException(sprintf(T('Cookie expiration is not a valid number: "%s".'), $Expiration));
		}

		if(is_null($Path)) {
			$Path = \Gdn::Config('Garden.Cookie.Path', '/');
		}

    if(is_null($Domain)) {
			$Domain = \Gdn::Config('Garden.Cookie.Domain', '');
		}

		setcookie($Name, json_encode($Value), (int)$Expiration, $Path, $Domain, $Secure, $HTTPOnly);
	}

	/**
	 * Adds the JavaScript files required by the controller.
	 *
	 * @since 14.07.19.001
	 */
	protected function AddJsFiles() {
		// Dummy
	}

	/**
	 * Adds the CSS files required by the controller.
	 *
	 * @since 14.07.19.001
	 */
	protected function AddCssFiles() {
		// Dummy
	}

	/**
	 * Determines if the request received by a controller was sent via Ajax.
	 *
	 * @return bool
	 * @since 14.07.19.001
	 */
	protected function IsAjaxRequest() {
		return InArrayI($this->DeliveryMethod(), array(DELIVERY_METHOD_JSON, DELIVERY_METHOD_XML));
	}

	/**
	 * Sets the destination URL where the user should be redirected.
	 *
	 * @param string URL The destination URL.
	 * @since 14.07.19.001
	 */
	protected function SetRedirect($URL) {
		$this->RedirectDestination = $URL;
	}

	/**
	 * Stashes the messages to be shown to the user. This method is used when a
	 * user is being redirected to another page, so that the messages can be
	 * displayed correctly when the destination page is rendered.
	 *
	 * @return string The key to be used to retrieve the messages from user's
	 * session.
	 */
	protected function StashMessages() {
		$Key = sha1(uniqid(Gdn::Session()->UserID, true));

		$Messages = array(
			'inform' => $this->_InformMessages,
			'errors' => $this->_ErrorMessages,
		);

		Gdn::Session()->Stash($Key, $Messages);

		return $Key;
	}

	/**
	 * Adds a message key to a URL. The message key will be used to retrieve the
	 * messages eventually stashed in user's session.
	 *
	 * @param string URL The URL to modify.
	 * @param string MessagesKey The key to add to the URL.
	 * @return string
	 */
	protected function AddMessagesKey($URL, $MessagesKey) {
		$Query = parse_url($URL, PHP_URL_QUERY);

		$URL .= empty($Query) ? '?' : '&';
		$URL .= Definitions::ARG_MESSAGES_KEY . '=' . $MessagesKey;

		return $URL;
	}

	/**
	 * Loads the messages stashed in user's session and stores them in the internal
	 * properties.
	 */
	protected function LoadStashedMessages() {
		$MessagesKey = Gdn::Request()->GetValue(Definitions::ARG_MESSAGES_KEY);
		if(empty($MessagesKey)) {
			return;
		}
		$Messages = Gdn::Session()->Stash($MessagesKey, null, true);

		$InformMessages = GetValue('inform', $Messages);
		if(is_array($InformMessages)) {
			$this->_InformMessages = array_merge($this->_InformMessages, $InformMessages);
		}

		$ErrorMessages = GetValue('errors', $Messages);
		if(is_array($ErrorMessages)) {
			$this->_ErrorMessages = array_merge($this->_ErrorMessages, $ErrorMessages);
		}
	}

	/**
	 * Determines where to go after executing a controller method.
	 *
	 * @param string Target A URL where the user should be redirected.
	 * @param string DefaultTarget The default target if none is specified with
	 * Target parameter, nor is posted with the form, or passed with the request.
	 * @return string URL to actually go to (validated & safe).
	 */
	protected function Target($Target = null, $DefaultTarget = '/') {
		if(empty($Target)) {
			$Target = $this->Form->GetFormValue('Target', $this->Request->Get('Target', $DefaultTarget));
		}

		// If Target parameter, default target and target field are all empty, return
		// the original (empty) Target passed as a parameter
		if(empty($Target)) {
			return $Target;
		}

		// If target is not an absolute URL, consider it a relative one
		if(!preg_match('`(^https?://)`', $Target)) {
			return Url('/' . ltrim($Target), true);
		}

		$MyHostname = parse_url(Gdn::Request()->Domain(), PHP_URL_HOST);
		$TargetHostname = parse_url($Target, PHP_URL_HOST);

		// Only allow external redirects to trusted domains.
		$TrustedDomains = C('Garden.TrustedDomains');
		if(!is_array($TrustedDomains)) {
			$TrustedDomains = array();
		}

		// Add this domain to the trusted hosts
		$TrustedDomains[] = $MyHostname;

		$Sender->EventArguments['TrustedDomains'] = &$TrustedDomains;
		$this->FireEvent('BeforeTargetReturn');

		// Loop the trusted domains looking for a match
		if(InArrayI($TargetHostname, $TrustedDomains)) {
			return $Target;
		}
		else {
			return '';
		}
	}
}

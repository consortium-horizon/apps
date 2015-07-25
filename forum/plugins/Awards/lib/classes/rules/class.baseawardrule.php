<?php if(!defined('APPLICATION')) exit();


/* simple_html_dom library is required to manipulate Form fields, to allow
 * grouping Rule fields together
 */
require(AWARDS_PLUGIN_EXTERNAL_PATH . '/simple_html_dom/simple_html_dom.php');

interface IAwardRule {
	// @see BaseAwardRule::Process();
	public function IsRuleEnabled(stdClass $Settings);
	public function Process($UserID, stdClass $Settings, array $EventInfo = null);
	public function GetConfigUI(Gdn_Controller $Sender);
	public function ValidateSettings(Gdn_Form $Form, array $Settings);

	public static function RenderRuleField($InputHTML);
	public static function RenderMissingRequirements(array $MissingRequirements);
}

/**
 * Base Award Assignment Rule Class.
 */
class BaseAwardRule extends Gdn_Controller implements IAwardRule {
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

	/* @var array Contains the types of calculation available for the Rule
	 * - At: Rule returns true when the specified threshold is reached.
	 * - Every: Rule returns true whenever the specified amount of content type is reached.
	 *
	 * Note: variable cannot be initialized on declaration because it makes use of
	 * T() for translations.
	 */
	public static $CountTypes;

	// @var int Indicates that the Rule is enabled and should be processed.
	const RULE_ENABLED = 0;
	// @var int Indicates that the Rule is disabled and should not be processed.
	const RULE_DISABLED = 1;
	// @var int Indicates that the Rule is enabled and should be processed, but it cannot be due to a lack of requirements (e.g. missing plugins)
	const RULE_ENABLED_CANNOT_PROCESS = 2;

	// @var Gdn_Validation Internal validator, used to validate Rule settings.
	protected $Validation;

	// @var int Indicates that an Award should not be assigned, as the Rule checks did not pass.
	const NO_ASSIGNMENTS = 0;
	// @var int Indicates that an Award should be assigned once, as the Rule checks did pass.
	const ASSIGN_ONE = 1;

	// @var array A list of the plugins required by the Rule.
	protected $_RequiredPlugins = array();
	// @var array A list of error messages indicating the missing requirements.
	protected $_MissingRequirements = array();

	/**
	 * Retrieves and stores User data using UserModel.
	 *
	 * @param int UrerID The UserID to use as a key to retrieve the data.
	 * @return stdClass An object containing User Data.
	 * @see UserModel::GetID()
	 */
	protected function GetUserData($UserID) {
		if(!isset($this->_UserData) || ($UserID != GetValue('_CurrentUserID', $this))) {
			$UserModel = new UserModel();

			$this->_CurrentUserID = $UserID;
			$this->_UserData = $UserModel->GetID($UserID);
		}

		return $this->_UserData;
	}

	/**
	 * Runs the processing of the Rule, which will return how many times the Award
	 * should be assigned to the User, based on the specified configuration.
	 *
	 * @param int UserID The ID of the User candidated to receive an Award.
	 * @param stdClass Settings The settings to be applied to the Rule, passed as
	 * an object.
	 * @param array EventInfo Additional information passed with the event that
	 * triggered the processing of Awards.
	 * @return int A number indicating how many times the Award should be assigned
	 * to the User, based on the logic of the rule.
	 */
	protected function _Process($UserID, stdClass $Settings, array $EventInfo = null) {
		return self::NO_ASSIGMENTS;
	}

	/**
	 * Runs the processing of the Rule, which will return how many times the Award
	 * should be assigned to the User, based on the specified configuration.
	 *
	 * NOTE: The actual processing occurs in method BaseAwardRule::_Process().
	 * This method exists so that BaseAwardRule::IsRuleEnabled() is called before
	 * the actual processing. It's true that IsRuleEnabled is normally called by
	 * the RulesManager anyway, but it's double checked here for safety (processing
	 * a rule by mistake could give unpredictable results).
	 *
	 * @param int UserID The ID of the User candidated to receive an Award.
	 * @param stdClass Settings The settings to be applied to the Rule, passed as
	 * an object.
	 * @param array EventInfo Additional information passed with the event that
	 * triggered the processing of Awards.
	 * @return int A number indicating how many times the Award should be assigned
	 * to the User, based on the logic of the rule.
	 */
	public function Process($UserID, stdClass $Settings, array $EventInfo = null) {
		if($this->IsRuleEnabled($Settings) != self::RULE_ENABLED) {
			$this->Log()->error(sprintf(T('Processing of rule "%s" was called even though ' .
																		'the Rule was not enabled. Please check the Award ' .
																		'configuration see what the Rule requirements are ' .
																		'and make sure that it is configured correctly. If the ' .
																		'error persists, please contact Support. Current Rule settings: "%s".'),
																	get_called_class(),
																	json_encode($Settings)
																	));
			return null;
		}

		return $this->_Process($UserID, $Settings, $EventInfo);
	}

	/**
	 * Checks if the Rule requirements are satisfied. If not, adds a list of
	 * missing requirements to Sender's data, so that the Rule View can display
	 * them to the User.
	 *
	 * @param Gdn_Controller Sender The Controller which is rendering the page.
	 */
	private function AddMissingRequirements(Gdn_Controller $Sender) {
		if($this->_CheckRequirements() === false) {
			$MissingRequirements = $Sender->Data('MissingRuleRequirements', array());
			$MissingRequirements[get_called_class()] = $this->_MissingRequirements;
			$Sender->SetData('MissingRuleRequirements', $MissingRequirements);
		}
	}

	/**
	 * Retrieves and returns the name of the View used to render the configuration
	 * interface for the Rule.
	 *
	 * @param Gdn_Controller Sender The Controller which is rendering the page.
	 * @return string The name of the View used to render the configuration
	 * interface for the Rule.
	 */
	public function GetConfigUI(Gdn_Controller $Sender) {
		// Before passing the View to the Sender, add any eventual error message
		// related to missing requirements
		$this->AddMissingRequirements($Sender);

		// Return the full path and file name of the view, and let the caller load it.
		// This will allow the View to be loaded in the caller's context, and access
		// all the exposed data
		$Reflector = new ReflectionClass(get_class($this));
		return dirname($Reflector->getFileName()) . '/views/settings_view.php';
	}

	/**
	 * Replaces aninput name with a hierarchical name, to allow grouping inputs
	 * once they are submitted.
	 * The result will be a name attribute such as Rules[RuleClass][GroupName][FieldName].
	 *
	 * @param string FieldName The name of the field to rename. It can be a simple
	 * field name (e.g. "MyField") or a hierarchical name. In latter case, the
	 * hierarchy must be indicated by underscores (e.g. Group_Field, Group_Subgroup_Field).
	 * @return string The new field name, in format "Rules[RuleClass][GroupName][FieldName]".
	 */
	protected static function RenameRuleField($FieldName) {
		// If FieldName ends with brackets, it means that it's an array field. In
		// such case, the brackets must be removed before the field is renamed. They
		// will be added again later
		$ArrayFieldBracketsPos = strrpos($FieldName, '[]');
		if($ArrayFieldBracketsPos !== false) {
			$FieldName = substr($FieldName, 0, $ArrayFieldBracketsPos);
		}
		$FieldIsArray = ($ArrayFieldBracketsPos !== false);


		/* Split the field into its sub-parts. Rule field names should be declared
		 * as follows:
		 * - Simple fields - MyField, SomeField, etc.
		 * - Grouped fields - Group_Field1, Group_Field2, etc. Separator must be an
		 *   underscore.
		 */
		$FieldNameParts = explode('_', $FieldName);

		/* Reformat Field Name by adding two prefixes:
		 * - "Rules". This prefix will group all the Rules fields under a single
		 *   array.
		 * - Class Name. This prefix will group all the fields belonging to this
		 *   Rule under their own array.
		 *
		 * The result will be a field named as follows:
		 * Rules[RuleName][GroupName][FieldName]
		 *
		 * Using this convention, PHP will automatically create nested array of
		 * fields and pass them into the $_POST variable. This will allow to group
		 * all the fields belonging to a specific Rule without having to search for
		 * them by using substring. Also, appending the Rule class to which the fields
		 * belong will automatically resolve any ambiguity, allowing all Rules to
		 * name their fields as they like.
		 */
		$NewFieldName = 'Rules[' . get_called_class() . '][' . implode('][', $FieldNameParts) . ']';

		// If field is an array, add back the brackets that were removed previously
		if($FieldIsArray) {
			$NewFieldName .= '[]';
		}

		return $NewFieldName;
	}

	/**
	 * Processes the HTML generated by one of the Gdn_Form::Input() methods and
	 * replaces the names of the inputs with a hierarchical name, to allow grouping
	 * them once they are submitted.
	 * The result will be a name attribute such as Rules[RuleClass][GroupName][FieldName].
	 *
	 * @param string InputHTML The HTML generated by a Gdn_Form::Input() function.
	 * @return string The original HTML, with the replaced names.
	 */
	public static function RenderRuleField($InputHTML) {
		$HTMLObj = str_get_html($InputHTML);
		foreach($HTMLObj->find('input[name],select[name]') as $Input) {
			// For each Checkbox, Garden framework adds a hidden field named
			// "Checkboxes[]", to keep track of all checkboxes, whether they have been
			// ticked or not. For such field, the name that we have to replace is stored
			// in its "value" attribute.
			if(strcasecmp($Input->name, 'Checkboxes[]') == 0){
				$InputAttribute = 'value';
			}
			else {
				$InputAttribute = 'name';
			}

			// The last element of InputNameParts contains the Field Name to process
			$InputNameParts	= explode('/', $Input->{$InputAttribute});
			$FieldName = array_pop($InputNameParts);

			// Process field name by transforming it into a hierarchical name
			$InputNameParts[] = self::RenameRuleField($FieldName);


			// Replace the name in the processed HTML Input element
			$Input->{$InputAttribute} = implode('/', $InputNameParts);
		}

		echo $HTMLObj;
	}

	/**
	 * Given a list of Missing Rule Requirements, it produces the HTML to display it.
	 *
	 * @param array MissingRequirements A list of missing requirements.
	 */
	public static function RenderMissingRequirements(array $MissingRequirements) {
		if(!empty($MissingRequirements)) {
			echo '<div class="MissingRequirements Warning">';
			echo Wrap(T('Missing Requirements'),
								'h5',
								array('title' => T('A rule with missing requirements cannot run. ' .
																	 'If such Rule is enabled, the Award containing it ' .
																	 'will not be assigned until the Rule requirements ' .
																	 'are satisfied.')));
			echo '<ul>';

			foreach($MissingRequirements as $MissingReq) {
				echo Wrap($MissingReq, 'li');
			}
			echo '</ul>';
			echo '</div>';
		}
	}

	/**
	 * Applies a validation rule to a field posted through a Form. This function
	 * calls Gdn_Validation::ApplyRule(), but first it renames the field to match
	 * the name generated by BaseAwardRule::RenderRuleField() function.
	 *
	 * @see Gdn_Validation::ApplyRule().
	 * @see BaseAwardRule::RenderRuleField().
	 */
	protected function ApplyRule($FieldName, $RuleName, $CustomError = '') {
		$FieldName = $this->RenameRuleField($FieldName);

		$this->Validation->ApplyRule($FieldName, $RuleName, $CustomError);
	}

	/**
	 * Validates Rule Settings.
	 * @throws A "not implemented" Exception. This method must be implemented by
	 * descendat classes.
	 */
	protected function _ValidateSettings() {
		throw new Exception(T('Not implemented. Descendant classes must implement this method.'));
	}

	/**
	 * Validates Rule settings. This method is a wrapper around
	 * BaseAwardRule::_ValidateSettings(), which is the method that actually
	 * performs the validation.
	 *
	 * @param Gdn_Form Form The Form which will contain the validation results.
	 * @param array Settings The Rule Settings to validate.
	 * @return bool True, if Validation was successful, False otherwise.
	 */
	public function ValidateSettings(Gdn_Form $Form, array $Settings) {
		$this->_ValidateSettings($Settings);

		$Form->SetValidationResults($this->Validation->Results());

		return (count($this->Validation->Results()) == 0);
	}

	/**
	 * Checks if Rule should be enabled, based on the Settings.
	 * @throws A "not implemented" Exception. This method must be implemented by
	 * descendat classes.
	 */
	protected function _IsRuleEnabled(stdClass $Settings) {
		throw new Exception(T('Not implemented. Descendant classes must implement this method.'));
	}

	/**
	 * Checks if the Rule is enabled, based on the settings and other criteria.
	 * Descendant classes must implement this method.
	 *
	 * @param stdClass Settings An object containing settings for the Rule.
	 * @return int An integer value indicating if the Rule should is enabled.
	 * Possible return values are:
	 * - BaseAwardRule::RULE_ENABLED
	 * - BaseAwardRule::RULE_DISABLED
	 * - BaseAwardRule::RULE_ENABLED_CANNOT_PROCESS
	 */
	public function IsRuleEnabled(stdClass $Settings) {
		$Result = $this->_IsRuleEnabled($Settings);

		// If Rule is enabled, check that it can actually run, based on its
		// requirements
		if($Result == self::RULE_ENABLED) {
			if($this->_CheckRequirements() === false) {
				return self::RULE_ENABLED_CANNOT_PROCESS;
			}
		}

		return $Result;
	}

	/**
	 * Checks if all the Requirements needed by the rule are present. Base method
	 * always returns True, it's up to the descendant Rules to implement it.
	 *
	 * @return bool True, if all Requirements are satisfied, False otherwise.
	 */
	protected function _CheckRequirements() {
		$this->_MissingRequirements = array();

		$PluginManager = Gdn::PluginManager();
		// Check that all required plugins are installed and enabled
		foreach($this->_RequiredPlugins as $RequiredPlugin) {
			if($PluginManager->CheckPlugin($RequiredPlugin) === false) {
				$this->_MissingRequirements[] = sprintf(T('Missing Plugin: "%s".'),
																								$RequiredPlugin);
			}
		}

		return empty($this->_MissingRequirements);
	}

	/**
	 * Processes the settings to be used by the rule, eventually adding extra
	 * information that may be required.
	 *
	 * @param array Settings An associative array of Rule Settings.
	 * @return array An associative array of Rule Settings.
	 */
	public function PrepareSettings(array $Settings) {
		return $Settings;
	}

	/**
	 * Class constructor.
	 *
	 * @return BaseController An Instance of Base Controller.
	 */
	public function __construct() {
		parent::__construct();
		$this->Validation = new Gdn_Validation();
		$this->_RequiredPlugins = array();
		$this->_MissingRequirements = array();

		self::$CountTypes = array(1 => T('At'),
															2 => T('Every'),);
	}
}

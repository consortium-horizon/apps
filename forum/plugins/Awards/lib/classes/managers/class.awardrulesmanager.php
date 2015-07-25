<?php if(!defined('APPLICATION')) exit();


/**
 * Manages the list of all available Rules and provides convenience
 * functions to retrieve the Model, Validation and View for each one.
 */
class AwardRulesManager extends BaseManager {
	// @var array Contains a list of all available Rules.
	private static $Rules = array();

	/* @var array Contains a list of the Rule directories that have been processed
	 * and whose rule has been loaded.
	 */
	private $RulesDirs = array();

	const GROUP_GENERAL = 'general';
	const GROUP_CUSTOM = 'custom';

	const TYPE_CONTENT = 'content';
	const TYPE_USER = 'user';
	const TYPE_MISC = 'misc';

	public static $RuleGroups = array();
	public static $RuleTypes = array();

	/**
	 * Registers a Rule to the array of available Rules.
	 *
	 * @param string RuleClass The name of the Rule Class.
	 * @param array An associative array of Rule Information.
	 * @throws An Exception if the Rule Class doesn't exist.
	 */
	public static function RegisterRule($RuleClass, array $RuleInfo) {
		self::$Rules[$RuleClass] = $RuleInfo;

		$Group = GetValue('Group', self::$Rules[$RuleClass]);
		// If a Rule Group is not specified, or it's not valid, assign the Rule to
		// the "General" group
		if(empty($Group) || !isset(self::$RuleGroups[$Group])) {
			self::$Rules[$RuleClass]['Group'] = self::GROUP_GENERAL;
		}

		$Type = GetValue('Type', self::$Rules[$RuleClass]);
		// If a Rule Type is not specified, or it's not valid, assign the Rule to
		// the "Miscellaneous" type
		if(empty($Type) || !isset(self::$RuleTypes[$Type])) {
			self::$Rules[$RuleClass]['Type'] = self::TYPE_MISC;
		}

		// Store the file name where the Rule class was declared
		$Reflector = new ReflectionClass($RuleClass);
		self::$Rules[$RuleClass]['File'] = $Reflector->getFileName();
	}

	/**
	 * Returns the Rule Information array associated to a Rule Class.
	 *
	 * @param string RuleClass The Rule Class for which to retrieve the
	 * information.
	 * @return array|null An associative array of Rule Information, or null, if
	 * the Rule Class could not be found.
	 */
	public function GetRuleInfo($RuleClass) {
		return GetValue($RuleClass, self::$Rules, null);
	}

	/**
	 * Getter for Rules property.
	 *
	 * @return array The value of Rules property.
	 */
	public function GetRules() {
		return self::$Rules;
	}

	/**
	 * Returns the instance of a previously loaded Rule.
 	 *
	 * @param string RuleClass The Rule Class for which to retrieve the instance.
	 * @return $RuleClass An instance of the specified Rule Class.
	 * @throws InvalidArgumentException if the Rule Class is not registered.
 	 */
	protected function GetRuleInstance($RuleClass) {
		if(!$this->RuleExists($RuleClass)) {
			$this->Log()->error($ErrorMsg = sprintf(T('Requested instance for invalid class: %s.',
																							$RuleClass)));
			throw new InvalidArgumentException($ErrorMsg);
		}
		// Return the instance stored during the loading of Rule Classes
		return self::$Rules[$RuleClass]['Instance'];
 	}

	/**
	 * Loads a rule class and stores its instance for later use.
	 *
	 * @param Rule The Class of the Rule.
	 * @return void.
	 */
	protected function LoadRule($RuleClass) {
		// Instantiate the Rule to have it readily available when required. This will
		// also prevent the need of instantiating the same rule multiple times
		self::$Rules[$RuleClass]['Instance'] = new $RuleClass();
	}

	/**
	 * Install in Vanilla's Factories all auxiliary classes for available Rule
	 * Classes.
	 *
	 * @return void.
	 */
	protected function LoadRules() {
		//var_dump(self::$Rules);
		foreach(self::$Rules as $RuleClass => $RuleInfo) {
			$this->LoadRule($RuleClass);
		}
	}

	/**
	 * Checks if a Rule Class exists in the list of the configured ones.
	 *
	 * @param RuleClass The Rule class to be checked.
	 * @return True if the class exists in the list of configured Rules, False otherwise.
	 */
	function RuleExists($RuleClass) {
		return array_key_exists($RuleClass, self::$Rules);
	}

	/**
	 * Checks if the specified file name is a valid directory (i.e. it is a
	 * directory, but not "." or "..").
	 *
	 * @param string Directory The directory where the file is located.
	 * @param string FileName The file name to check.
	 * @return bool True if the specified FileName is a directory, False if it is
	 * not a directory, or if it is "." or "..".
	 */
	private function IsValidDirectory($Directory, $FileName) {
		return ($FileName !== '.') &&
					 ($FileName !== '..') &&
					 (is_dir($Directory . '/' . $FileName));
	}

	/**
	 * Loads all Rule files found in the specified folder.
	 *
	 * @param string RulesDir The folder where to look for Rule files.
	 * @return bool False, if directory doesn't exist or could not be opened, True
	 * if it exist and could be opened (regardless if any Rule file was loaded).
	 */
	private function LoadRuleFiles($RulesDir) {
		$Handle = opendir($RulesDir);
		if(empty($Handle)) {
			return false;
		}

		// Load all Rule Files, so that they can add themselves to the list of
		// installed Rules
    while($File = readdir($Handle)) {
      if(!is_dir($File) && preg_match('/^class\..+?rule/i', $File) == 1) {
				include_once($RulesDir . '/' . $File);
			}
		}
		closedir($Handle);
		return true;
	}

	/**
	 * Scans the Rules directory for all appender files and loads them, so
	 * that they can add themselves to the list of available appenders.
	 *
	 * @return void.
	 */
	private function LoadRulesDefinitions($RulesSubDir = AWARDS_PLUGIN_CORE_RULES_DIR) {
		$this->Log()->trace(T('Loading Rules Definitions...'));
		$RulesDir = AWARDS_PLUGIN_RULES_PATH . '/' . $RulesSubDir;
		$Handle = opendir($RulesDir);
		if(empty($Handle)) {
			return false;
		}

		try {
			// Look for subfolders in Rules folder. Each Rule should be stored in its
			// SubFolder
			while($RuleDirName = readdir($Handle)) {
				if($this->IsValidDirectory($RulesDir, $RuleDirName)) {
					// If a Rule with the same name has already been loaded, skip this one
					if(isset($this->RulesDirs[$RuleDirName])) {
						$this->Log()->info(sprintf(T('A Rule folder named "%s" has already been ' .
																				 'processed and imported (path: "%s"). Skipping folder.')),
															 $RuleDirName,
															 $this->RulesDirs[$RuleDirName]);
						continue;
					}

					// Store the Rule folder amongst the processed ones
					$this->RulesDirs[$RuleDirName] = $RulesDir;
					$this->LoadRuleFiles($RulesDir . '/' . $RuleDirName);
				}
			}

			$this->Log()->trace(T('Done.'));
		}
		catch(Exception $e) {
			$ErrorMsg = sprintf(T('Exception occurred while loding Rule Definitions. Error: %s'),
													$e->getMessage());
			$this->Log()->error($ErrorMsg);
		}
		closedir($Handle);
	}

	/**
	 * Validates the settings for each of the Rules configured for an Award.
	 *
	 * @param Gdn_Form Form The form which contains the posted values.
	 * @return bool True, if validation is successful, False otherwise.
	 */
	public function ValidateRulesSettings(Gdn_Form $Form) {
		$RulesSettings = &$Form->GetFormValue('Rules');

		if(empty($RulesSettings)) {
			$Form->AddError(T('No Rules configured. Please enable and configure at least ' .
												'one Rule.'));
		}

		$Result = true;
		foreach($RulesSettings as $RuleClass => $Settings) {
			$RuleInstance = $this->GetRuleInstance($RuleClass);

			// Validate Rules settings and add the validation results to the form
			$Result = $Result && $RuleInstance->ValidateSettings($Form, $Settings);
		}

		return $Result;
	}

	/**
	 * Transforms the settings of Award Rules into a JSON string, which will then
	 * be saved with an Award.
	 *
	 * @param Gdn_Form Form The form containing the Rule settings.
	 * @return string The settings, converted to JSON.
	 */
	public function RulesSettingsToJSON(Gdn_Form $Form) {
		$FormRulesSettings = &$Form->GetFormValue('Rules');

		$Result = array();
		foreach($FormRulesSettings as $RuleClass => $Settings) {
			$RuleInstance = $this->GetRuleInstance($RuleClass);

			// Prepares the Settings for being saved, by allowing the Rule to which
			// they belong to add some extra information which was not passed by the
			// form
			$Result[$RuleClass] = $RuleInstance->PrepareSettings($Settings);
		}

		return json_encode($Result);
	}

	/**
	 * Processes a set of Rules to see if an Award should be assigned to a User
	 * and, in case, how many times it should be assigned. Multiple assigmnent in
	 * a single process has been implemented to support recurring Awards.
	 *
	 * @param int UserID The ID of the User candidate to receive the Award.
	 * @param array RulesSettings An array of Settings for each of the Rules to be
	 * processed.
	 * @return int A number indicating how many times the Award should be assigned
	 * to the User. Zero means no assignment.
	 */
	public function ProcessRules($UserID, array $RulesSettings) {
		$AwardAssignCounts = array();

		foreach($RulesSettings as $RuleClass => $Settings) {
			$this->Log()->debug(sprintf(T('Inspecting Rule %s...'), $RuleClass));

			// Retrieve the instance of the Rule to process
			$RuleInstance = $this->GetRuleInstance($RuleClass);

			// If Rule instance does not exist, log the fact and just skip it
			if(!isset($RuleInstance)) {
				$this->Log()->info(sprintf(T('Instance of Award Rule "%s" not found. Skipping Rule.'),
																	 $RuleClass));
				continue;
			}


			$this->Log()->debug(T('Checking if Rule is enabled...'));
			$IsRuleEnabled = $RuleInstance->IsRuleEnabled($Settings);
			// Check if the Rule is enabled
			switch($IsRuleEnabled) {
				// Disabled - Rule has not been configured and should not be processed
				case BaseAwardRule::RULE_DISABLED:
					$this->Log()->trace(sprintf(T('Rule "%s" disabled, skipping.'),
																			$RuleClass));
					// "continue 2" will break out of the switch and continue to next loop
					continue 2;
				case BaseAwardRule::RULE_ENABLED_CANNOT_PROCESS:
					$this->Log()->error(sprintf(T('Rule "%s" enabled, but could not be processed due to ' .
																				'missing requirements or misconfiguration. Award processing '.
																				'aborted. Please check the Award configuration for more ' .
																				'details on what could be misconfigured.'),
																			$RuleClass));
					return BaseAwardRule::NO_ASSIGNMENTS;
				case BaseAwardRule::RULE_ENABLED:
					// Do nothing and carry on
					break;
				default:
					$this->Log()->error(sprintf(T('Unexpected value returned by %s::IsRuleEnabled(): %s. ' .
																				'Award processing aborted.'),
																			$RuleClass,
																			$IsRuleEnabled));
					return BaseAwardRule::NO_ASSIGNMENTS;
			}
			//var_dump($Settings, $IsRuleEnabled);

			$this->Log()->debug(sprintf(T('Processing Rule "%s"...'), $RuleClass));

			/* Plugin architecture allows to configure recurring Awards (e.g. a
			 * "registration anniversary"). To handle them, each Rule returns the
			 * amount of time that the Award should be assigned to the User, based
			 * on the Rule's specific criteria. If User has been on the forum for
			 * 3 years, then a registration anniversary rule might return "3" (if it
			 * was never processed before), meaning that the Award should be
			 * assigned three times.
			 *
			 * If a Rule returns zero, then the Award cannot be assigned, there is
			 * no need to process other rules.
			 */
			$AwardAssignCountFromRule = $RuleInstance->Process($UserID, $Settings);

			$this->Log()->debug(sprintf(T('Rule returned %s.'), $AwardAssignCountFromRule));

			/* A Rule returning NULL means "Rule doesn't have to be processed". In
			 * such case, processing can continue.
			 */
			if($AwardAssignCountFromRule === null) {
				continue;
			}

			/* If a Rule returns zero, it means that Rule's conditions are not
			 * satisfied. Since an Award can be assigned only if ALL the Rules'
			 * conditions are satisfied, there is no point on processing the
			 * remaining ones.
			 */
			if($AwardAssignCountFromRule <= 0) {
				$AwardAssignCounts[] = BaseAwardRule::NO_ASSIGNMENTS;
				break;
			}

			/* Any other return value indicates how many times an Award should be
			 * assigned. This is useful for recurring Awards, which might have to
			 * be assigned multiple times (ag. anniversaries).
			 *
			 * The final result will be the minimum amount returned by the rules.
			 * This is because different Rules might return different amounts. For
			 * example, a rule might grant the Award 4 times, while another would
			 * only grant it 2 times. In such case, "2" would be acceptable for both
			 * rules.
			 */
			$AwardAssignCounts[] = $AwardAssignCountFromRule;
		}

		/* If $AwardAssignCounts is empty, it means that no rules have been
		 * processed. In such case, just return "NO_ASSIGNMENTS" to state that the
		 * Award doesn't have to be assigned.
		 */
		if(empty($AwardAssignCounts)) {
			return BaseAwardRule::NO_ASSIGNMENTS;
		}
		return min($AwardAssignCounts);
	}

	/**
	 * Renders the Rules List page.
	 *
	 * @param Gdn_Plugin Caller The Plugin which called the method.
	 * @param object Sender Sending controller instance.
	 */
	public function RulesList(Gdn_Plugin $Caller, $Sender) {
		$Sender->SetData('CurrentPath', AWARDS_PLUGIN_RULES_LIST_URL);
		// Prevent non authorised Users from accessing this page
		$Sender->Permission('Plugins.Awards.Manage');

		$Sender->SetData('Rules', $this->GetRules());

		$Sender->Render($Caller->GetView('awards_ruleslist_view.php'));
	}

	/**
	 * Constructor. It initializes the class and populates the list of available
	 * Rules.
	 */
	public function __construct() {
		parent::__construct();

		self::$RuleGroups = array(self::GROUP_GENERAL => T('General'),
															self::GROUP_CUSTOM => T('Custom'));

		self::$RuleTypes = array(self::TYPE_CONTENT => T('Content and Achievements'),
														 self::TYPE_USER => T('User'),
														 self::TYPE_MISC => T('Misc.'));

		/* Load Rules definitions, starting with Custom ones (if any). Rule folder
		 * names must be unique, regardless of their location (i.e. two "postcount"
		 * rule folders cannot be loaded), therefore this logic will allow Users to
		 * override Core rules by simply giving them the same name and placing them
		 * in the "/custom" sufolder.
		 */
		$this->LoadRulesDefinitions(AWARDS_PLUGIN_CUSTOM_RULES_DIR);
		$this->LoadRulesDefinitions(AWARDS_PLUGIN_CORE_RULES_DIR);
		$this->LoadRules();
	}
}

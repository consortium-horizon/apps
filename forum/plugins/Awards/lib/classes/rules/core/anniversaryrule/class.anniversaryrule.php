<?php if(!defined('APPLICATION')) exit();


/**
 * Anniversary Award Rule.
 */
class AnniversaryRule extends BaseAwardRule {
	/**
	 * Checks if the amount of Years passed since a User's first login is greater
	 * or equal than the threshold to assign the Award.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckYearsSinceFirstLogin($UserID, stdClass $Settings) {
		$YearsThreshold = $Settings->Anniversary->Years;
		$this->Log()->trace(sprintf(T('Checking "Anniversary" for User ID %d. Threshold: %d.'),
																$UserID,
																$YearsThreshold));

		// Calculate the time difference between now and User's first visit, in Years
		$User = $this->GetUserData($UserID);
		$Today = new DateTime(date('Y-m-d'));
		$UserFirstVisit = new DateTime($User->DateFirstVisit);

		$YearsSinceFirstVisit = (int)$Today->diff($UserFirstVisit)->format('%y');
		//var_dump($YearsSinceFirstVisit);

		//var_dump($this->GetUserData($UserID));
		if($YearsSinceFirstVisit >= $YearsThreshold) {
			$this->Log()->trace(T('Passed.'));
			return self::ASSIGN_ONE;
		}
		$this->Log()->trace(T('Failed.'));
		return self::NO_ASSIGNMENTS;
	}

	/**
	 * Runs the processing of the Rule, which will return how many times the Award
	 * should be assigned to the User, based on the specified configuration.
	 *
	 * @see AwardBaseRule::Process().
	 */
	protected function _Process($UserID, stdClass $Settings, array $EventInfo = null) {
		// Check Years since first login
		if(GetValue('Enabled', $Settings->Anniversary) == 1) {
			$Results[] = $this->CheckYearsSinceFirstLogin($UserID, $Settings);
		}

		//var_dump("AnniversaryRule Result: " . min($Results));
		return min($Results);
	}

	/**
	 * Validates Rule's settings.
	 *
	 * @param array Settings The array of settings to validate.
	 * @return bool True, if all settings are valid, False otherwise.
	 */
	protected function _ValidateSettings(array $Settings) {
		$Result = array();

		// Check settings for Anniversary
		$AnniversarySettings = GetValue('Anniversary', $Settings);

		$YearsThreshold = GetValue('Years', $AnniversarySettings);
		if(GetValue('Enabled', $AnniversarySettings) || !empty($YearsThreshold)) {
			if(!is_numeric($YearsThreshold) || ($YearsThreshold <= 0)) {
				$this->Validation->AddValidationResult('Anniversary_Years',
																							 T('Years threshold must be a positive integer.'));
			}
		}
		return (count($this->Validation->Results()) == 0);
	}

	/**
	 * Checks if the Rule is enabled, based on the settings and other criteria.
	 *
	 * @param stdClass Settings An object containing settings for the Rule.
	 * @return int An integer value indicating if the Rule should is enabled.
	 * Possible return values are:
	 * - BaseAwardRule::RULE_ENABLED
	 * - BaseAwardRule::RULE_DISABLED
	 * - BaseAwardRule::RULE_ENABLED_CANNOT_PROCESS
	 */
	protected function _IsRuleEnabled(stdClass $Settings) {
		if((GetValue('Enabled', $Settings->Anniversary) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return AnniversaryRule.
	 */
	public function __construct() {
		parent::__construct();
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'AnniversaryRule',
	array('Label' => T('Anniversary'),
				'Description' => T('Checks User\'s Anniversary of Registration'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_USER,
				// Version is for reference only
				'Version' => '13.04.03',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

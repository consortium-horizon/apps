<?php if(!defined('APPLICATION')) exit();


/**
 * Thanks Award Rule.
 *
 * Assigns an Award based on the Thanks received by a User.
 */
class ThanksRule extends BaseAwardRule {
	/**
	 * Checks if the User received enough Thanks to be assigned an Award based
	 * on such criteria.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserReceivedThanksCount($UserID, stdClass $Settings) {
		$ReceivedThanksThreshold = $Settings->ReceivedThanks->Amount;
		$this->Log()->trace(sprintf(T('Checking count of Received Thanks for User ID %d. Threshold: %d.'),
																$UserID,
																$ReceivedThanksThreshold));
		$UserData = $this->GetUserData($UserID);
		//var_dump($UserData);
		if(GetValue('ReceivedThankCount', $UserData, 0) >= $ReceivedThanksThreshold) {
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
		// Check Received Thanks Count
		if(GetValue('Enabled', $Settings->ReceivedThanks) == 1) {
			$Results[] = $this->CheckUserReceivedThanksCount($UserID, $Settings);
		}

		//var_dump("ThanksRule Result: " . min($Results));
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

		// Check settings for ReceivedThanks threshold
		$ReceivedThanksSettings = GetValue('ReceivedThanks', $Settings);
		$ReceivedThanksThreshold = GetValue('Amount', $ReceivedThanksSettings);
		if(GetValue('Enabled', $ReceivedThanksSettings)) {
			if(empty($ReceivedThanksThreshold) ||
				 !is_numeric($ReceivedThanksThreshold) ||
				 ($ReceivedThanksThreshold <= 0)) {
				$this->Validation->AddValidationResult('ReceivedThanks_Amount',
																							 T('Received Thanks threshold must be a positive integer.'));
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
		if((GetValue('Enabled', $Settings->ReceivedThanks) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return ThanksRule.
	 */
	public function __construct() {
		parent::__construct();

		$this->_RequiredPlugins[] = 'ThankfulPeople';
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'ThanksRule',
	array('Label' => T('Thanks'),
				'Description' => T('Checks "Thanks" received by the User'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_CONTENT,
				// Version is for reference only
				'Version' => '13.04.03',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

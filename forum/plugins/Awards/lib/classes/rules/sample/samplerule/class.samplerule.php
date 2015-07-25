<?php if(!defined('APPLICATION')) exit();



/**
 * Sample Award Rule.
 *
 * This rule doesn't do anything. Use it as the base of your own Rules.
 */
class SampleRule extends BaseAwardRule {
	/**
	 * Runs the processing of the Rule, which will return how many times the Award
	 * should be assigned to the User, based on the specified configuration.
	 *
	 * @param int UserID The ID of the User candidate to receive the Award.
	 * @param stdClass Settings An object containing the Settings for the Rule.
	 * @param array EventInfo Additional Event Data passed by an Event Handler. Not
	 * used as of 06/04/2013.
	 * @return int The amount of times the Award should be assigned to the User.
	 * A value of zero means that Rule did not pass, therefore the Award should not
	 * be assigned.
	 * @see AwardBaseRule::Process().
	 */
	protected function _Process($UserID, stdClass $Settings, array $EventInfo = null) {
		// GUIDE Perform the checks you need here, determining how many times an Award should be assigned to the User

		// GUIDE Replace NO_ASSIGNMENTS with the amount of times the Award should be assigned
		return self::NO_ASSIGNMENTS;
	}

	/**
	 * Validates Rule's settings.
	 *
	 * @param array Settings The array of settings to validate.
	 * @return bool True, if all settings are valid, False otherwise.
	 */
	protected function _ValidateSettings(array $Settings) {
		$Result = array();

		// GUIDE Check the settings entered for the Rule. If any of them is incorrect, add an error message to the Validation object. See example below.

		// A Rule can receive several settings. In this example, we are checking the
		// array with the key "Sample"
		$SampleSettings = GetValue('SampleSettings', $Settings);
		// The array should contain an "Enabled" field. If that is passed, then the
		// Rule is Enabled and further checks are required
		if(GetValue('Enabled', $SampleSettings)) {
			// Extract the value of "Amount" field from the settings
			$SampleThreshold = GetValue('Amount', $SampleSettings);

			// The Amount must be a positive integer to be valid
			if(empty($SampleThreshold) || !is_numeric($SampleThreshold) || ($SampleThreshold <= 0)) {
				$this->Validation->AddValidationResult('Sample_Amount',
																							 T('Sample Threshold must be a positive integer.'));
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
		// GUIDE Add your logic to determine if the Rule is enabled or not. See example below.

		//  Example: Rule is enabled if field SampleSettings->Enabled is set to "1"
		if((GetValue('Enabled', $Settings->SampleSettings) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return SampleRule.
	 */
	public function __construct() {
		parent::__construct();

		// GUIDE Add any required plugin to the _RequiresPlugins array. If any of the required plugins is not installed, or not enabled, the rule will not be processed (although it can still be configured from the Admin interface)
		$this->_RequiredPlugins[] = 'QnA';
	}
}

// Register the Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'SampleRule',
	array('Label' => T('Sample Rule'),
				'Description' => T('This is a sample rule. Use it as a skeleton for your own Rules.'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_CONTENT,
				// Version is for reference only
				'Version' => '13.04.04',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

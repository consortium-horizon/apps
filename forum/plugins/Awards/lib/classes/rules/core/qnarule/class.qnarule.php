<?php if(!defined('APPLICATION')) exit();


/**
 * QnA Award Rule.
 *
 * Assigns an Award based on the Questions and Answers performance of a User.
 */
class QnARule extends BaseAwardRule {
	/**
	 * Checks if the User received enough "Accept" to the Answers he posted to be
	 * assigned an Award based on such criteria.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserReceivedAcceptedAnswersCount($UserID, stdClass $Settings) {
		$ReceivedAcceptedAnswersThreshold = $Settings->ReceivedAcceptedAnswers->Amount;
		$this->Log()->trace(sprintf(T('Checking count of Received "Accepted Answer" for User ID %d . ' .
																	'Threshold: %d.'),
																$UserID,
																$ReceivedAcceptedAnswersThreshold));
		$UserData = $this->GetUserData($UserID);
		//var_dump($UserData);
		if(GetValue('QnACountAccept', $UserData, 0) >= $ReceivedAcceptedAnswersThreshold) {
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
		// Check Received "Accepted Answers" Count
		if(GetValue('Enabled', $Settings->ReceivedAcceptedAnswers) == 1) {
			$Results[] = $this->CheckUserReceivedAcceptedAnswersCount($UserID, $Settings);
		}

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

		// Check settings for ReceivedAcceptedAnswers threshold
		$ReceivedAcceptedAnswersSettings = GetValue('ReceivedAcceptedAnswers', $Settings);
		$ReceivedAcceptedAnswersThreshold = GetValue('Amount', $ReceivedAcceptedAnswersSettings);
		if(GetValue('Enabled', $ReceivedAcceptedAnswersSettings)) {
			if(empty($ReceivedAcceptedAnswersThreshold) ||
				 !is_numeric($ReceivedAcceptedAnswersThreshold) ||
				 ($ReceivedAcceptedAnswersThreshold <= 0)) {
				$this->Validation->AddValidationResult('ReceivedAcceptedAnswers_Amount',
																							 T('Received "Accepted Answers" threshold must be a positive integer.'));
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
		if((GetValue('Enabled', $Settings->ReceivedAcceptedAnswers) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return QnARule.
	 */
	public function __construct() {
		parent::__construct();

		$this->_RequiredPlugins[] = 'QnA';
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'QnARule',
	array('Label' => T('QnA (Questions & Answers)'),
				'Description' => T('Checks the "Accepted Answers" that the User has given or received'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_CONTENT,
				// Version is for reference only
				'Version' => '13.04.30',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

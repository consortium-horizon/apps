<?php if(!defined('APPLICATION')) exit();


/**
 * Post Count Award Rule.
 */
class PostCountRule extends BaseAwardRule {
	/**
	 * Checks if the User posted enough Discussions to be assigned an Award based
	 * on such criteria.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserDiscussionsCount($UserID, stdClass $Settings) {
		$DiscussionsThreshold = $Settings->Discussions->Amount;
		$this->Log()->trace(sprintf(T('Checking "CountDiscussions" for User ID %d. Threshold: %d.'),
																$UserID,
																$DiscussionsThreshold));
		//var_dump($this->GetUserData($UserID));
		if($this->GetUserData($UserID)->CountDiscussions >= $DiscussionsThreshold) {
			$this->Log()->trace(T('Passed.'));
			return self::ASSIGN_ONE;
		}
		$this->Log()->trace(T('Failed.'));
		return self::NO_ASSIGNMENTS;
	}

	/**
	 * Checks if the User posted enough Comments to be assigned an Award based
	 * on such criteria.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserCommentsCount($UserID, stdClass $Settings) {
		$CommentsThreshold = $Settings->Comments->Amount;
		$this->Log()->trace(sprintf(T('Checking "CountComments" for User ID %d. Threshold: %d.'),
																$UserID,
																$CommentsThreshold));
		//var_dump($this->GetUserData($UserID));
		if($this->GetUserData($UserID)->CountComments >= $CommentsThreshold) {
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
		// Check Discussion Count
		if(GetValue('Enabled', $Settings->Discussions) == 1) {
			$Results[] = $this->CheckUserDiscussionsCount($UserID, $Settings);
		}

		// Check Comment Count
		if(GetValue('Enabled', $Settings->Comments) == 1) {
			$Results[] = $this->CheckUserCommentsCount($UserID, $Settings);
		}

		//var_dump("PostCountRule Result: " . min($Results));
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

		// Check settings for Discussions threshold
		$DiscussionsSettings = GetValue('Discussions', $Settings);
		$DiscussionsThreshold = GetValue('Amount', $DiscussionsSettings);
		if(GetValue('Enabled', $DiscussionsSettings)) {
			if(empty($DiscussionsThreshold) ||
				 !is_numeric($DiscussionsThreshold) ||
				 ($DiscussionsThreshold <= 0)) {
				$this->Validation->AddValidationResult('Discussions_Amount',
																							 T('Discussions threshold must be a positive integer.'));
			}
		}

		// Check settings for Comments  threshold
		$CommentsSettings = GetValue('Comments', $Settings);
		$CommentsThreshold = GetValue('Amount', $CommentsSettings);
		if(GetValue('Enabled', $CommentsSettings)) {
			if(empty($CommentsThreshold) ||
				 !is_numeric($CommentsThreshold) ||
				 ($CommentsThreshold <= 0)) {
				$this->Validation->AddValidationResult('Comments_Amount',
																							 T('Comments threshold must be a positive integer.'));
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
		if((GetValue('Enabled', $Settings->Discussions) == 1) ||
			 (GetValue('Enabled', $Settings->Comments) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return PostCountRule.
	 */
	public function __construct() {
		parent::__construct();
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'PostCountRule',
	array('Label' => T('Post Count'),
				'Description' => T('Checks User\'s Post count'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_CONTENT,
				// Version is for reference only
				'Version' => '13.04.03',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

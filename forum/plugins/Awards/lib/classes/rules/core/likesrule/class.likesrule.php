<?php if(!defined('APPLICATION')) exit();


/**
 * Likes Award Rule.
 *
 * Assigns an Award based on the Likes received by a User.
 */
class LikesRule extends BaseAwardRule {
	/**
	 * Checks if the User received enough Likes to be assigned an Award based
	 * on such criteria.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserReceivedLikesCount($UserID, stdClass $Settings) {
		$ReceivedLikesThreshold = $Settings->ReceivedLikes->Amount;
		$this->Log()->trace(sprintf(T('Checking count of Received Likes for User ID %d. Threshold: %d.'),
																$UserID,
																$ReceivedLikesThreshold));
		$UserData = $this->GetUserData($UserID);
		//var_dump($UserData);
		if(GetValue('Liked', $UserData, 0) >= $ReceivedLikesThreshold) {
			$this->Log()->trace(T('Passed.'));
			return self::ASSIGN_ONE;
		}
		$this->Log()->trace(T('Failed.'));
		return self::NO_ASSIGNMENTS;
	}

	/**
	 * Checks User's Likes to Messages ratio.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserLikesToPostsRatio($UserID, stdClass $Settings) {
		$LikesToPostsThreshold = $Settings->LikesToPostsRatio->Amount;
		$this->Log()->trace(sprintf(T('Checking Likes to Posts Ratio for User ID %d. Threshold: %d.'),
																$UserID,
																$LikesToPostsThreshold));
		$UserData = $this->GetUserData($UserID);
		//var_dump($UserData);

		// Get total Posts (Discussions and Comments)
		$TotalPosts = (int)(GetValue('CountDiscussions', $UserData, 0) + GetValue('CountComments', $UserData, 0));
		// Get total Likes received
		$TotalReceivedLikes = (int)GetValue('Liked', $UserData, 0);
		// Check passes if Likes to Posts ratio is greater or equal to the specified one
		if(($TotalReceivedLikes / $TotalPosts) >= $LikesToPostsThreshold) {
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
		// Check Received Likes Count
		if(GetValue('Enabled', $Settings->ReceivedLikes) == 1) {
			$Results[] = $this->CheckUserReceivedLikesCount($UserID, $Settings);
		}

		// Check Likes to Posts Ratio
		if(GetValue('Enabled', $Settings->LikesToPostsRatio) == 1) {
			$Results[] = $this->CheckUserLikesToPostsRatio($UserID, $Settings);
		}

		//var_dump("LikesRule Result: " . min($Results));
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

		// Check settings for ReceivedLikes threshold
		$ReceivedLikesSettings = GetValue('ReceivedLikes', $Settings);
		$ReceivedLikesThreshold = GetValue('Amount', $ReceivedLikesSettings);
		if(GetValue('Enabled', $ReceivedLikesSettings)) {
			if(empty($ReceivedLikesThreshold) ||
				 !is_numeric($ReceivedLikesThreshold) ||
				 ($ReceivedLikesThreshold <= 0)) {
				$this->Validation->AddValidationResult('ReceivedLikes_Amount',
																							 T('Received Likes threshold must be a positive integer.'));
			}
		}

		// Check settings for LikesToPostsRatio threshold
		$LikesToPostsRatioSettings = GetValue('LikesToPostsRatio', $Settings);
		$LikesToPostsRatioThreshold = GetValue('Amount', $LikesToPostsRatioSettings);
		if(GetValue('Enabled', $LikesToPostsRatioSettings)) {
			if(empty($LikesToPostsRatioThreshold) ||
				 !is_numeric($LikesToPostsRatioThreshold) ||
				 ($LikesToPostsRatioThreshold <= 0)) {
				$this->Validation->AddValidationResult('LikesToPostsRatio_Amount',
																							 T('Likes to Posts  Ratio must be a positive floating point number.'));
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
		if((GetValue('Enabled', $Settings->ReceivedLikes) == 1) ||
			 (GetValue('Enabled', $Settings->LikesToPostsRatio) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return LikesRule.
	 */
	public function __construct() {
		parent::__construct();

		$this->_RequiredPlugins[] = 'LikeThis';
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'LikesRule',
	array('Label' => T('Likes'),
				'Description' => T('Checks "Likes" received by the User'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_CONTENT,
				// Version is for reference only
				'Version' => '13.04.04',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

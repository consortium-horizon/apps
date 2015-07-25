<?php if(!defined('APPLICATION')) exit();


/**
 * Photogenic Award Rule.
 *
 * Assigns an Award when User uploads a Profile picture.
 */
class PhotogenicRule extends BaseAwardRule {
	/**
	 * Checks if the User uploaded a Profile picture.
	 *
	 * @param int UserID The ID of the User.
	 * @param stdClass Settings The Rule settings.
	 * @return int "1" if check passed, "0" otherwise.
	 */
	private function CheckUserUploadedProfilePicture($UserID, stdClass $Settings) {
		$this->Log()->trace(sprintf(T('Checking if User uploaded a Profile picture. User ID %d'),
																$UserID));
		$UserData = $this->GetUserData($UserID);
		//var_dump($UserData);
		$UserPhoto = GetValue('Photo', $UserData);
		if(!empty($UserPhoto)) {
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
		// Check Received Photogenic Count
		if(GetValue('Enabled', $Settings->Photogenic) == 1) {
			$Results[] = $this->CheckUserUploadedProfilePicture($UserID, $Settings);
		}

		//var_dump("PhotogenicRule Result: " . min($Results));
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

		// No validation required

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
		if((GetValue('Enabled', $Settings->Photogenic) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'PhotogenicRule',
	array('Label' => T('Photogenic'),
				'Description' => T('Checks if User uploaded a Profile Picture'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_USER,
				// Version is for reference only
				'Version' => '13.04.04',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
				)
);

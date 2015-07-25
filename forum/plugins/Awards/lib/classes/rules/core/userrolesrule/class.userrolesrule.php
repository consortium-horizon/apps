<?php if(!defined('APPLICATION')) exit();


/**
 * UserRoles Award Rule.
 *
 * Assigns an Award when a User has one of the specified Roles.
 */
class UserRolesRule extends BaseAwardRule {
	// @var int Indicates that User has none of the specified Roles.
	const ROLECOUNT_NONE = 0;
	// @var int Indicates that User has some of the specified Roles.
	const ROLECOUNT_SOME = 1;
	// @var int Indicates that User has all of the specified Roles.
	const ROLECOUNT_ALL = 2;

	// @var array Holds a list of a User's Roles.
	private $_UserRoles = array();

	/**
	 * Retrieves and stores User Roles using UserModel.
	 *
	 * @param int UrerID The UserID to use as a key to retrieve the data.
	 * @return stdClass An object containing User Data.
	 * @see UserModel::GetID()
	 */
	protected function GetUserRoles($UserID) {
		if(!isset($this->_UserRoles) || ($UserID != GetValue('_CurrentUserID', $this))) {
			$UserModel = new UserModel();

			$this->_CurrentUserID = $UserID;
			$this->_UserRoles = $UserModel->GetRoles($UserID)->Result();
		}

		return $this->_UserRoles;
	}

	/**
	 * Checks if the User belongs to one or more Roles.
	 *
	 * @param int UserID The ID of the User.
	 * @param array Roles The Roles to check.
	 * @param array PassResults An array of Results that will make the Rule pass.
	 * @return int A value that indicates if the User has some, all or none of the
	 * specified Roles.
	 */
	private function UserHasRoles($UserID, array $Roles, array $PassResults) {
		$this->Log()->trace(sprintf(T('Checking if User has the specified Roles. ' .
																	'User ID %d. Roles IDs (JSON): %s'),
																$UserID,
																json_encode($Roles)));

		// Get all User's Roles
		$UserRoles = $this->GetUserRoles($UserID);

		// Check how many of the Roles the user has from the passed list
		$FoundRoles = 0;
		foreach($UserRoles as $UserRole) {
			if(in_array(GetValue('RoleID', $UserRole), $Roles)) {
				$FoundRoles++;
			}
		}

		if($FoundRoles === 0) {
			$Result = self::ROLECOUNT_NONE;
		}
		else {
			$Result = ($FoundRoles == count($Roles)) ? self::ROLECOUNT_ALL : self::ROLECOUNT_SOME;
		}

		if(in_array($Result, $PassResults)) {
			$this->Log()->trace(T('Passed.'));
			return self::ASSIGN_ONE;
		}
		$this->Log()->trace(T('Failed.'));
		return self::NO_ASSIGNMENTS;

	}

	private function UserHasAnyRoles($UserID, stdClass $Settings) {
		// Extract the Roles to check from the Settings
		$RolesToCheck = GetValue('Roles', $Settings->AnyRoles);
		return $this->UserHasRoles($UserID,
															 $RolesToCheck,
															 array(self::ROLECOUNT_SOME,
																		 self::ROLECOUNT_ALL));
	}

	private function UserHasAllRoles($UserID, stdClass $Settings) {
		$RolesToCheck = GetValue('Roles', $Settings->AllRoles);
		return $this->UserHasRoles($UserID,
															 $RolesToCheck,
															 array(self::ROLECOUNT_ALL));
	}

	private function UserHasNoRoles($UserID, stdClass $Settings) {
		$RolesToCheck = GetValue('Roles', $Settings->NoRoles);
		return $this->UserHasRoles($UserID,
															 $RolesToCheck,
															 array(self::ROLECOUNT_NONE));
	}

	/**
	 * Runs the processing of the Rule, which will return how many times the Award
	 * should be assigned to the User, based on the specified configuration.
	 *
	 * @see AwardBaseRule::Process().
	 */
	protected function _Process($UserID, stdClass $Settings, array $EventInfo = null) {
		// Check "Has any of" Roles
		if(GetValue('Enabled', $Settings->AnyRoles) == 1) {
			$Results[] = $this->UserHasAnyRoles($UserID, $Settings);
		}
		// Check "Has none of" Roles
		if(GetValue('Enabled', $Settings->NoRoles) == 1) {
			$Results[] = $this->UserHasNoRoles($UserID, $Settings);
		}

		//var_dump("UserRolesRule Result: " . min($Results));
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

		//var_dump($Settings);die();

		// Check settings for "Has any of" Roles
		$AnyRolesSettings = GetValue('AnyRoles', $Settings);
		$RolesList = GetValue('Roles', $AnyRolesSettings);
		if(GetValue('Enabled', $AnyRolesSettings)) {
			if(empty($RolesList)) {
				$this->Validation->AddValidationResult('AnyRoles_Roles',
																							 T('You must select at least one Role from the list.'));
			}
		}

		// Check settings for "Has none of" Roles
		$NoRolesSettings = GetValue('NoRoles', $Settings);
		$RolesList = GetValue('Roles', $NoRolesSettings);
		if(GetValue('Enabled', $NoRolesSettings)) {
			if(empty($RolesList)) {
				$this->Validation->AddValidationResult('NoRoles_Roles',
																							 T('You must select at least one Role from the list.'));
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
		if((GetValue('Enabled', $Settings->AnyRoles) == 1) ||
			 (GetValue('Enabled', $Settings->NoRoles) == 1)) {
			return self::RULE_ENABLED;
		}

		return self::RULE_DISABLED;
	}

	/**
	 * Class constructor.
	 *
	 * @return UserRolesRule.
	 */
	public function __construct() {
		parent::__construct();
	}
}

// Register Rule with the Rule Manager
AwardRulesManager::RegisterRule(
	'UserRolesRule',
	array('Label' => T('User Roles'),
				'Description' => T('Checks if a User has one or more Roles'),
				'Group' => AwardRulesManager::GROUP_GENERAL,
				'Type' => AwardRulesManager::TYPE_USER,
				// Version is for reference only
				'Version' => '13.04.03',
				'Author' => 'D.Zanella',
				'AuthorEmail' => 'diego@pathtoenlightenment.net',
			)
);

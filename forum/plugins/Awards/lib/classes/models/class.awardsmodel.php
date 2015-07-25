<?php if(!defined('APPLICATION')) exit();


/**
 * Awards Model
 */

/**
 * This model is used to retrieve the data related to the Awards.
 */
class AwardsModel extends AwardsPluginBaseModel {
	// @var int Indicates that the Award has been assigned to the User
	const STATUS_ASSIGNED = 1000;
	// @var int Indicates that the Award has been revoked from the User
	const STATUS_REVOKED = 1001;

	/**
	 * Defines the related database table name.
	 *
	 */
	public function __construct() {
		parent::__construct('Awards');

		$this->_SetAwardsValidationRules();
	}

	/**
	 * Set Validation Rules that apply when saving a new Award.
	 */
	protected function _SetAwardsValidationRules() {
		$Validation = new Gdn_Validation();

		// Add extra rules below

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.

		$this->Validation = $Validation;
	}

	/**
	 * Build SQL query to retrieve the list of configured Awards.
	 */
	protected function PrepareAwardsQuery() {
		$Query = $this->SQL
			->Select('VAAL.AwardID')
			->Select('VAAL.AwardClassID')
			->Select('VAAL.AwardName')
			->Select('VAAL.AwardDescription')
			->Select('VAAL.Recurring')
			->Select('VAAL.AwardIsEnabled')
			->Select('VAAL.AwardImageFile')
			->Select('VAAL.RankPoints')
			->Select('VAAL.DateInserted')
			->Select('VAAL.DateUpdated')
			->Select('VAAL.RulesSettings')
			->Select('VAAL.AwardClassName')
			->Select('VAAL.AwardClassCSSClass')
			->Select('VAAL.AwardClassImageFile')
			->Select('VAAL.AwardClassRankPoints')
			->From('v_awards_awardslist VAAL');
		return $Query;
	}

	/**
	 * Convenience method to returns a DataSet containing a list of all the
	 * configured Awards.
	 *
	 * @param int Limit Limit the amount of rows to be returned.
	 * @param int Offset Specifies from which rows the data should be returned. Used
	 * for pagination.
	 * @param array OrderBy An associative array of ORDER BY clauses. They should
	 * be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see AwardsModel::GetWhere()
	 */
	public function Get($Limit = 1000, $Offset = 0, array $OrderBy = array()) {
		return $this->GetWhere(array(), $OrderBy, $Limit, $Offset);
	}

	/**
	 * Convenience method to return the data of a single Award.
	 *
	 * @param int AwardID The ID of the Award for which to retrieve the data.
	 * @return Gdn_DataSet A DataSet containing the data of the specified Award.
	 */
	public function GetAwardByID($AwardID) {
		return $this->GetWhere(array('AwardID' => $AwardID));
	}

	/**
	 * Convenience method to return the data of a single Award using its
	 * name.
	 *
	 * @param int AwardClassName The name of the Award for which to retrieve the data.
	 * @return Gdn_DataSet A DataSet containing the data of the specified Award.
	 */
	public function GetAwardByName($AwardName) {
		return $this->GetWhere(array('AwardName' => $AwardName));
	}

	/**
	 * Retrieves a list of the Awards available to a specific User.
	 *
	 * @param int UserID The ID of the User for whom to retrieve the available
	 * Awards.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 */
	public function GetAvailableAwards($UserID) {
		$this->PrepareAwardsQuery();

		$Result = $this->SQL
			->LeftJoin('UserAwards UA', '(UA.AwardID = VAAL.AwardID) AND (UA.UserID = ' . (int)$UserID . ')')
			// Awards must be enabled to be available
			->Where('VAAL.AwardIsEnabled', 1)
			->BeginWhereGroup()
			// An Award is available if it was never assigned before, or if it is
			// recurring (i.e. it can be assigned multiple times)
			->Where('UA.AwardID', NULL)
			->OrWhere('VAAL.Recurring', 1)
			->EndWhereGroup()
			->GroupBy(array('UA.UserID',
											'VAAL.AwardID',
											'VAAL.AwardName',
											'VAAL.RankPoints',))
			//->OrderBy('VAAL.AwardName')
			->Get();

		return $Result;
	}

	/**
	 * Convenience method to returns a DataSet containing a list of all the
	 * configured Awards, together with the amount of times they have been awarded.
	 *
	 * @param array Wheres An associative array of WHERE clauses. They should
	 * be passed as specified in Gdn_SQLDriver::Where() method.
	 * @param array OrderBy An associative array of ORDER BY clauses. They should
	 * be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @param int Limit Limit the amount of rows to be returned.
	 * @param int Offset Specifies from which rows the data should be returned. Used
	 * for pagination.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see AwardsModel::GetWhere()
	 */
	public function GetWithTimesAwarded(array $Wheres = array(), array $OrderBy = array(),
																			$Limit = 1000, $Offset = 0) {
		/* Add data related to the User Awards before calling GetWhere(). Even
		 * though all these clauses are specified here, in a seemingly "random" way,
		 * the SQL Builder will sort them out and build a proper query.
		 */
		$this->SQL
			->Select('UA.TimesAwarded', 'COALESCE(SUM(%s), 0)', 'TotalTimesAwarded')
			->LeftJoin('UserAwards UA', '(UA.AwardID = VAAL.AwardID)')
			->GroupBy(array(
				'VAAL.AwardID',
				'VAAL.AwardClassID',
				'VAAL.AwardName',
				'VAAL.AwardDescription',
				'VAAL.Recurring',
				'VAAL.AwardIsEnabled',
				'VAAL.AwardImageFile',
				'VAAL.RankPoints',
				'VAAL.DateInserted',
				'VAAL.DateUpdated',
				'VAAL.RulesSettings',
				'VAAL.AwardClassName',
				'VAAL.AwardClassCSSClass',
				'VAAL.AwardClassImageFile',));

		return $this->GetWhere($Wheres, $OrderBy, $Limit, $Offset);
	}

	/**
	 * Returns a DataSet containing a list of the configured Awards.
	 *
	 * @param array WhereClauses An associative array of WHERE clauses. They should
	 * be passed as specified in Gdn_SQLDriver::Where() method.
	 * @param array OrderByClauses An associative array of ORDER BY clauses. They
	 * should	be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @param int Limit Limits the amount of rows to be returned.
	 * @param int Offset Specifies from which rows the data should be returned. Used
	 * for pagination.
	 * @return Gdn_DataSet A DataSet containing a list of the configured Awards.
	 *
	 * @see Gdn_SQLDriver::Where()
	 */
	public function GetWhere(array $WhereClauses, array $OrderByClauses = array(), $Limit = 1000, $Offset = 0) {
		// Set default Limit and Offset, if invalid ones have been passed.
		$Limit = (is_numeric($Limit) && $Limit > 0) ? $Limit : 1000;
		$Offset = (is_numeric($Offset) && $Offset > 0) ? $Offset : 0;

		// Prepare the base query
		$this->PrepareAwardsQuery();

		// Add additional WHERE clauses, if any has been passed
		if(!empty($WhereClauses)) {
			$this->SQL->Where($WhereClauses);
		}

		// Add ORDER BY clauses, if any has been passed
		if(!empty($OrderByClauses)) {
			$this->SetOrderBy($OrderByClauses);
		}
		else {
			$this->SQL->OrderBy('VAAL.AwardName');
		}

		$Result = $this->SQL
			->Limit($Limit, $Offset)
			->Get();

		return $Result;
	}

	/**
	 * Determines if a Primary Key exists in Awards table.
	 *
	 * @param int PrimaryKeyValue The Primary Key.
	 * @return bool True, if the Primary Key exists, False otherwise.
	 */
	protected function PrimaryKeyExists($PrimaryKeyValue) {
		return $this->GetWhere(array('AwardID' => $PrimaryKeyValue))->FirstRow() !== false;
	}

	/**
	 * Deletes an Award and its Rule settings from the Awards and AwardRules
	 * tables.
	 *
	 * Note: all the assignments of the Award will be deleted with this operation.
	 * That is, all Users who gained the Award will lose it permanently.
	 *
	 * @param AwardID The ID of the Award to be deleted.
	 */
	public function Delete($AwardID) {
		$this->SQL->Delete('Awards', array('AwardID' => $AwardID,));
	}

	/**
	 * Enables/disables an Award.
	 *
	 * @param int AwardID The ID of the Award to enabler or disable.
	 * @param int EnableFlag A flag indicating if the Award should be enabled.
	 * It can be "1" for Enable and "0" for Disable.
	 * @return bool True, if operation completed successfully, False otherwise.
	 */
	public function EnableAward($AwardID, $EnableFlag) {
		// Award ID must be a number. There's no point in running a query if it is
		// empty or non-numeric.
		if(!is_numeric($AwardID) || !is_numeric($EnableFlag)) {
			return null;
		}

		// Set the IsEnabled flag in Award configuration
		$Result = $this->SQL->Update('Awards')
								->Set('AwardIsEnabled', $EnableFlag)
								->Where('AwardID', $AwardID)
								->Put();

		return $Result;
	}

	/**
   * Saves an Award.
   *
   * @see AwardsPluginBaseModel::Save()
   */
  public function Save($FormPostValues, $Settings = false) {
		$AwardID = GetValue('AwardID', $FormPostValues);
		if(empty($AwardID)) {
			// Check that the Award Name is Unique. This check cannot be performed by
			// the automatic validation mechanism used with Forms, due its limitations
			if($this->ValidateUnique(GetValue('AwardName', $FormPostValues), 'AwardName') != true) {
				$this->Validation->AddValidationResult('AwardName', T('Award Name must be unique.'));
				return false;
			}
		}
		return parent::Save($FormPostValues, $Settings);
	}
}

<?php if(!defined('APPLICATION')) exit();


/**
 * UserAwards Model
 */

/**
 * This model is used to retrieve the data related to the Awards assigned to the
 * Users.
 */
class UserAwardsModel extends AwardsPluginBaseModel {
	/**
	 * Defines the related database table name.
	 */
	public function __construct() {
		parent::__construct('UserAwards');

		$this->_SetUserAwardsValidationRules();
	}

	/**
	 * Set Validation Rules that apply when saving a new User Award.
	 */
	protected function _SetUserAwardsValidationRules() {
		$Validation = new Gdn_Validation();

		// Add extra rules below

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.

		$this->Validation = $Validation;
	}

	/**
	 * Build SQL query to retrieve the list of configured User Awards.
	 */
	protected function PrepareUserAwardsQuery() {
		$Query = $this->SQL
			->Select('VAUAL.UserAwardID')
			->Select('VAUAL.UserID')
			->Select('VAUAL.DateAwarded')
			->Select('VAUAL.AwardedRankPoints')
			->Select('VAUAL.Status')
			->Select('VAUAL.AwardID')
			->Select('VAUAL.AwardName')
			->Select('VAUAL.AwardDescription')
			->Select('VAUAL.Recurring')
			->Select('VAUAL.AwardImageFile')
			->Select('VAUAL.RankPoints')
			->Select('VAUAL.AwardClassID')
			->Select('VAUAL.AwardClassName')
			->Select('VAUAL.AwardClassCSSClass')
			->Select('VAUAL.AwardClassRankPoints')
			->From('v_awards_userawardslist VAUAL');
		return $Query;
	}

	/**
	 * Builds the SQL query to retrieve the Awards Score for each User.
	 *
	 * @param int Limit Limit the amount of rows to be returned.
	 * @return string An SQL Statement
	 */
	protected function GetUserAwardsScoresSQL($Limit = 10) {
		$Px = Gdn::Database()->DatabasePrefix;

		// Ensure that the passed limit is numeric and enforce default limit if it's not
		if(!is_numeric($Limit)) {
			$Limit = 10;
			$this->Log()->error(sprintf(T('GetUserAwardsScoresSQL. Invalid Limit specified: %s. ' .
																		'Replacing with default.'),
																	$Limit));
		}

		return "
			SELECT
				UA.UserID
				,SUM(AwardedRankPoints) AS TotalAwardsScore
			FROM
				{$Px}UserAwards UA
			GROUP BY
				UA.UserID
			LIMIT " . (int)$Limit;
	}

	/**
	 * Creates a temporary table containing the Post Count of each User in a period
	 * of time.
	 *
	 * @param int Limit Limit the amount of rows to be returned.
	 * @return object A DataSet containing a list of the scores of Top Contributors.
	 */
	public function CreateUserAwardsScoresTable($Limit = 10) {
		$Px = Gdn::Database()->DatabasePrefix;
		// Prepare the SQL to retrieve the data
		$SelectSQL = $this->GetUserAwardsScoresSQL($Limit);

		// Prepare the SQL to save the data to a temporary table
		$CreateSQL = "
			CREATE TEMPORARY TABLE {$Px}_UserAwardsScores (
				UserID int
				,TotalAwardsScore int
				,PRIMARY KEY(UserID)
				,INDEX(TotalAwardsScore desc)
			)
			AS (
				%s
			)
		";

		$SQL = sprintf($CreateSQL, $SelectSQL);

		$Result = $this->SQL
			->Query($SQL);

		return $Result;
	}
	/**
	 * Convenience method to returns a DataSet containing a list of all the
	 * Awards obtained by Users.
	 *
	 * @param array OrderBy An associative array of ORDER BY clauses. They should
	 * be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @param int Limit Limit the amount of rows to be returned.
	 * @param int Offset Specifies from which rows the data should be returned. Used
	 * for pagination.
	 * @return Gdn_DataSet A DataSet containing User Awards data.
	 *
	 * @see UserAwardsModel::GetWhere()
	 */
	public function Get(array $OrderBy = array(), $Limit = 1000, $Offset = 0) {
		return $this->GetWhere(array(), $OrderBy, $Limit, $Offset);
	}

	/**
	 * Convenience method to returns a DataSet containing a list of all the
	 * Awards obtained by a specific User.
	 *
	 * @param int UserID The ID of the User.
	 * @param array OrderBy An associative array of ORDER BY clauses. They
	 * should	be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see UserAwardsModel::GetWhere()
	 */
	public function GetForUser($UserID, array $OrderBy = array()) {
		return $this->GetWhere(array('VAUAL.UserID' => $UserID), $OrderBy);
	}

	/**
	 * Convenience method to retrieve the details of a single Award earned by a
	 * User.
	 *
	 * @param int UserID The ID of the User.
	 * @param int AwardID The ID of the Award.
	 * @param array OrderBy An associative array of ORDER BY clauses. They
	 * should	be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see UserAwardsModel::GetWhere()
	 */
	public function GetUserAwardData($UserID, $AwardID) {
		return $this->GetWhere(array('VAUAL.UserID' => $UserID,
																 'VAUAL.AwardID' => $AwardID))
								->FirstRow();
	}

	/**
	 * Retrieves the last Users who received an Award, together with some User
	 * details.
	 *
	 * @param int AwardID The ID of the Award.
	 * @param array OrderBy An associative array of ORDER BY clauses. They
	 * should	be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @param int Limit The maximum amount of rows to return.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see UserAwardsModel::GetWhere()
	 */
	public function GetRecentAwardRecipients($AwardID, array $OrderBy = array(), $Limit = 1000) {
		/* Add a bunch of fields related to the Users before calling GetWhere(). Even
		 * though all these clauses are specified here, in a seemingly "random" way,
		 * the SQL Builder will sort them out and build a proper query.
		 */
		$this->SQL
			->Select('U.Name')
			->Select('U.Photo')
			->Select('U.Email')
			->Select('U.Gender')
			->Join('User U', '(U.UserID = VAUAL.UserID)', 'inner');

		// Run the query and return the result;
		return $this->GetWhere(array('AwardID' => $AwardID),
													 $OrderBy,
													 $Limit);
	}

	/**
	 * Retrieves the Users with the highest Award Score and most Awards.
	 *
	 * @param array Wheres An associative array of WHERE clauses. They should
	 * be passed as specified in Gdn_SQLDriver::Where() method.
	 * @param array OrderBy An associative array of ORDER BY clauses. They
	 * should	be passed as specified in Gdn_SQLDriver::OrderBy() method.
	 * @param int Limit The maximum amount of rows to return.
	 * @return Gdn_DataSet A DataSet containing Awards data.
	 *
	 * @see UserAwardsModel::GetWhere()
	 */
	public function GetTopUsers(array $Wheres = array(), array $OrderBy = array(), $Limit = 10) {
		// Create temporary table with User Scores. This makes it easier to use Vanilla's
		// Database Library to query the aggregated subquery
		$this->CreateUserAwardsScoresTable($Limit);

		/* Add a bunch of fields related to the Users before calling GetWhere(). Even
		 * though all these clauses are specified here, in a seemingly "random" way,
		 * the SQL Builder will sort them out and build a proper query.
		 */
		$this->SQL
			->Select('U.Name')
			->Select('U.Photo')
			->Select('U.Email')
			->Select('U.Gender')
			->Join('User U', '(U.UserID = VAUAL.UserID)', 'inner')
			->Select('UAS.TotalAwardsScore')
			->Join('_UserAwardsScores UAS', '(UAS.UserID = VAUAL.UserID)', 'inner');

		// If no specific Order By was passed, use the default one
		if(empty($OrderBy)) {
			$OrderBy = array('UAS.TotalAwardsScore desc',
											 'U.UserID asc',
											 'VAUAL.AwardClassRankPoints desc',
											 'VAUAL.RankPoints desc',
											 'VAUAL.AwardName desc',
											 );
		}
		$this->SetOrderBy($OrderBy);

		// Run the query and return the result;
		return $this->GetWhere($Wheres, $OrderBy);
	}

	/**
	 * Calculates and returns the total Score accrued by a User by earning Awards.
	 *
	 * @param int User ID The ID of the User.
	 * @return int User's Score.
	 */
	public function GetUserAwardsScore($UserID) {
		$UserScoreDataSet = $this->SQL
			->Select('AwardedRankPoints', 'SUM', 'TotalAwardsPoints')
			->From('UserAwards')
			->Where('UserID', $UserID)
			->Get()
			->FirstRow();

		return GetValue('TotalAwardsPoints', $UserScoreDataSet, 0);
	}

	/**
	 * Returns a DataSet containing a list of the Awards earned by a User.
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
		$this->PrepareUserAwardsQuery();

		// Add additional WHERE clauses, if any has been passed
		if(!empty($WhereClauses)) {
			$this->SQL->Where($WhereClauses);
		}

		//var_dump($this->SQL->GetSelect());die();

		// Add ORDER BY clauses, if any has been passed
		if(!empty($OrderByClauses)) {
			$this->SetOrderBy($OrderByClauses);
		}

		$Result = $this->SQL
			->Limit($Limit, $Offset)
			->Get();

		return $Result;
	}

	/**
	 * Determines if a Primary Key exists in UserAwards table.
	 *
	 * @param int PrimaryKeyValue The Primary Key.
	 * @return bool True, if the Primary Key exists, False otherwise.
	 */
	protected function PrimaryKeyExists($PrimaryKeyValue) {
		return $this->GetWhere(array('UserAwardID' => $PrimaryKeyValue))->FirstRow() !== false;
	}

	/**
	 * Deletes a User Award.
	 *
	 * @param UserAwardID The ID of the User Award to be deleted.
	 */
	public function Delete($UserAwardID) {
		$this->SQL->Delete('UserAwards', array('UserAwardID' => $UserAwardID,));
	}
}

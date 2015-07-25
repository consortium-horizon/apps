<?php if (!defined('APPLICATION')) exit();


/**
 * Vanilla Database Log Model.
 * It saves Log4php Log entries to a table into Vanilla Database, leveraging
 * objects provided by Vanilla framework.
 *
 * @package LoggerPlugin
 */

class VanillaDBLogModel extends Gdn_Model {
	/// Stores the name of the Log table which Model will read and write.
	protected $LogTableName;

	/**
	 * Set Validation Rules that apply when saving a new row in Cron Jobs History.
	 *
	 * @return void
	 */
	protected function _SetVanillaDBLogValidationRules() {
		//$this->Validation = &new Gdn_Validation();

		// Set additional Validation Rules here. Please note that formal validation
		// is done automatically by base Model Class, by retrieving Schema
		// Information.
	}

	/**
	 * Build SQL query to retrieve data from the Log Table.
	 */
	protected function PrepareLogQuery() {
		$Query = $this->SQL
			->Select('LOG.LogEntryID')
			->Select('LOG.TimeStamp')
			->Select('LOG.LoggerName')
			->Select('LOG.Level')
			->Select('LOG.Message')
			->Select('LOG.Thread')
			->Select('LOG.ClassName')
			->Select('LOG.MethodName')
			->Select('LOG.FileName')
			->Select('LOG.LineNumber')
			->Select('LOG.InsertUserID')
			->From($this->LogTableName . ' LOG');
		return $Query;
	}

	/**
	 * Creates a the Log Table.
	 *
	 * @return void.
	 */
	public function CreateLogTable() {
		Gdn::Structure()
			->Table($this->LogTableName)
			->PrimaryKey('LogEntryID')
			->Column('LoggerName', 'varchar(100)')
			->Column('Level', 'varchar(40)')
			->Column('Message', 'varchar(9999)')
			->Column('Thread', 'varchar(32)')
			->Column('ClassName', 'varchar(100)')
			->Column('MethodName', 'varchar(200)')
			->Column('FileName', 'varchar(400)')
			->Column('LineNumber', 'int')
			->Column('Exception', 'text', TRUE)
			->Column('TimeStamp', 'datetime', FALSE, 'index')
			->Column('InsertUserID', 'int', TRUE)
			->Set(FALSE, FALSE);
	}

	/**
	 * Checks if the Log Table exists.
	 *
	 * @return True if the Table exists, False otherwise.
	 */
	protected function LogTableExists() {
		// The SQL object is cloned because using FetchTables could interfere with
		// the execution of other queries. This may happen if such method is invoked
		// half way through the building of another SELECT statement. From time to
		// time, this could cause the half-built statement to "lose some pieces".
		// By cloning the whole SQL object, instead, we work with an independent
		// copy, which we can manipulate as we like.
		$SQL = clone $this->SQL;
		return count($SQL->FetchTables(':_' . $this->LogTableName)) > 0;
	}

	/**
	 * Defines the related database table name. Table name must be passed as a
	 * parameter.
	 *
	 * @param TableName The name of the table that the Model will manipulate.
	 * @throws an Exception if no Table Name has been provided.
	 */
	public function __construct($TableName) {
		if(empty($TableName)) {
			throw new InvalidArgumentException(T('Model requires a Table Name for instantiation, but none was provided.'));
		}
		$this->LogTableName = $TableName;

		parent::__construct($this->LogTableName);

		// Create Log Table, if it doesn't exist
		if(!$this->LogTableExists()) {
			$this->CreateLogTable();
		}

		$this->_SetVanillaDBLogValidationRules();
	}

	/**
	 * Returns a DataSet containing a list of Log Entries.
	 *
	 * @param datetime DateFrom Beginning of the period to include in the result. Date
	 * must be passed as a string in ISO8601 Format (e.g. '2012-03-01')'
	 * @param datetime DateTo End of the period to include in the result. It follows the
	 * same format rules as parameter $DateFrom.
	 * @param int Limit Limit the amount of rows to be returned. Note: it doesn't
	 * apply to Summary Datasets, as they normally contain one row per total.
	 * @param int Offset Specifies from which rows the data should be returned. Used
	 * for pagination. Note: it doesn't apply to Summary Datasets.
	 * @param array Wheres An associative array of WHERE clauses to be added to
	 * the query.
	 * @return object A DataSet containing a list of the configured API Clients.
	 */
	public function Get($DateFrom, $DateTo, $Limit = 1000, $Offset = 0, array $Wheres = array()) {
		// Set default Limit and Offset, if invalid ones have been passed.
		$Limit = (is_numeric($Limit) && $Limit > 0) ? $Limit : 1000;
		$Offset = (is_numeric($Offset) && $Offset > 0) ? $Offset : 0;

		// On day is added to DateTo as the date it represents should be included
		// until 23:59:59.000. By adding one day and querying by "< DateTo", we're
		// sure to get all the data.
		$DateTo = date('Y-m-d', strtotime($DateTo . ' +1 day'));

		// Return the Jobs Started within the Date Range.
		$this->PrepareLogQuery();
		$Result = $this->SQL
			->Where('TimeStamp >=', array("DATE('%s')" => $DateFrom,), TRUE, FALSE)
			->Where('TimeStamp <', array("DATE('%s')" => $DateTo,), TRUE, FALSE)
			->OrderBy('TimeStamp', 'desc')
			->Where($Wheres)
			->Limit($Limit, $Offset)
			->Get();

		return $Result;
	}

	/**
	 * Save a Log Entry into the Log Table.
	 *
   * @param array LogFields An associative array of Log Entry fields.
   * @return The value of the Primary Key of the row that has been saved, or FALSE if the operation
   * could not be completed successfully.
	 */
	public function Save(&$LogFields) {
		// Define the primary key in this model's table.
		$this->DefineSchema();

		// Validate posted data
		if(!$this->Validate($LogFields)) {
			//var_dump($this->Validate->Results());
			return false;
		}

		// Prepare all the validated fields to be passed to an INSERT/UPDATE query
		$Fields = &$this->Validation->ValidationFields();

		$this->AddInsertFields($Fields);
		return $this->Insert($Fields);
	}

	/**
	 * Deletes an entry from the Log Table.
	 *
	 * @param LogEntryID The ID of the Log Entry to be deleted.
	 * @return LOGGER_OK if Log Entry was deleted successfully, or a numeric error
	 * code if deletion failed.
	 */
	public function Delete($LogEntryID) {
		$this->SQL->Delete($this->LogTableName, array('LogEntryID' => $LogEntryID,));
	}
}

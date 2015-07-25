<?php


namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Extends base Gdn_Model by adding a Logger to it. This way, plugins that want
 * to use logging capabilities won't have to instantiate the Logger every time.
 */
class Model extends \Gdn_Model {
	// @var int The default value to use for LIMIT clause of queries when none is specified.
	const DEFAULT_LIMIT = 0;
	// @var int The default value to use for OFFSET clause of queries when none is specified.
	const DEFAULT_OFFSET = 0;

	// @var Logger The Logger used by the class.
	private $_Log;

	// @var array An array of fields which have been inspected to see if they are AutoIncrement
	// fields. Used to avoid querying the database every time.
	private $_InspectedAutoIncFields = array();

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = \LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}

	/**
	 * Set Validation Rules that apply when saving a new row to the table.
	 *
	 * @param array FormPostValues The values posted with the form.
	 * @return void
	 */
	protected function SetValidationRules(array $FormPostValues = array()) {
		// Set additional Validation Rules in descendant classes. Please note that
		// formal validation is done automatically by base Model Class, by
		// retrieving Schema Information.
	}

	/**
	 * Takes an array of ORDER BY clauses and adds them to the class instance's
	 * SQL object.
	 *
	 * @param array OrderByClauses An array of Order By clauses. Each clause can
	 * contain just the field name, or the field and the sort direction (e.g.
	 * "SomeField ASC").
	 */
	protected function SetOrderBy(array $OrderByClauses) {
		foreach($OrderByClauses as $OrderBy) {
			$OrderByParts = array_filter(explode(' ', $OrderBy));

			/* An order by must contain at most two elements: a field name and the
			 * sort direction. If anything else is found, the clause is considered not
			 * valid and, therefore, ignored.
			 */
			if(count($OrderByParts) > 2) {
				$ErrorMsg = sprintf(T('Invalid ORDER BY clause received: "%s". Ignoring clause.'),
														$OrderBy);
				$this->Log()->error($ErrorMsg);
				continue;
			}

			// Add the ORDER BY clause to the SQL
			$Field = array_shift($OrderByParts);
			$Direction = array_shift($OrderByParts);

			$this->SQL->OrderBy($Field, $Direction);
		}
	}

	/**
	 * Takes an array of WHERE clauses and adds them to the class instance's
	 * SQL object.
	 *
	 * @param array WhereClauses An array of Where clauses.
	 * @see Gdn_SQLDriver::Where()
	 */
	protected function SetWhere(array $WhereClauses) {
		foreach($WhereClauses as $Field => $Value) {
			$this->SQL->Where($Field, $Value);
		}
	}

	/**
	 * Extracts and returns the value of the primary key from the posted values.
	 *
	 * @param array FormPostValues The values posted with the form.
	 * @return mixed
	 */
	protected function GetPrimaryKeyValue($FormPostValues) {
		return GetValue($this->PrimaryKey, $FormPostValues, false);
	}

	/**
	 * Returns true if a primary key exists in a table, false otherwise. In its
	 * default implementation, it just returns true if the value passed as a
	 * parameter is different from "false".
	 *
	 * @param mixed PrimaryKeyValue The primary key for which existence should be
	 * checked.
	 * @return bool
	 */
	protected function PrimaryKeyExists($PrimaryKeyValue) {
		return ($PrimaryKeyValue !== false);
	}

	/**
	 * Returns true if a primary key is empty.
	 *
	 * @param mixed PrimaryKeyValue The primary key value.
	 * @return bool
	 */
	protected function PrimaryKeyEmpty($PrimaryKeyValue) {
		return empty($PrimaryKeyValue);
	}

	/**
   * Takes a set of form data ($Form->_PostValues), validates them, and
   * inserts or updates them to the datatabase.
   *
   * @param array $FormPostValues An associative array of $Field => $Value pairs that represent data posted
   * from the form in the $_POST or $_GET collection.
   * @param array $Settings If a custom model needs special settings in order to perform a save, they
   * would be passed in using this variable as an associative array.
   * @return
   */
  public function Save($FormPostValues, $Settings = false) {
    // Define the primary key in this model's table.
    $this->DefineSchema();

    // See if a primary key value was posted and decide how to save
    $PrimaryKeyValue = $this->GetPrimaryKeyValue($FormPostValues);

		// If Primary Key does not exist, then it's not valid and an INSERT has to
		// be performed
    $Insert = $this->PrimaryKeyEmpty($PrimaryKeyValue) || !$this->PrimaryKeyExists($PrimaryKeyValue);

		$this->EventArguments['Insert'] = $Insert;
		$this->EventArguments['PrimaryKeyValue'] = $PrimaryKeyValue;
		$this->EventArguments['FormPostValues'] = &$FormPostValues;
		$this->FireEvent('BeforeSave');

		// Add special fields, such as DateInserted, DateUpdated, etc. if they are
		// not already populated
    if($Insert) {
      $this->AddInsertFields($FormPostValues);
    }
		else {
      $this->AddUpdateFields($FormPostValues);
    }

		// Set additional validation rules
		$this->SetValidationRules($FormPostValues);

    // Validate the form posted values
    if($this->Validate($FormPostValues, $Insert) == false) {
			$this->Log()->warn(sprintf(T('Validation failed. Data (JSON): %s. Validation ' .
																	 'Results: %s.'),
																 json_encode($FormPostValues),
																 $this->Validation->ResultsText()
																 ));
			return false;
		}

		try {
			// Models perform validation twice: the first time against all posted form fields (see above),
			// the second during the insert/update, only against the fields required by the target table.
			// This logic merges the posted form fields with the ones returned by the first validation,
			// allowing controllers to inject extra data that can be used for further validation.
      $Fields = array_merge($FormPostValues, $this->Validation->ValidationFields());

			// Primary key field(s) should not be removed during either INSERT or
			// UPDATE, like standard Gdn_Model class does. Passing a value for the
			// primary key is legitimate for both the operations and, in MySQL, even
			// if the INSERT is done on a table containing an auto-increment field.
      //$Fields = RemoveKeyFromArray($Fields, $this->PrimaryKey);
      if($Insert === false) {
				// TODO Add support for multi-field primary key.
				// $this->PrimaryKey always contain only one field, i.e. the first one
				// found in the primary key, which is INCORRECT. When the PK contains
				// multiple fields, this code will break.
        $this->Update($Fields, array($this->PrimaryKey => $PrimaryKeyValue));
      }
			else {
        $InsertResult = $this->Insert($Fields);

				if($this->AutoIncField == $this->PrimaryKey) {
					$PrimaryKeyValue = $InsertResult;
				}
				else {
					$PrimaryKeyValue = $Fields[$this->PrimaryKey];
				}
      }

			$this->EventArguments['PrimaryKeyValue'] = $PrimaryKeyValue;
			$this->EventArguments['FormPostValues'] = &$FormPostValues;
			$this->FireEvent('AfterSave');

	    return $PrimaryKeyValue;
		}
		catch(\Exception $e) {
			$this->Log()->error(sprintf(T('Exception occurred while writing to database. Error: %s. Data (JSON): %s. Trace: %s'),
																$e->getMessage(),
																json_encode($Fields),
																$e->getTraceAsString()));

			$this->EventArguments['FormPostValues'] = &$FormPostValues;
			$this->FireEvent('SaveFailed');

			return false;
		}
  }

	/**
	 * Prepares the SELECT clauses to use to retrieve the data from the database.
	 *
	 * @return string;
	 */
	protected function PrepareGetQuery() {
		$Query = $this->SQL
			->Select('*')
			->From($this->Name);

		return $Query;
	}

	/**
	 * Sets the WHERE clauses needed to filter a field by a date range. Clauses
	 * are added directly to the internal SQL object.
	 *
	 * @param string FieldName The field which should be filtered.
	 * @param string DateFrom The start date, in YYYYMMDDHHMMSS format.
	 * @param string DateTo The enddate, in YYYYMMDDHHMMSS format.
	 */
	protected function SetDateRange($FieldName, $DateFrom, $DateTo) {
		$InvalidDateMsg = T('"%s" is not a valid "%s" date. Date should be specified in YYYYMMDDHHMMSS format.');

		// If an empty or invalid "Date From" is passed, just ignore it
		if(strtotime($DateFrom) != false) {
			$this->SQL->Where($FieldName . ' >=', "DATE('$DateFrom')", true, false);
		}

		// If an empty or invalid "Date To" is passed, just ignore it
		if(strtotime($DateTo) != false) {
			// On day is added to DateTo as the date it represents should be included
			// until 23:59:59.000. By adding one day and querying by "< DateTo", we're
			// sure to get all the data.
			$DateTo = date('Ymd', strtotime($DateTo . ' +1 day'));
			$this->SQL->Where($FieldName . ' <', "DATE('$DateTo')", true, false);
		}
	}

	/**
	 * Returns a DataSet populated with the data retrieved from the database.
	 *
	 * @param Wheres An associative array of [Field => Where clause] to add to the
	 * query.
	 * @param OrderBys An associative array of [Field => Order (asc, desc)] to add to the
	 * query.
	 * @param Limit Limit the amount of rows to be returned. Note: it doesn't
	 * apply to Summary Datasets, as they normally contain one row per total.
	 * @param Offset Specifies from which rows the data should be returned. Used
	 * for pagination. Note: it doesn't apply to Summary Datasets.
	 * @return Gdn_Dataset A DataSet containing a list of the throttled Users.
	 */
	public function Get(array $Wheres = null, array $OrderBys = null, $Limit = self::DEFAULT_LIMIT, $Offset = self::DEFAULT_OFFSET) {
		$this->FireEvent('BeforeGet');

		// Return the Jobs Started within the Date Range.
		$this->PrepareGetQuery();

		// Add WHERE clauses, if provided
		if(!empty($Wheres)) {
			$this->SQL->Where($Wheres);
		}

		// Add ORDER BY clauses, if provided
		if(!empty($OrderBys)) {
			foreach($OrderBys as $Field => $Order) {
				$this->SQL->OrderBy($Field, $Order);
			}
		}

		// Set default Limit and Offset, if invalid ones have been passed.
		$Limit = (is_numeric($Limit) && $Limit > 0) ? $Limit : self::DEFAULT_LIMIT;
		$Offset = (is_numeric($Offset) && $Offset > 0) ? $Offset : self::DEFAULT_OFFSET;
		if(($Limit > 0) || ($Offset > 0)) {
			// If limit and offset are both zero, it means "return everything". In such
			// case, there's no point in adding the Limit clause
			$this->SQL->Limit($Limit, $Offset);
		}

		$Result = $this->SQL
			->Get();

		return $Result;
	}

	public function __construct($TableName) {
		parent::__construct($TableName);
	}

	/**
	 * Factory method.
	 *
	 * @param string TableName The name of the table to associate to the model.
	 * @return Aelia\BaseModel
	 */
	public static function Factory($TableName) {
		$Class = get_called_class();
		$Model = new $Class($TableName);

		return $Model;
	}

	/**
	 * Loads the schema of the model's table.
	 *
	 * @see Gdn_Model::DefineSchema()
	 */
	public function DefineSchema() {
		parent::DefineSchema();

		$this->AutoIncField = $this->FieldIsAutoInc($this->PrimaryKey) ? $this->PrimaryKey : null;
	}

	/**
	 * Indicates if the specified field is an AutoIncrement field.
	 *
	 * @param string FieldName The field name.
	 * @return bool
	 */
	protected function FieldIsAutoInc($FieldName) {
		if(isset($this->_InspectedAutoIncFields[$FieldName])) {
			return $this->_InspectedAutoIncFields[$FieldName];
		}

		$TablePrefix = \Gdn::Database()->DatabasePrefix;

		$this->SQL->NamedParameter(':TABLE_NAME', false, $TablePrefix . $this->Name);
		$this->SQL->NamedParameter(':COLUMN_NAME', false, $FieldName);

		$Result = $this->SQL
			->Query("
					SELECT *
					FROM
						INFORMATION_SCHEMA.COLUMNS ISC
					WHERE
						(TABLE_NAME = :TABLE_NAME) AND
						(COLUMN_NAME = :COLUMN_NAME) AND
						((DATA_TYPE = 'int') OR (DATA_TYPE = 'bigint')) AND
						(COLUMN_DEFAULT IS NULL) AND
						(IS_NULLABLE = 'NO') AND
						(EXTRA LIKE '%auto_increment%')
			");

		$this->_InspectedAutoIncFields[$FieldName] = ($Result->NumRows() > 0);
		return $this->_InspectedAutoIncFields[$FieldName];
	}
}

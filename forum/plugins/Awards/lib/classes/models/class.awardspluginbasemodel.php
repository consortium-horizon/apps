<?php if(!defined('APPLICATION')) exit();


/**
 * Extends base Gdn_Model by adding a Logger to it. This way, plugins that want
 * to use logging capabilities won't have to instantiate the Logger every time.
 */
class AwardsPluginBaseModel extends Gdn_Model {
	// @var Logger The Logger used by the class.
	private $_Log;

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}

	/**
	 * Checks if a value is unique for the specified field.
	 *
	 * @param mixed Value The Value to check.
	 * @param mixed Field Field Information.
	 * @return bool True, if the Value is unique, False otherwise.
	 */
	protected function ValidateUnique($Value, $Field) {
		$TableName = $this->Name;
		$Result = $this->SQL
			->Select($Field)
			->From($TableName)
			->Where($Field, $Value)
			->Limit(1)
			->Get()
			->FirstRow();

		return $Result === false;
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
	 * Extracts and returns the value of the Primery Key from the posted values.
	 *
	 * @param array FormPostValues The posted values.
	 * @return mixed The value of the Primary Key.
	 */
	protected function GetPrimaryKeyValue($FormPostValues) {
		return GetValue($this->PrimaryKey, $FormPostValues, false);
	}

	/**
	 * Checks if a Primary Key already exists in the Model's table.
	 *
	 * @param mixed PrimaryKey The value of the Primary Key.
	 * @return bool True if the value exists as a PK, False otherwise.
	 */
	protected function PrimaryKeyExists($PrimaryKeyValue) {
		return ($PrimaryKeyValue !== false);
	}

	/**
   * Takes a set of form data ($Form->_PostValues), validates them, and
   * inserts or updates them to the datatabase.
   *
   * @param array $FormPostValues An associative array of $Field => $Value pairs that represent data posted
   * from the form in the $_POST or $_GET collection.
   * @param array $Settings If a custom model needs special settings in order to perform a save, they
   * would be passed in using this variable as an associative array.
   * @return mixed The Primary Key of the saved record.
   */
  public function Save($FormPostValues, $Settings = false) {
    // Define the primary key in this model's table.
    $this->DefineSchema();

    // See if a primary key value was posted and decide how to save
    $PrimaryKeyValue = $this->GetPrimaryKeyValue($FormPostValues);

		// If Primary Key does not exist, then it's not valid and an INSERT has to
		// be performed
    $Insert = empty($PrimaryKeyValue) || !$this->PrimaryKeyExists($PrimaryKeyValue);

		// Add special fields, such as DateInserted, DateUpdated, etc. if they are
		// not already populated
    if($Insert) {
      $this->AddInsertFields($FormPostValues);
    }
		else {
      $this->AddUpdateFields($FormPostValues);
    }

    // Validate the form posted values
    if(!$this->Validate($FormPostValues, $Insert) === true) {
			return false;
		}

		$this->Database->BeginTransaction();
		try {
      $Fields = $this->Validation->ValidationFields();
			// Don't try to insert or update the primary key
      $Fields = RemoveKeyFromArray($Fields, $this->PrimaryKey);
      if($Insert === false) {
        $this->Update($Fields, array($this->PrimaryKey => $PrimaryKeyValue));
      }
			else {
        $PrimaryKeyValue = $this->Insert($Fields);

				if($PrimaryKeyValue === false) {
					$this->Log()->error(sprintf(T('Could not save data. Validation Results (JSON): "%s".'),
																			json_encode($this->ValidationResults())));
				}
      }
	    return $PrimaryKeyValue;
		}
		catch(Exception $e) {
			$this->Database->RollbackTransaction();
			$this->Log()->error(sprintf(T('Exception occurred while writing to database. Error: %s. Data (JSON): %s.'),
																$e->getMessage(),
																json_encode($Fields)));
			return false;
		}
  }

	public function __construct($TableName) {
		parent::__construct($TableName);
	}
}

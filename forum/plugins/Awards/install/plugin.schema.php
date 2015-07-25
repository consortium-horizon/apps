<?php if (!defined('APPLICATION')) exit();
/**
{licence}
*/

// Some plugins include this file without using "_once", therefore there's a
// risk that the inclusion runs twice. By using class_exists() we prevent the
// class redeclaration issue.
if(class_exists('PluginSchema')) {
	return;
}

class PluginSchema {
	protected $Database;
	protected $Construct;
	protected $Px;

	/**
	 * Checks if column exists in the specified table.
	 *
	 * @param Table The table that may contain the column.
	 * @param Column The name of the column.
	 * @return True, if the table contains the specified column, False otherwise.
	 */
	protected function ColumnExists($Table, $Column) {
		Gdn::Structure()->Table($Table);
		return Gdn::Structure()->ColumnExists($Column);
	}

	/**
	 * Drops a Table from the Database.
	 *
	 * @param $TableName The name of the View to be dropped.
	 */
	protected function DropTable($TableName) {
		$Px = $this->Px;

		// Attempt to drop a table. In case of failure, an exception will be
		// raised and should be handled.
		$Sql = "DROP TABLE `{$Px}$TableName`";
		try {
			$this->Construct->Query($Sql);
		}
		catch(Exception $e) {
			
		}
	}

	/**
	 * Drops a View from the Database.
	 *
	 * @param $ViewName The name of the View to be dropped.
	 */
	protected function DropView($ViewName) {
		$Px = $this->Px;

		// Attempt to drop a table. In case of failure, an exception will be
		// raised and should be handled.
		$Sql = "DROP VIEW `{$Px}$ViewName`";
		try {
			$this->Construct->Query($Sql);
		}
		catch(Exception $e) {
			
		}
	}

	/**
	 * Adds a Foreign Key to a table.
	 *
	 * @param string $TableName The name of the table to which to add Foreign Key.
	 * @param string $ForeignKeyName The name of the Foreign Key.
	 * @param array $Columns An array of field names which will be part of the
	 * Foreign Key.
	 * @param string $RefTableName The name of the referenced table.
	 * @param array $RefColumns An array containing the name of the referenced
	 * fields.
	 * @param string $OnDeleteAction The action to be executed when rows are
	 * deleted in referenced table.
	 * @param string $OnUpdateAction The action to be executed when rows are
	 * updated in referenced table.
	 * @return bool True if Foreign Key was added successfully.
	 * @throws An Exception if Foreign Key could not be added.
	 */
	protected function AddForeignKey($TableName, $ForeignKeyName, array $Columns,
																	 $RefTableName, array $RefColumns,
																	 $OnDeleteAction = 'NO ACTION', $OnUpdateAction = 'NO ACTION') {
		$Px = $this->Px;

		// Drop Foreign Key with the same name, before creating the new one
		$this->DropForeignKey($TableName, $ForeignKeyName);

		$ColumnsTxt = implode(', ', $Columns);
		$RefColumnsTxt = implode(', ', $RefColumns);
		// Add Foreign Key to the table
		$SQL = "
			ALTER TABLE `{$Px}$TableName`
				ADD CONSTRAINT `$ForeignKeyName` FOREIGN KEY ($ColumnsTxt)
				REFERENCES `{$Px}$RefTableName` ($RefColumnsTxt)
				ON DELETE $OnDeleteAction
				ON UPDATE $OnUpdateAction
		";
		$this->Construct->Query($SQL);
		return true;
	}

	/**
	 * Drops a Foreign Key from a table.
	 *
	 * @param string $TableName The name of the table containing the Foreign Key.
	 * @param string $ForeignKeyName The name of the Foreign Key to drop.
	 * @return bool True if Foreign Key was dropped, False if it was not found.
	 * @throws An Exception if Foreign Key could not be dropped.
	 */
	protected function DropForeignKey($TableName, $ForeignKeyName) {
		$Px = $this->Px;
		// Prepare query to check if Foreign Key exists
		$Sql = "
				SELECT
					TABLE_SCHEMA
					,TABLE_NAME
					,CONSTRAINT_NAME
				FROM
					INFORMATION_SCHEMA.TABLE_CONSTRAINTS
				WHERE
					(CONSTRAINT_SCHEMA = DATABASE()) AND
          (CONSTRAINT_NAME = '$ForeignKeyName') AND
          (CONSTRAINT_TYPE = 'FOREIGN KEY')
				";

		// If Foreign Key doesn't exist, just return False
		if($this->Database->SQL()->Query($Sql)->FirstRow() === false) {
			return false;
		}

		// Drop Foreign Key
		$Sql = "
				ALTER TABLE `{$Px}$TableName`
				DROP FOREIGN KEY `$ForeignKeyName`
		";
		$this->Construct->Query($Sql);
		return true;
	}

	/**
	 * Adds an Index to a table.
	 *
	 * @param string $TableName The name of the table to which to add Index.
	 * @param string $IndexName The name of the Index.
	 * @param array $Columns An array of field names which will be part of the
	 * Index.
	 * @param string $IndexType A String indicating which type of index should be
	 * created. It should have one of the following values: empty string, UNIQUE,
	 * FULLTEXT, SPATIAL.
	 * @return True if the Index was added successfully.
	 * @throws An Exception if Foreign Key could not be added.
	 */
	protected function CreateIndex($TableName, $IndexName, array $Columns, $IndexType = '') {
		$Px = $this->Px;

		// Drop existing index with the same name
		$this->DropIndex($TableName, $IndexName);
		$ColumnsTxt = implode(', ', $Columns);

		// Add Foreign Key to the table
		$SQL = "
			ALTER TABLE `{$Px}$TableName`
			ADD $IndexType INDEX `$IndexName` ($ColumnsTxt)
		";
		$this->Construct->Query($SQL);
		return true;
	}

	/**
	 * Drops an Index from a table.
	 *
	 * @param string $TableName The name of the table containing the Foreign Key.
	 * @param string $IndexName The name of the Index to drop.
	 * @return bool True if Index was dropped, False if it was not found.
	 * @throws An Exception if Index could not be dropped.
	 */
	protected function DropIndex($TableName, $IndexName) {
		$Px = $this->Px;

		// Prepare query to check if Index exists
		$Sql = "
				SELECT INDEX_NAME
				FROM
					INFORMATION_SCHEMA.STATISTICS
				WHERE
					(`TABLE_SCHEMA` = DATABASE()) AND
					(`TABLE_NAME` = '{$Px}$TableName') AND
					(`INDEX_NAME` = '$IndexName')
				";

		// If Index doesn't exist, just return False
		if($this->Database->SQL()->Query($Sql)->FirstRow() === false) {
			return false;
		}

		// Drop Index
		$Sql = "
			ALTER TABLE `{$Px}$TableName`
			DROP INDEX `$IndexName`
		";
		$this->Construct->Query($Sql);
		return true;
	}

	
	
	
	

	public function __construct() {
		$this->Database = Gdn::Database();
		$this->Construct = $this->Database->Structure();
		$this->Px = $this->Database->DatabasePrefix;
	}

	/**
	 * Create all the Database Objects in the appropriate order.
	 */
	protected function CreateObjects() {
	}

	/**
	 * Delete the Database Objects.
	 */
	protected function DropObjects() {
	}

	/**
	 * This method will be called during the Setup phase by the plugin to create
	 * the necessary Database Objects.
	 */
	public static function Install() {
		$Schema = new static();
		$Schema->CreateObjects();
	}

	/**
	 * This method will be called during the Disable/Uninstall phase by the plugin
	 * to remove created objects.
	 */
	public static function Uninstall() {
		$Schema = new static();
		$Schema->DropObjects();
	}
}

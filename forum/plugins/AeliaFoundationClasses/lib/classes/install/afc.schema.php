<?php
namespace Aelia;
if(!defined('APPLICATION')) exit();

class AFC_Schema extends \Aelia\Schema {
	/**
	 * Create the table which will store the list of configured Award Classes.
	 */
	protected function create_semaphore_table() {
		\Gdn::Structure()
			->Table('AFC_Semaphores')
			->PrimaryKey('LockID')
			->Column('LockName', 'varchar(200)', false, 'unique')
			->Column('Locked', 'int', 0)
			->Column('LockTimestamp', 'datetime', false)
			->Column('DateInserted', 'datetime', false)
			->Column('InsertUserID', 'int', true)
			->Column('DateUpdated', 'datetime', true)
			->Column('UpdateUserID', 'int', true)
			->Set(false, false);
	}

	/**
	 * Create all the Database Objects in the appropriate order.
	 */
	protected function CreateObjects() {
		$this->create_semaphore_table();
	}

	/**
	 * Delete the Database Objects.
	 */
	protected function DropObjects() {
		$this->DropTable('AFC_Semaphore');
	}
}

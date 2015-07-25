<?php
namespace Aelia;
if(!defined('APPLICATION')) exit();

use \Gdn;

/**
 * Allows to read/write Semaphore table, which contains a list
 * of merchants.
 */
class SemaphoresModel extends \Aelia\Model {
	const DEFAULT_SEMAPHORE_LOCK_WAIT = 180;

	/**
	 * Defines the related database table name.
	 */
	public function __construct() {
		parent::__construct('AFC_Semaphores');
	}

	/**
	 * Returns the details of a single merchant.
	 *
	 * @param string MerchantID The merchant ID.
	 * @return Gdn_DataSet
	 */
	public function GetByName($SemaphoreName) {
		return $this->Get(array('LockName' => $SemaphoreName))->FirstRow();
	}

	/**
	 * Returns the details of a single merchant, using the domain as the key.
	 *
	 * @param string Domain The merchant's website domain.
	 * @return Gdn_DataSet
	 */
	public function GetByDomain($Domain) {
		return $this->Get(array('Domain' => $Domain));
	}

	/**
	 * Deletes a semaphore.
	 *
	 * @param varchar SemaphoreID The semaphore ID.
	 */
	public function Delete($SemaphoreID) {
		$this->SQL->Delete($this->Name, array('SemaphoreID' => $SemaphoreID,));
	}

	/**
	 * Deletes a semaphore, using its name as the key to find it.
	 *
	 * @param varchar SemaphoreName The semaphore ID.
	 */
	public function DeleteByName($SemaphoreName) {
		$this->SQL->Delete($this->Name, array('LockName' => $SemaphoreName,));
	}

	/**
	 * Initialises a semaphore.
	 *
	 * @param string SemaphoreName The name of the semaphore.
	 * @return int|bool The ID of the newly created semaphore, or false on failure.
	 */
	public function Initialize($SemaphoreName) {
		if($this->GetByName($SemaphoreName) != false) {
			return true;
		}

		$SemaphoreData = array(
			'LockName' => $SemaphoreName,
			'Locked' => 0,
			'LockTimestamp' => date('YmdHis'),
		);

		Gdn::Database()->BeginTransaction();
		try {
			$Result = $this->Save($SemaphoreData);
			Gdn::Database()->CommitTransaction();
		}
		catch(\Exception $e) {
			$this->Log()->error(sprintf(T('Unexpected error occurred while initialising ' .
																		'semaphore "%s". Error message: "%s".'),
																	$SemaphoreName,
																	$e->getMessage()));
			Gdn::Database()->RollbackTransaction();
			$Result = false;
		}

		return $Result;
	}

	public function SetLock($SemaphoreName) {
		$CurrentTimestamp = gmdate('YmdHis');
		$SQL = $this->SQL
			->Update($this->Name)
			->Set('Locked', 1)
			->Set('LockTimestamp', $CurrentTimestamp)
			->Where('LockName', $SemaphoreName)
			->Where('Locked', 0)
			->GetUpdate();

		$Result = $this->SQL->Query($SQL, 'default');
		$RowsAffected = $Result->PDOStatement()->rowCount();

		if($RowsAffected > 0) {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_LockSet_Success', 'Set semaphore "%s" lock. Timestamp: "%s".'),
																	$SemaphoreName,
																	$CurrentTimestamp));
		}
		else {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_LockSet_Failure', 'Lock set failed. Semaphore "%s" might be already locked.'),
																	$SemaphoreName));
		}
		return $RowsAffected;
	}

	public function UnsetLock($SemaphoreName) {
		$SQL = $this->SQL
			->Update($this->Name)
			->Set('Locked', 0)
			->Where('LockName', $SemaphoreName)
			->GetUpdate();

		$Result = $this->SQL->Query($SQL, 'default');
		$RowsAffected = $Result->PDOStatement()->rowCount();

		if($RowsAffected > 0) {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_LockUnset_Success', 'Lock unser succeeded. Semaphore "%s" unlocked.'),
																	$SemaphoreName));
		}
		else {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_LockUnset_Failure', 'Lock unser failed. Semaphore "%s" still locked.'),
																	$SemaphoreName));
		}
		return $RowsAffected;
	}

	public function ResetStuckLock($SemaphoreName, $lock_wait = self::DEFAULT_SEMAPHORE_LOCK_WAIT) {
		$CurrentTimestamp = gmdate('YmdHis');
		$LockTimeout = gmdate('YmdHis', time() - $lock_wait);

		$SQL = $this->SQL
			->Update($this->Name)
			->Set('LockTimestamp', $CurrentTimestamp)
			->Where('LockName', $SemaphoreName)
			->Where('LockTimestamp <=', $LockTimeout)
			->GetUpdate();

		$Result = $this->SQL->Query($SQL, 'default');
		$RowsAffected = $Result->PDOStatement()->rowCount();

		if($RowsAffected == 1) {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_WasStuck', 'Semaphore "%s" was stuck, set lock time to %s.'),
																	$SemaphoreName,
																	$CurrentTimestamp));
			return true;
		}

		return false;
	}
}

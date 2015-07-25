<?php
namespace Aelia;
if(!defined('APPLICATION')) exit();

/**
 * Semaphore Lock Management.
 * Adapted from WP Social under the GPL. Thanks to Alex King.
 *
 * @link https://github.com/crowdfavorite/wp-social
 */
class Semaphore extends BaseClass {
	// @var bool Indicates if the lock was broken.
	protected $LockBroken = false;
	// @var string Identifies the lock.
	protected $LockName = 'lock';

	// @var int The amount of seconds after which a locked semaphore should be considered "stuck".
	protected $SemaphoreLockWait;

	// @var Aelia\SemaphoresModel The model used to handle semaphores in the database.
	protected $SemaphoresModel;

	/**
	 * Class constructor.
	 *
	 * @param string LockName The name to assign to the lock.
	 * @param int The amount of seconds after which a "locked lock" is considered
	 * stuck and should be forcibly unlocked.
	 */
	public function __construct($LockName, $SemaphoreLockWait = SemaphoresModel::DEFAULT_SEMAPHORE_LOCK_WAIT) {
		parent::__construct();

		if(empty($LockName)) {
			throw new \InvalidArgumentException(T('AFC_Semaphore_InvalidLock', 'Invalid lock name specified for semaphore.'));
		}
		$this->LockName = $LockName;
		$this->SemaphoreLockWait = $SemaphoreLockWait;

		$this->SemaphoresModel = SemaphoresModel::Factory();
	}

	/**
	 * Initializes the semaphore object.
	 *
	 * @return Semaphore
	 */
	public static function Factory($LockName, $SemaphoreLockWait = SemaphoresModel::DEFAULT_SEMAPHORE_LOCK_WAIT) {
		return new self($LockName, $SemaphoreLockWait);
	}

	/**
	 * Initializes the lock.
	 */
	public function Initialize() {
		// Initialises the semaphore
		$this->SemaphoresModel->Initialize($this->LockName);
	}

	/**
	 * Attempts to start the lock. If the rename works, the lock is started.
	 *
	 * @return bool
	 */
	public function Lock() {
		$this->Initialize();

		$RowsAffected = $this->SemaphoresModel->SetLock($this->LockName);

		if(($RowsAffected == 0) && !$this->StuckCheck()) {
			$this->Log()->debug(sprintf(T('AFC_Semaphore_LockFailed', 'Semaphore lock "%s" failed.'),
																	$this->LockName));
			return false;
		}

		$this->Log()->debug(sprintf(T('AFC_Semaphore_LockSetComplete', 'Set semaphore lock "%s" complete.'),
																$this->LockName));
		return true;
	}

	/**
	 * Unlocks the process.
	 *
	 * @return bool
	 */
	public function Unlock() {
		return $this->SemaphoresModel->UnsetLock($this->LockName);
	}

	/**
	 * Attempts to jiggle the stuck lock loose.
	 *
	 * @return bool
	 */
	private function StuckCheck() {
		// Check to see if we already broke the lock.
		if($this->LockBroken) {
			return true;
		}

		if($this->SemaphoresModel->ResetStuckLock($this->LockName, $this->lock_w)) {
			$this->LockBroken = true;
			return true;
		}
		else {
			return false;
		}
	}
}

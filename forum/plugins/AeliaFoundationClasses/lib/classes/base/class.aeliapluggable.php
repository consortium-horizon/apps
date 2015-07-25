<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Base class for other classes which rely on Foundation plugin. It implements
 * a few features which can be reused by all classes.
 */
class Pluggable extends \Gdn_Pluggable {
	// @var Logger The Logger used by the class.
	private $_Log;

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
	 * Factory method. Returns an instanced of the class. Arguments passed to the
	 * method are used to instantiate the class.
	 *
	 * @return object An instance of the class for which the factory method was
	 * invoked.
	 */
	public static function Factory() {
		$Args = func_get_args();
		$Class = new \ReflectionClass(get_called_class());
		$Instance = $Class->newInstanceArgs($Args);
		return $Instance;
	}
}

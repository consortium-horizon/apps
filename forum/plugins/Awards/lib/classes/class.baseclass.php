<?php if(!defined('APPLICATION')) exit();


/**
 * Base Class. Implements a set of common properties and methods for all other
 * classes.
 */
class BaseClass {
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
	 * Returns an instance of a Class and stores it as a property of this class.
	 * The function follows the principle of lazy initialization, instantiating
	 * the class the first time it's requested.
	 *
	 * @param string ClassName The Class to instantiate.
	 * @param array Args An array of Arguments to pass to the Class' constructor.
	 * @return object An instance of the specified class.
	 * @throws An Exception if the specified class does not exist.
	 */
	protected function GetInstance($ClassName) {
		$FieldName = '_' . $ClassName;

		if(empty($this->$FieldName)) {
			$Args = func_get_args();
			// Discard the first argument, as it is the Class Name, which doesn't have
			// to be passed to the instance of the Class
			array_shift($Args);

			$Reflect  = new ReflectionClass($ClassName);

			$this->$FieldName = $Reflect->newInstanceArgs($Args);
		}

		return $this->$FieldName;
	}

	public function __construct() {
		
	}
}

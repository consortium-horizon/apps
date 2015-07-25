<?php


namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Extends base Gdn_Model by adding a Logger to it. This way, plugins that want
 * to use logging capabilities won't have to instantiate the Logger every time.
 */
class ConfigurationModel extends \Gdn_ConfigurationModel {
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

	public function __construct() {
		parent::__construct();

		$this->Validation = new \Gdn_Validation();
		$this->SetFields();
	}

	/**
	 * Sets the field on the configuration model.
	 */
	protected function SetFields() {
		$this->SetField(array());
	}

	/**
	 * Throws an exception indicating a not supported operation.
	 *
	 * @throws Exception
	 */
	protected function ReturnNotSupported() {
		throw new Exception(T('Exception_OperationNotSupported', 'Operation not supported'));
	}

	/**
	 * Set Validation Rules that apply when saving a new row to the table.
	 *
	 * @param array FormPostValues The values posted with the form.
	 * @return void
	 */
	protected function SetValidationRules(array $FormPostValues = array()) {
		// Set Validation Rules in descendant classes. Please note that
		// formal validation is done automatically by base Model Class, by
		// retrieving Schema Information.
	}

   /**
    * Takes a set of form data ($Form->_PostValues), validates them, and
    * inserts or updates them to the configuration file.
    *
    * @param array $FormPostValues An associative array of $Field => $Value pairs that represent data posted
    * from the form in the $_POST or $_GET collection.
    */
   public function Save($FormPostValues, $Live = FALSE) {
		// Set additional validation rules
		$this->SetValidationRules($FormPostValues);

		$Result = parent::Save($FormPostValues, $Live);
	  return $Result;
  }

	/**
	 * Factory method.
	 *
	 * @return Aelia\ConfigurationModel
	 */
	public static function Factory() {
		$Class = get_called_class();
		$Model = new $Class();

		return $Model;
	}
}

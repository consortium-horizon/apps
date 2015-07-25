<?php	if (!defined('APPLICATION')) exit();
/**
 * Vanilla DB Logger Appender
 * This Appender is used to write to a table into Vanilla's Database by using
 * the Objects provided by Vanilla's framework. Such objects will be
 * retrieved automatically by the Appender when it's instantiated. For this
 * reason, this Appender will only work when used within the forum, it can't
 * be exported as standalone.
 *
 * @package LoggerPlugin
 */
class LoggerAppenderVanillaDB extends LoggerAppender {
	// Log Table Model
	protected $LogModel;

	/// The properties below will be set automatically by Log4php with the data it
	/// will get from the configuration.
	/// @var string The name of the table where the log will be stored.
	protected $Table;
	/// @var int Indicates if the Appender should create the log table on the fly (1) or not (0).
	protected $CreateTable;

	/**
	/**
	 * Getter for CreateTable property.
	 */
	public function getCreateTable() {
		return $this->CreateTable;
	}

	/**
	 * Setter for CreateTable property.
	 */
	public function setCreateTable($Value) {
		$this->CreateTable = $Value;
	}

	/**
	 * Getter for Table property.
	 */
	public function getTable() {
		return $this->Table;
	}

	/**
	 * Setter for Table property.
	 */
	public function setTable($Value) {
		$this->Table = $Value;
	}

	/**
	 * Returns a string representation of an exception.
	 *
	 * @param Exception The exception to convert to a string.
	 * @return A string representation of the Exception.
	 */
	private function FormatThrowable(Exception $Exception) {
		return $Exception->__toString();
	}

	/**
	 * Class constructor.
	 */
	public function __construct($name = '') {
		parent::__construct($name);

		// Retrieve Vanilla's Database Objects
		$this->Database = &Gdn::Database();
	}

	/**
	 * Transforms a Log4php Log Event into an associative array of fields, which
	 * will be saved to a database table.
	 *
	 * @param event A Log4php Event.
	 * @return An associative array of fields containing the information passed by
	 * the Log Event.
	 */
	protected function PrepareLogFields(LoggerLoggingEvent $event) {
		$Fields = array();

		$Fields['LoggerName'] = $event->getLoggerName();
		$Fields['Level'] = $event->getLevel()->toString();
		$Fields['Message'] = $event->getMessage();
		$Fields['Thread'] = $event->getThreadName();

		$LocationInformation = &$event->getLocationInformation();
		$Fields['ClassName'] = $LocationInformation->getClassName();
		$Fields['MethodName'] = $LocationInformation->getMethodName();
		$Fields['FileName'] = $LocationInformation->getFileName();
		$Fields['LineNumber'] = $LocationInformation->getLineNumber();
		$Fields['TimeStamp'] = date('Y-m-d H:i:s', $event->getTimeStamp());

		$ThrowableInfo = $event->getThrowableInformation();
		if(isset($ThrowableInfo)) {
			$Fields['Exception'] = $this->FormatThrowable($ThrowableInfo->getThrowable());
		}


		return $Fields;
	}

	/**
	 * Apply new configuration.
	 *
	 * @return True if configuration is applied successfully.
	 * @throws an Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Layout doesn't apply to this Logger, then use the default one
			$this->layout = new LoggerLayoutPattern();

			// Instantiate the Model that will write to the Log Table
			$this->LogModel = new VanillaDBLogModel($this->Table);
		}
		catch (Exception $e) {
			throw new Exception($e);
		}
		return true;
	}


	/**
	 * Appends a new Log Entry to the Log Table.
	 *
	 * @param event A Log Event object, containing all Log Event Details.
	 * @return void.
	 */
	public function append(LoggerLoggingEvent $event) {
		$this->LogModel->Save($this->PrepareLogFields($event));
	}
}

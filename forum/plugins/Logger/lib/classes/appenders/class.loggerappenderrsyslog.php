<?php if (!defined('APPLICATION')) exit();
/**
 * RSyslog Log Appender
 * Supported Log4php parameters
 * - HostName
 * - Port
 * - Timeout
 *
 * @package LoggerPlugin
 */
class LoggerAppenderRSyslog extends LoggerAppender {
	// @var int The default Timeout to be used when communicating with the Remote Syslog Server.
	const DEFAULT_TIMEOUT = 1;

	
	// @var int The default Facility used to build Syslog Messages.
	const DEFAULT_FACILITY = SyslogFacility::USER;

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.
	// @var string The address of the RSyslog log to which the log messages will be sent.
	protected $HostName;
	// @var int The port to use to connect to RSyslog server.
	protected $Port;
	// @var int Timeout tro be used when communicating with Remote SysLog Server
	protected $Timeout;

	public function setTimeout($Value) {
		$this->Timeout = $Value;
	}

	public function setPort($Value) {
		$this->Port = $Value;
	}

	public function setHostName($Value) {
		$this->HostName = $Value;
	}

	public function __construct($name = '') {
		parent::__construct($name);
	}

	/**
	 * Builds and returns the full URL where the Log messages will be sent.
	 *
	 * @return string The full URL where the Log messages will be sent.
	 */
	protected function GetSyslogPublisher() {
		if(empty($this->SyslogPublisher)) {
			$this->SyslogPublisher = new RSyslog(sprintf('%s:%s',
																									 $this->HostName,
																									 $this->Port),
																					 $this->Timeout);
		}

		return $this->SyslogPublisher;
	}

	/**
	 * Builds a Message that will be sent to a RSyslog Server.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return string A string representing the message.
	 */
	protected function BuildSysLogMessage(LoggerLoggingEvent $event) {
		
		return new SyslogMessage($this->layout->format($event),
														 self::DEFAULT_FACILITY,
														 $event->getLevel()->getSysLogEquivalent(),
														 $event->getTimeStamp());
	}

	/**
	 * Sends a Message to a RSyslog server.
	 *
	 * @param string Message The Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$Result = $this->GetSyslogPublisher()->Send($Message);
		if($Result === true) {
			return true;
		}

		// In case of error, RSysLog publisher returns an array containing an Error Number
		// and an Error Message
		trigger_error(sprintf('Error occurred sending log to Remote Syslog Server. Error number: %d. Error Message: %s',
													$Result[0],
													$Result[1]));
		return false;
	}

	/**
	 * Returns a string representation of an exception.
	 *
	 * @param Exception The exception to convert to a string.
	 * @return A string representation of the Exception.
	 */
	protected function FormatThrowable(Exception $Exception) {
		return $Exception->__toString();
	}

	/**
	 * Apply new configuration.
	 *
	 * @return True if configuration is applied successfully.
	 * @throws an Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Use Log4php default layout to format the Log Message
			
			$this->layout = new LoggerLayoutPattern();
			$this->layout->setConversionPattern('%c - %m %x. Location: %l.');
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
		$Message = $this->BuildSysLogMessage($event);

		return $this->PublishMessage($Message);
	}
}

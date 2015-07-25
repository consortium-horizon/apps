<?php if (!defined('APPLICATION')) exit();
/**
 * Loggly Log Appender
 * Supported Log4php parameters
 * - InputKey
 * 
 * @package LoggerPlugin
 */
class LoggerAppenderLoggly extends LoggerAppender {
	// @var string The URL of Loggly Log Server
	protected $LogglyServer = 'logs.loggly.com';
	protected $LogglyPort = 443;
	protected $LogglyPath = '/inputs';

	// Connection timeout, in seconds
	const CONNECTION_TIMEOUT = 15;

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.

	// @var string The SHA Input Key to be used to send Logs to Loggly via HTTPS
	protected $InputKey;

	/**
	 * Setter for InputKey field.
	 */
	public function setInputKey($Value) {
		$this->InputKey = $Value;
	}

	public function __construct($name = '') {
		parent::__construct($name);
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
	 * Builds a JSON Message that will be sent to a Loggly Server.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return string A JSON structure representing the message.
	 */
	protected function BuildJSONMessage(LoggerLoggingEvent $event) {
		$Fields = array();

		$Fields['LoggerName'] = $event->getLoggerName();
		$Fields['Level'] = $event->getLevel()->getSysLogEquivalent();
		$Fields['Message'] = $event->getMessage();
		$Fields['Thread'] = $event->getThreadName();

		$LocationInformation = $event->getLocationInformation();
		$Fields['ClassName'] = $LocationInformation->getClassName();
		$Fields['MethodName'] = $LocationInformation->getMethodName();
		$Fields['FileName'] = $LocationInformation->getFileName();
		$Fields['LineNumber'] = $LocationInformation->getLineNumber();
		$Fields['TimeStamp'] = date('Y-m-d H:i:s', $event->getTimeStamp());

		$ThrowableInfo = $event->getThrowableInformation();
		if(isset($ThrowableInfo)) {
			$Fields['Exception'] = $this->FormatThrowable($ThrowableInfo->getThrowable());
		}

		return json_encode($Fields);
	}

	/**
	 * Sends a JSON Message to Loggly.
	 *
	 * @param string Message The JSON-Encoded Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$fp = fsockopen(sprintf('ssl://%s', $this->LogglyServer),
										$this->LogglyPort,
										$ErrorNumber,
										$ErrorMessage,
										self::CONNECTION_TIMEOUT);

		try {
			$Out = sprintf("POST %s/%s HTTP/1.1\r\n",
										 $this->LogglyPath,
										 $this->InputKey);
			$Out .= sprintf("Host: %s\r\n", $this->LogglyServer);
			$Out .= "Content-Type: application/json\r\n";
			$Out .= "User-Agent: Vanilla Logger Plugin\r\n";
			$Out .= sprintf("Content-Length: %d\r\n", strlen($Message));
			$Out .= "Connection: Close\r\n\r\n";
			$Out .= $Message . "\r\n\r\n";

			$Result = fwrite($fp, $Out);
			fclose($fp);

			if($Result == false) {
				trigger_error(sprintf('Error occurred posting log message to Loggly via HTTPS. Posted Message: %s',
															$Message));
			}
		}
		catch(Exception $e) {
			trigger_error(sprintf('Exception occurred while posting the message to Loggly via HTTPS. Error Number: %d. Error Message: %s. Exception details: %s',
														$ErrorNumber,
														$ErrorMessage,
														$e->__toString()));
		}

		return $Result;
	}

	/**
	 * Apply new configuration.
	 *
	 * @return bool True if configuration is applied successfully.
	 * @throws An Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Layout doesn't apply to this Logger, then use the default one
			$this->layout = new LoggerLayoutSimple();
		}
		catch (Exception $e) {
			throw new Exception($e);
		}
		return true;
	}

	/**
	 * Appends a new Log Entry to the Log Table.
	 *
	 * @param LoggerLoggingEvent event A Log Event object, containing all Log Event Details.
	 * @return bool True if message was saved correctly, False otherwise.
	 */
	public function append(LoggerLoggingEvent $event) {
		$Message = $this->BuildJSONMessage($event);

		return $this->PublishMessage($Message);
	}
}

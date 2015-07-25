<?php if (!defined('APPLICATION')) exit();
/**
 * Graylog2 Log Appender
 * Supported Log4php parameters
 * - HostName
 * - Port
 * - ChunkSize
 *
 * @package LoggerPlugin
 */
class LoggerAppenderGraylog2 extends LoggerAppender {
	/// @var The Publisher that will send messages to Graylog2.
	protected $GELFMessagePublisher;

	const GRAYLOG2_DEFAULT_PORT = 12201;
	const GRAYLOG2_DEFAULT_CHUNK_SIZE = 1420;

	/// The properties below will be set automatically by Log4php with the data it
	/// will get from the configuration.
	// @var string The Name or IP Address of Graylog2 server.
	protected $HostName;
	// @var int Port The Port to use to communicate with Graylog2.
	protected $Port;
	// @var int The size of the chunks to send to Graylog2.
	protected $ChunkSize;

	public function setHostName($Value) {
		$this->HostName = $Value;
	}

	public function setPort($Value) {
		$this->Port = $Value;
	}

	public function setChunkSize($Value) {
		$this->ChunkSize = $Value;
	}

	public function __construct($name = '') {
		parent::__construct($name);
	}

	/**
	 * Getter for GELFMessagePublisher field. It uses lazy initialization for the
	 * field.
	 */
	protected function GetPublisher() {
		if(empty($this->GELFMessagePublisher)) {
			// Instantiate the Message Publisher that will be used to communicate with
			// Graylog2 Server
			$this->GELFMessagePublisher = new GELFMessagePublisher($this->HostName,
																														 $this->Port,
																														 $this->ChunkSize);
		}

		return $this->GELFMessagePublisher;
	}


	/**
	 * Returns a string representation of an exception.
	 *
	 * @param Exception Exception The exception to convert to a string.
	 * @return string A string representation of the Exception.
	 */
	private function FormatThrowable(Exception $Exception) {
		return $Exception->__toString();
	}

	/**
	 * Builds a GELF Message that will be sent to a Graylog2 Server.
	 *
	 * @param LoggerLoggingEvent event A Log4php Event.
	 * @return GELFMessage A GELF Message instance.
	 */
	protected function BuildGELFMessage(LoggerLoggingEvent $event) {
		$Message = new GELFMessage();

		$Message->setAdditional('LoggerName', $event->getLoggerName());
		$Message->setLevel($event->getLevel()->getSysLogEquivalent());
		$Message->setShortMessage($event->getMessage());
		$Message->setAdditional('Thread', $event->getThreadName());

		$LocationInformation = $event->getLocationInformation();
		$Message->setAdditional('ClassName', $LocationInformation->getClassName());
		$Message->setAdditional('MethodName', $LocationInformation->getMethodName());
		$Message->setFile($LocationInformation->getFileName());
		$Message->setLine($LocationInformation->getLineNumber());
		$Message->setTimestamp(date('Y-m-d H:i:s', $event->getTimeStamp()));

		$ThrowableInfo = $event->getThrowableInformation();
		if(isset($ThrowableInfo)) {
			$Message->setFullMessage($this->FormatThrowable($ThrowableInfo->getThrowable()));
		}

		// This value is not produced, nor managed by Log4php, but Graylog2 can
		// accept it, therefore it's passed to the server as an additional detail.
		$Message->setHost(gethostname());

		return $Message;
	}

	/**
	 * Sends a GELF Message to a Graylog2 Server.
	 *
	 * @param GELFMessage Message The GELF Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage(GELFMessage $Message) {
		return $this->GetPublisher()->publish($Message);
	}

	/**
	 * Apply new configuration.
	 *
	 * @return bool True if configuration is applied successfully.
	 * @throws an Exception if configuration can't be applied successfully.
	 */
	public function activateOptions() {
		try {
			// Layout doesn't apply to this Logger, then use a default one
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
		$Message = $this->BuildGELFMessage($event);

		try {
			return $this->PublishMessage($Message);
		}
		catch(Exception $e) {
			trigger_error(sprintf('log4php: Exception occurred while sending message to Graylog2 Server. Details:',
														$e->__toString()));
			return false;
		}
	}
}

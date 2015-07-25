<?php if (!defined('APPLICATION')) exit();


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

require_once LOGGER_PLUGIN_PATH . '/vendor/psr/log/Psr/Log/LoggerInterface.php';
require_once LOGGER_PLUGIN_PATH . '/vendor/psr/log/Psr/Log/LogLevel.php';

/**
 * Wraps a PHP Logger into a class implementing the psr/logger interface.
 *
 * @link https://github.com/php-fig/fig-standards
 */
class PSRLogger implements \Psr\Log\LoggerInterface {
	protected $Log;

	/**
	 * "Translates" a PSR log level to a Log4php log level.
	 *
	 * @param string PSRLogLevel A PSR log level.
	 * @return mixed A Log4php log level.
	 */
	protected function TranslateLogLevel($PSRLogLevel) {
		switch($PSRLogLevel) {
			case LogLevel::EMERGENCY:
			case LogLevel::ALERT:
			case LogLevel::CRITICAL:
				return LoggerLevel::getLevelFatal();
			break;
			case LogLevel::ERROR:
				return LoggerLevel::getLevelError();
			break;
			case LogLevel::WARNING:
				return LoggerLevel::getLevelWarning();
			break;
			case LogLevel::NOTICE:
			case LogLevel::INFO:
				return LoggerLevel::getLevelInfo();
			break;
			case LogLevel::DEBUG:
				return LoggerLevel::getLevelDebug();
			break;
			default:
				return LoggerLevel::getLevelInfo();
		}
	}

	/**
	 * Magic method.
	 * Returns the value of a property from underlying Log4php logger (if
	 * available). Throws a fatal error is property doesn't exist in either the
	 * main object or the encapsulated Logger.
	 *
	 * @param string Property A property name.
	 * @return mixed
	 */
	public function __get($Property) {
		if(property_exists($this->Log, $Property)) {
			return $this->Log->$Property;
		}
		trigger_error(sprintf(T('Property does not exist: %s::%s'),
													get_class($this),
													$Property),
									E_USER_ERROR);
	}

	/**
	 * Magic method.
	 * Sets the value of a property on the underlying Log4php logger (if
	 * it exists on such object), or on the main object (if it doesn't exist).
	 *
	 * @param string Property A property name.
	 * @param mixed Value The property value.
	 */
	public function __set($Property, $Value) {
		if(property_exists($this->Log, $Property)) {
			$this->Log->$Property = $Value;
		}
		else {
			$this->$Property = $Value;
		}
	}

	/**
	 * Magic method.
	 * Forwards the call to a method to the encapsulated Log4php logger. Throws a
	 * fatal error is property doesn't exist in either the main object or the
	 * encapsulated Logger.
	 *
	 * @param string Method A method name.
	 * @param array Args An array of arguments.
	 * @return mixed
	 */
	public function __call($Method, $Args = array()) {
		if(method_exists($this->Log, $Method)) {
			call_user_func_array(array($this->Log, $Method), $Args);
		}
		else {
		trigger_error(sprintf(T('Method does not exist: %s::%s'),
													get_class($this),
													$Method),
									E_USER_ERROR);
		}
	}

	/**
	 * Class constructor.
	 *
	 * @param Logger Logger An instance of a Log4php logger.
	 */
	public function __construct(Logger $Logger) {
		$this->Log = $Logger;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function emergency($message, array $context = array())	{
		$this->Log->fatal($message);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function alert($message, array $context = array())	{
		$this->Log->fatal($message);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function critical($message, array $context = array()) {
		$this->Log->fatal($message);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function error($message, array $context = array())	{
		$this->Log->error($message);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function warning($message, array $context = array())
	{
		$this->Log->warn($message);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function notice($message, array $context = array())
	{
		$this->Log->info($message);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function info($message, array $context = array()) {
		$this->Log->info($message);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function debug($message, array $context = array())	{
		$this->Log->debug($message);
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, array $context = array()) {
		$level = $this->TranslateLogLevel($level);
		$this->Log->log($level, $message);
	}
}

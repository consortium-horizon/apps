<?php
/**
 * Holds the possible values for Syslog Severity
 */
final class SyslogSeverity {
	const EMERGENCY = 0;
	const ALERT = 1;
	const CRITICAL = 2;
	const ERROR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	/**
	 * Checks if a value is a valid Syslog Severity.
	 *
	 * @param Severity The value to validate.
	 * @return True if the value is a valid Severity, False otherwise.
	 */
	public static function IsValidSeverity($Severity) {
		return isset($Severity) &&
					 $Severity >= self::EMERGENCY &&
					 $Severity <= self::DEBUG;
	}
}

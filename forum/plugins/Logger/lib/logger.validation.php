<?php if (!defined('APPLICATION')) exit();


/**
 * Validation functions for Logger Appender Configuration.
 *
 */

if(!function_exists('ValidateAppenderClass')){
	/**
	 * Checks if an Appender Class is amongst the available ones.
	 */
	function ValidateAppenderClass($Value, $Field, $FormPostedValues){
		return LoggerPlugin::AppendersManager()->AppenderExists($Value);
	}
}

if (!function_exists('ValidatePositiveInteger')) {
	/**
	 * Check that a value is a positive Integer.
	 */
	function ValidatePositiveInteger($Value, $Field) {
		return ValidateInteger($Value, $Field) &&
					 ($Value > 0);
	}
}

if (!function_exists('ValidateTCPPort')) {
	/**
	 * Check that a value is a valid number for a TCP Port. Valid numbers range
	 * from 1 to 65535.
	 */
	function ValidateTCPPort($Value, $Field) {
		return ValidatePositiveInteger($Value, $Field) &&
					 ($Value <= 65535);
	}
}

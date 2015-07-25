<?php if(!defined('APPLICATION')) exit();

if (!function_exists('ValidatePositiveInteger')) {
	/**
	 * Check that a value is a positive Integer.
	 */
	function ValidatePositiveInteger($Value, $Field) {
		return ValidateInteger($Value, $Field) &&
					 ($Value > 0);
	}
}

if (!function_exists('ValidatePositiveNumber')) {
	/**
	 * Check that a value is a positive number.
	 */
	function ValidatePositiveNumber($Value, $Field) {
		return ValidateDecimal($Value, $Field) &&
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

if (!function_exists('ValidateDirectory')) {
	/**
	 * Checks if the specified file name is a valid directory (i.e. it is a
	 * directory, but not "." or "..").
	 *
	 * @param string $Value The directory where the file is located.
	 * @return bool True if the specified FileName is a directory, False if it is
	 * not a directory, or if it is "." or "..".
	 */
	function ValidateDirectory($Value) {
		$BaseName = basename($Value);
		return ($BaseName != '.') &&
					 ($BaseName != '..') &&
					 (is_dir($Value));
	}
}

<?php if(!defined('APPLICATION')) exit();


if(!function_exists('ValidatePositiveInteger')) {
	/**
	 * Check that a value is a positive Integer.
	 */
	function ValidatePositiveInteger($Value, $Field) {
		return ValidateInteger($Value, $Field) &&
					 ($Value > 0);
	}
}

if(!function_exists('ValidateCSSClassName')) {
	/**
	 * Check that a value is a valid name for a CSS class.
	 */
	function ValidateCSSClassName($Value, $Field) {
		return preg_match('/^-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/', $Value) == 1;
	}
}

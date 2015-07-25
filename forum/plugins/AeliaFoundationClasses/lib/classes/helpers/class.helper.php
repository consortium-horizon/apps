<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * General helper. Contains several methods that don't directly belong to any
 * other class.
 */
class Helper extends BaseClass {
	const DATE_RANGE_THIS_YEAR = 'this_year';
	const DATE_RANGE_THIS_MONTH = 'this_month';
	const DATE_RANGE_THIS_WEEK = 'this_week';
	const DATE_RANGE_THIS_DAY = 'this_day';

	/**
	 * Checks if a date range is valid.
	 * @param string DateFrom The range start date.
	 * @param string DateTo The range end date.
	 * @return bool
	 */
	public static function ValidDateRange($DateFrom, $DateTo) {
		return (strtotime($DateFrom) != false) &&
					 (strtotime($DateTo)!= false);
	}

	public static function GenerateDateRange($Period, &$DateFrom, &$DateTo) {
		$DateFrom = null;
		$DateTo = null;

		switch($Period) {
			case self::DATE_RANGE_THIS_YEAR:
				$DateFrom = date('Y-') . '01-01';
				$DateTo = date('Y') . '12-31';
			break;
			case self::DATE_RANGE_THIS_MONTH:
				$DateFrom = date('Y-m') . '-01';
				$DateTo = date('Y-m-t');
			break;
			case self::DATE_RANGE_THIS_WEEK:
				$DateFrom = date('Y-m-d', strtotime('monday this week'));
				$DateTo = date('Y-m-d', strtotime('sunday this week'));
			break;
			case self::DATE_RANGE_THIS_DAY:
				$DateFrom = date('Y-m-d');
				$DateTo = $DateTo;
			break;
		}

		return (!empty($DateFrom) && !empty($DateTo));
	}

	/**
	 * Translates a code into the selected locale's definition. This function
	 * extends original T() by accepting additional arguments and passing them
	 * to a sprintf() function for formatting.
	 *
	 * @param string $Code The code related to the language-specific definition.
	 * Codes thst begin with an '@' symbol are treated as literals and not translated.
	 * @param string $Default The default value to be displayed if the translation code is not found.
	 * @return string The translated string or $Code if there is no value in $Default.
	 * @see Gdn::Translate()
	 */
	public static function T($Code, $Default = false) {
		$Result = Gdn::Translate($Code, $Default);

		$Args = func_get_args();
		// Remove the first two arguments, which are the Code and the Default
		array_shift($Args);
		array_shift($Args);

		return vsprintf($Result, $Args);
	}
}

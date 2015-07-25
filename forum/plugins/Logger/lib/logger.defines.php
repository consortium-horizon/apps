<?php if (!defined('APPLICATION')) exit();


/**
 * Constants used by Logger Plugin.
 *
 * @package LoggerPlugin
 */

// Default Configuration Settings

// Paths
define('LOGGER_PLUGIN_PATH', PATH_PLUGINS . '/Logger');
define('LOGGER_PLUGIN_LIB_PATH', LOGGER_PLUGIN_PATH . '/lib');
define('LOGGER_PLUGIN_CLASS_PATH', LOGGER_PLUGIN_LIB_PATH . '/classes');
define('LOGGER_PLUGIN_MODEL_PATH', LOGGER_PLUGIN_CLASS_PATH . '/models');
define('LOGGER_PLUGIN_EXTERNAL_PATH', LOGGER_PLUGIN_LIB_PATH . '/external');
define('LOGGER_PLUGIN_VIEW_PATH', LOGGER_PLUGIN_PATH . '/views');
define('LOGGER_PLUGIN_ETC_PATH', LOGGER_PLUGIN_PATH . '/etc');
define('LOGGER_PLUGIN_CERTS_PATH', LOGGER_PLUGIN_ETC_PATH . '/certificates');

// URLs
define('LOGGER_PLUGIN_BASE_URL', '/plugin/logger');
define('LOGGER_GENERALSETTINGS_URL', LOGGER_PLUGIN_BASE_URL . '/settings');

// Return Codes
define('LOGGER_OK', 0);
define('LOGGER_ERR_INVALID_APPENDER_ID', 1001);

// Http Arguments

// Definitions for Log4php configuration files
define('LOGGER_LOG4PHP_ROOTLOGGER', 'rootLogger');
define('LOGGER_LOG4PHP_APPENDERS', 'appenders');

/**
 * Auxiliary class to handled serialized arrays declared using "define".
 */
class LoggerConst {
	/**
	 * Generic function to retrieve a value from a serialized array.
	 *
	 * @param SerializedArray The serialized array from which the value should be
	 * retrieved.
	 * @param Key the Key which will be used to retrieve the value.
	 * @return A value from the serialized array, or null if the array doesn't
	 * exist, or the Key is not found.
	 */
	protected static function GetFromSerializedArray($SerializedArray, $Key) {
		if(empty($SerializedArray)) {
			return null;
		}
		$Values = unserialize($SerializedArray);
		return $Values[$Key];
	}
}

<?php


namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Base class for other classes which rely on Foundation plugin. It implements
 * a few features which can be reused by all classes.
 */
class Plugin extends \Gdn_Plugin {
	// @var Logger The Logger used by the class.
	private $_Log;

	/**
	 * Returns the instance of the Logger used by the class.
	 *
	 * @param Logger An instance of the Logger.
	 */
	protected function Log() {
		if(empty($this->_Log)) {
			$this->_Log = \LoggerPlugin::GetLogger(get_called_class());
		}

		return $this->_Log;
	}
}

<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Extends base Gdn_Module by adding a Logger and some basic functions to it.
 */
class Module extends \Gdn_Module {
	// @var Logger The Logger used by the class.
	private $_Log;

	// @var string The Asset Target where the module will be rendered
	protected $_AssetTarget = 'Panel';

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

	/**
	 * Specifies or returns the target Asset for the module.
	 *
	 * @return string The target Asset where the module will be rendered (usually
	 * "Panel").
	 */
	public function AssetTarget($AssetTarget = null) {
		if(!empty($AssetTarget)) {
			$this->_AssetTarget = $AssetTarget;
		}

		return $this->_AssetTarget;
	}
}

<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

use \LoggerLevel as LoggerLevel;

/**
 * Class to represent messages generated by the plugin.
 */
class Message {
	public $Level;
	public $Code;
	public $Message;

	/**
	 * Class constructor.
	 *
	 * @param int Level The message Level.
	 * @param string message The message itself.
	 * @param string Code The message code, if any.
	 * @return WC_Aelia_Message
	 */
	public function __construct($Level, $Message, $Code = '') {
		$this->Level = $Level;
		$this->Message = $Message;
		$this->Code = $Code;
	}
}

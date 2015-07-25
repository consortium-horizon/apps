<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Base Manager. Implements a set of common properties and methods. A manager is
 * a class that is normally used to manage collection of features, in a similar
 * way to a plugin system.
 *
 * The reason why this class is derived from Gdn_Plugin is to allow a manager
 * to take over tasks from the main plugin class, including rendering pages,
 * fetching views, etc, and they will need all the properties and methods of a
 * standard plugin.
 */
class Manager extends \Gdn_Plugin {
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

	/**
	 * Returns an instance of a Class and stores it as a property of this class.
	 * The function follows the principle of lazy initialization, instantiating
	 * the class the first time it's requested.
	 *
	 * @param string ClassName The Class to instantiate.
	 * @param array Args An array of Arguments to pass to the Class' constructor.
	 * @return object An instance of the specified class.
	 * @throws An Exception if the specified class does not exist.
	 */
	// TODO Find a way to move this function in a central place, as it's used by many classes
	protected function GetInstance($ClassName) {
		$FieldName = '_' . $ClassName;

		if(empty($this->$FieldName)) {
			$Args = func_get_args();
			// Discard the first argument, as it is the Class Name, which doesn't have
			// to be passed to the instance of the Class
			array_shift($Args);

			$Reflect  = new \ReflectionClass($ClassName);

			$this->$FieldName = $Reflect->newInstanceArgs($Args);
		}

		return $this->$FieldName;
	}

	/**
	 * Writes some content to a text file. This method tries to acquire an
	 * exclusive lock on the destination file before writing to it, and it returns
	 * an error if such lock cannot be acquired.
	 *
	 * @param string FileName The name of the destination file.
	 * @param string Content The content to write to the file.
	 * @return bool True, if the operation was successful, False otherwise.
	 */
	// TODO Move this method somewhere else. It's too generic to stay in the BaseManager class
	protected function WriteToFile($FileName, $Content) {
		//$FileName = realpath($FileName);
		if(!is_dir(dirname($FileName))) {
			$this->Log()->error(sprintf(T('Requested writing of content to file "%s", but path ' .
																		'is not valid. Content to write: "%s".'),
																	$FileName,
																	$Content));
			return false;
		}

		$fp = fopen($FileName, 'w+');

		// Lock file exclusively
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, $Content);
			// Release the lock
			flock($fp, LOCK_UN);
			$Result = true;
		}
		else {
	    $this->Log()->error(sprintf(T('Could not lock file "%s", writing aborted. ' .
																		'Content to write: "%s".'),
																	$FileName,
																	$Content));
			$Result = false;
		}

		fclose($fp);
		return $Result;
	}

	/**
	 * Removes the elements that are usually rendered for the Dashboard (i.e. the
	 * Admin backend) and replaces the Master View with the default frontend one.
	 *
	 * Purpose of this method
	 * This method is a trick to render a clean page on the frontend from inside
	 * the PluginController. Plugins run within Dashboard/PluginController, which
	 * automatically loads stuff related to the Admin backend when a plugin
	 * renders a view. This method removes everything related to the backend.
	 *
	 * @param Gdn_Controller Sender Sending controller instance.
	 */
	protected function RemoveDashboardElements($Sender) {
		unset($Sender->Assets['Panel']['SideMenuModule']);
		$Sender->MasterView = '';
		$Sender->EventArguments = array();
		$Sender->RemoveCssFile('admin.css');
		$Sender->AddCssFile('style.css');
	}
}

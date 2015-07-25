<?php
namespace Aelia	;
if(!defined('APPLICATION')) exit();

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \Aelia\AFC\Definitions;

/**
 * Manages the list of all core overrides.
 */
class OverridesManager extends BaseClass {
	private static $Instance;

	// @var array Contains a list of all available Overrides.
	private static $Overrides = array();

	/**
	 * Registers a scraper to the array of available Overrides.
	 *
	 * @param string OverrideClass The name of the scraper class.
	 * @param array An associative array of scraper information.
	 * @throws An Exception if the scraper class doesn't exist.
	 */
	public static function RegisterOverride($OverrideClass) {
		// Store the file name where the Override class was declared
		$Reflector = new \ReflectionClass($OverrideClass);

		$OverrideInfo = array(
			'Class' => $OverrideClass,
			'File' => $Reflector->getFileName(),
		);

		self::$Overrides[$OverrideClass] = $OverrideInfo;
	}

	/**
	 * Returns the Override Information array associated to a Override Class.
	 *
	 * @param string $OverrideClass The class of the Override for which to retrieve
	 * the information.
	 * @return array|null An associative array of Override Information, or null, if
	 * the Override Class could not be found.
	 */
	public function GetOverrideInfo($OverrideClass) {
		return GetValue($OverrideClass, self::$Overrides, null);
	}

	/**
	 * Getter for Overrides property.
	 *
	 * @return array The value of Overrides property.
	 */
	public function GetOverrides() {
		return self::$Overrides;
	}

	/**
	 * Loads all Override files found in the specified folder.
	 *
	 * @param string OverridesDir The folder where to look for Override files.
	 * @return bool False, if directory doesn't exist or could not be opened, True
	 * if it exist and could be opened (regardless if any Override file was loaded).
	 */
	private function LoadOverrides($OverridesFolder) {
		$FileSPLObjects =  new RecursiveIteratorIterator(new RecursiveDirectoryIterator($OverridesFolder),
																										 RecursiveIteratorIterator::CHILD_FIRST);

		try {
			foreach($FileSPLObjects as $FullFileName => $FileSPLObject ) {
				if(preg_match('/^class\..+?override/i', $FileSPLObject->getFilename()) == 1) {
					include_once($FullFileName);
				}
			}
		}
		catch (UnexpectedValueException $e) {
			$this->Log()->Info(sprintf(T('Could not read directory "%s".'),
																 $OverridesFolder));
		}
		return true;
	}

	public function __construct() {
		parent::__construct();

		$OverridesFolder = $this->GetOverridesFolder();
		if(!empty($OverridesFolder)) {
			$this->LoadOverrides($OverridesFolder);
		}
	}

	public static function Initialize() {
		OverridesManager::Instance();
	}

	public static function Instance() {
		if(empty(self::$Instance)) {
			self::$Instance = OverridesManager::Factory();
		}

		return self::$Instance;
	}

	protected function GetOverridesFolder() {
		$VersionFolders = array(
			// Vanilla 2.0.10.x or later
			'/^2\.0\.1[0-9]\..*$/' => 'Vanilla-2.0',
			// Vanilla 2.1
			'/^2\.1/' => 'Vanilla-2.1',
		);

		$OverridesFolder = null;
		foreach($VersionFolders as $Regex => $Folder) {
			if(preg_match($Regex, APPLICATION_VERSION)) {
				$OverridesFolder = Definitions::Path('core_overrides') . '/' . $Folder;
				break;
			}
		}
		return $OverridesFolder;
	}
}

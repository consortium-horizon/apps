<?php
namespace Aelia;
if (!defined('APPLICATION')) exit();

use \LoggerLevel as LoggerLevel;

/**
 * Handles installation and update of a plugin.
 */
class Installer extends BaseClass {
	protected $Database;
	protected $SQL;
	protected $Construct;
	protected $Px;

	// @var string Prefix used to retrieve the methods to run sequentially to perform the updates.
	const UPDATE_METHOD_PREFIX = 'update_to_';

	// @var array An array containing error messages returned by the class.
	protected $Messages = array();

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->Database = \Gdn::Database();
		$this->SQL = $this->Database->SQL();
		$this->Construct = $this->Database->Structure();
		$this->Px = $this->Database->DatabasePrefix;
	}

	/**
	 * Adds a message to the list.
	 *
	 * @param int Level The message level.
	 * @param string Message The message.
	 * @param string Code The message code.
	 */
	protected function AddMessage($Level, $Message, $Code = '') {
		$this->Messages[] = new Message($Level, $Message, $Code = '');

		$LogMessage = $Message;
		if(!empty($Code)) {
			$LogMessage .= sprintf('(%s) %s', $Code, $LogMessage);
		}
		$this->Log()->log($Level, $LogMessage);
	}

	/**
	 * Deletes all stored messages.
	 */
	protected function ClearMessages() {
		$this->Messages = array();
	}

	/**
	 * Returns a list of the methods that will perform the updates.
	 *
	 * @param string current_version Current version of the plugin. This will
	 * determine which update methods still have to be executed.
	 * @return array
	 */
	private function GetUpdateMethods($CurrentVersion) {
		if(empty($CurrentVersion)) {
			$CurrentVersion = '0';
		}
		else {
			// Strip all special characters so that the version can be compared with
			// update methods
			$CurrentVersion = $this->GetAlphanumVersion($CurrentVersion);
		}
		$UpdateMethods = array();

		$ClassMethods = get_class_methods($this);
		foreach($ClassMethods as $Method) {
			if(stripos($Method, self::UPDATE_METHOD_PREFIX) === 0) {
				$Version = str_ireplace(self::UPDATE_METHOD_PREFIX, '', $Method);
				if($Version > $CurrentVersion) {
					$UpdateMethods[$Version] = $Method;
				}
			}
		}
		ksort($UpdateMethods);
		return $UpdateMethods;
	}

	/**
	 * Given a plugin version, like "1.23.456 Beta", returns said version stripping
	 * all non alphanumeric characters.
	 *
	 * @param string version The version to process.
	 * @return string
	 */
	protected function GetAlphanumVersion($Version) {
		$Version = str_replace(' ', '_', $Version);
		// Remove dots, dashes, spaces and so on, to get a plain alphanumeric version
		// That is, version 1.2.3.45 Alpha becomes 12345_alpha
		$Version = strtolower(preg_replace("~[^A-Za-z0-9]~", "", $Version));
		return $Version;
	}

	/**
	 * Gets the version of an installed package (plugin, theme, application) from
	 * the configuration file.
	 *
	 * @param string PackageID The package ID.
	 * @param string DefaultValue The default value to return if the package is
	 * not found.
	 */
	protected function GetPackageVersion($PackageID, $DefaultValue = null) {
		$ConfigID = 'Package.' . $PackageID . '.Version';
		return C($ConfigID, $DefaultValue);
	}

	/**
	 * Saves in the configuration file the version of an installed package (plugin,
	 * theme, application).
	 *
	 * @param string PackageID The package ID.
	 * @param string Version The package version.
	 */
	protected function UpdatePackageVersion($PackageID, $Version) {
		$ConfigID = 'Package.' . $PackageID . '.Version';
		return SaveToConfig($ConfigID, $Version);
	}

	/**
	 * Returns a value that indicates if the update routine should be executed,
	 * depending on the version of the package just loaded compared to the one
	 * stored in configuration.
	 *
	 * @param string PackageID The ID of the package (plugin, application, theme)
	 * that is being updated.
	 * @param string NewVersion The new version of the plugin, which will be
	 * stored after a successful update to keep track of the status.
	 * @return bool
	 */
	protected function ShouldRunUpdates($PackageID, $NewVersion) {
		$CurrentVersion = $this->GetPackageVersion($PackageID);
		return (version_compare($CurrentVersion, $NewVersion) < 0);
	}

	/**
	 * Runs all the update methods required to update the plugin to the latest
	 * version.
	 *
	 * @param string PackageID The ID of the package (plugin, application, theme)
	 * that is being updated.
	 * @param string NewVersion The new version of the plugin, which will be
	 * stored after a successful update to keep track of the status.
	 * @return bool
	 */
	public function Update($PackageID, $NewVersion) {
		if(!$this->ShouldRunUpdates($PackageID, $NewVersion)) {
			return true;
		}

		$this->ClearMessages();
		$Result = true;

		$CurrentVersion = $this->GetPackageVersion($PackageID);
		$UpdateMethods = $this->GetUpdateMethods($CurrentVersion);

		if(empty($UpdateMethods)) {
			$this->UpdatePackageVersion($PackageID, $NewVersion);
			return true;
		}

		$this->AddMessage(LoggerLevel::INFO,
											sprintf(T('Running updates for package <span class="Aelia PackageID">%s</span>...'), $PackageID));
		foreach($UpdateMethods as $Version => $Method) {
			if(!is_callable(array($this, $Method))) {
				$this->AddMessage(LoggerLevel::WARN,
													sprintf(T('Update method "%s::%s()" is not a "callable" and was ' .
																		'skipped. Please report this issue to Support.'),
																	get_class($this),
																	$Method));
				continue;
			}
			try {
				$this->AddMessage(LoggerLevel::INFO,
													sprintf(T('Running update method %s::%s()...'),
																	get_class($this),
																	$Method));
				$Result = $this->$Method();
				if($Result === false) {
					break;
				}
			}
			catch(Exception $e) {
				$this->AddMessage(LoggerLevel::ERROR,
													sprintf(T('Update method "%s::%s() raised exception "%s". Update halted. ' .
																		'Please contact Support and provide the error details ' .
																		'that you will find below.'),
																	$e->getMessage(),
																	get_class($this),
																	$Method));
				$Result = false;
			}
		}

		if($Result === true) {
			$this->UpdatePackageVersion($PackageID, $NewVersion);
			$this->AddMessage(LoggerLevel::INFO,
												T('<span class="Aelia Important">Update completed successfully</span>.'));
		}
		else {
			$this->AddMessage(LoggerLevel::ERROR,
												T('<span class="Aelia Important">Update halted</span>. Please review displayed messages and ' .
													'correct any issue that was reported.'));
		}

		\AeliaFoundationClasses::Instance()->AddMessages($this->Messages);

		return $Result;
	}
}

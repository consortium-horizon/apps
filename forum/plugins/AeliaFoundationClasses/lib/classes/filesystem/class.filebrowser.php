<?php if (!defined('APPLICATION')) exit();


/**
 * Implements a Server Side file browser.
 */
class FileBrowser extends \Aelia\BaseClass {
	/**
	 * Class constructor.
	 *
	 * @param array RootDir The root directory for browsing. The class won't
	 * allow browsing of directories above this.
	 * @return FileBrowser An instance of FileBrowser.
	 */
	public function __construct($RootDir, array $Excludes = array()) {
		$this->_RootDir = realpath($RootDir);

		$this->_Excludes = $Excludes;
	}

	private function IsInRooDir($Path) {
		if(substr($Path, 0, strlen($this->_RootDir)) === $this->_RootDir) {
			return true;
		}

		return false;
	}

	public function GetFiles($Path, $ExcludeDotDirs = false, $Recursive = false, $FilesOnly = false) {
		// Path is supposed to be relative to the root folder
		$RealPath = realpath($this->_RootDir . '/' . $Path);

		if(!is_dir($RealPath)) {
			$this->Log()->error(sprintf(T('Path "%s" is not valid, or not readable.'),
																	$RealPath));
			return null;
		}

		if(!$this->IsInRooDir($RealPath)) {
			$this->Log()->error(sprintf(T('Path "%s" is outside specified root directories. ' .
																		'Root directories (JSON): %s.'),
																	$RealPath,
																	json_encode($this->_RootDir))
													);
			return false;
		}

		$Files = scandir($RealPath);
		// Exclude system directories, if requested
		if($ExcludeDotDirs) {
			$Files = array_diff($Files, array('.', '..'));
		}
		$Result = array();

		foreach($Files as $File) {
			$FileName = $RealPath . '/' . $File;

			if(is_dir($FileName)) {
				// If Recursive flag is set, find files in subdirectories
				if($Recursive) {
					$Result = array_merge($Result, $this->GetFiles($FileName));
				}

				// If only files are expected, move to next one (i.e. don't add
				// directories) to the Result list
				if($FilesOnly) {
					continue;
				}
			}
			$Result[] = $FileName;
    }

		return $Result;
	}
}

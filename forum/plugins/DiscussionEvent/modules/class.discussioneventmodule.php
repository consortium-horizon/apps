<?php if (!defined('APPLICATION')) exit();

class DiscussionEventModule extends Gdn_Module {
	public static $ApplicationFolder = 'plugins/DiscussionEvent';
	
	public $CategoryID;
	
	public $Limit = false;
	
	public function __construct($Sender = '') {
		parent::__construct($Sender, self::$ApplicationFolder);
	}
	
	public function getData($Limit = false) {
		if ($Limit === false) {
			$Limit = C('Plugins.DiscussionEvent.MaxDiscussionEvents');
		}
		
		$DiscussionEventModel = new DiscussionEventModel();
		
		// Check for individual categories:
		$Where = array();
		if ($this->CategoryID) {
			$Where['d.CategoryID'] = CategoryModel::filterCategoryPermissions(array($this->CategoryID));
		} else {
			$DiscussionEventModel->Watching = true;
		}
		
		$this->setData('DiscussionEvents', $DiscussionEventModel->getByDiscussionEventRange(0, $Limit, false, false, $Where));
	}
	
	public function assetTarget() {
		return 'Panel';
	}
	
	public function toString() {
		if (!$this->data('DiscussionEvents')) {
			$this->getData($this->Limit);
		}
		
		return parent::ToString();
	}
}
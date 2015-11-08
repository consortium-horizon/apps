<?php if (!defined('APPLICATION')) exit();

class DiscussionEventModel extends DiscussionModel {
		
		$BeginDate = $BeginDate ? Date('Y-m-d', StrToTime($BeginDate)) : Date('Y-m-d');
						
			->select('d.*')
			->from('Discussion d')
		// Determine category watching
		if ($this->Watching && !isset($Where['d.CategoryID'])) {
			$Watch = CategoryModel::CategoryWatch();
			if ($Watch !== true) {
				$Where['d.CategoryID'] = $Watch;
			}
		}
		
}
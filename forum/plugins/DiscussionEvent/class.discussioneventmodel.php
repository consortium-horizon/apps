<?php if (!defined('APPLICATION')) exit();

class DiscussionEventModel extends DiscussionModel {	public function getByDiscussionEventRange($Offset = false, $Limit = false, $BeginDate = false, $EndDate = false, $Where = array()) {
		
		$BeginDate = $BeginDate ? Date('Y-m-d', StrToTime($BeginDate)) : Date('Y-m-d');		$EndDate = $EndDate? Date('Y-m-d', StrToTime($EndDate)) : Date('Y-m-d', PHP_INT_MAX);
								$this->SQL
			->select('d.*')
			->from('Discussion d')			->where('d.DiscussionEventDate >=', $BeginDate)			->where('d.DiscussionEventDate <=', $EndDate)			->orderBy('d.DiscussionEventDate');		
		// Determine category watching
		if ($this->Watching && !isset($Where['d.CategoryID'])) {
			$Watch = CategoryModel::CategoryWatch();
			if ($Watch !== true) {
				$Where['d.CategoryID'] = $Watch;
			}
		}
				$this->SQL->where($Where);						if ($Offset !== false && $Limit !== false) {			$this->SQL->limit($Limit, $Offset);		}				return $this->SQL->get();	}
}
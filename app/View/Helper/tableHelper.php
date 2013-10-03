<?php 
class TableHelper extends AppHelper {

	public $helpers = array ('Form', 'Select', 'Html');

	public function getEventRegisterTableCellsData($tableData) {
		foreach ($tableData as $key => &$value) {
			foreach ($value as $key2 => &$cell) {
				if ($key2 == 0) {
					continue;
				}
				$cell = $this->Select->replaceSelectbox($key, $cell);
			}
			$tableCells[] = $value;
		}
		return $tableCells;
	}

	public function getTableCellsData($tableData, $eventId, $userId) {
		$tableCells = array();
		foreach ($tableData as $key => &$user) {
			foreach ($user as $key2 => &$cell) {
				if ($key2 == 0) {
					continue;
				}
				$cell = $this->Select->replaceSelectAnswer($cell);
			}
			// 自分自身の回答だけを編集できる
			if ($key == $userId) {
				$user[] = $this->Html->link(
						"回答を編集する",
						array('controller' => 'events', 'action' => 'register',
								"userId" => $key,
								"eventId" => $eventId)
				);
			} else {
				//$user[] = "";
				$user[] = $this->Html->link(
						"回答を編集する",
						array('controller' => 'events', 'action' => 'register',
								"userId" => $key,
								"eventId" => $eventId)
				);
			}
			$tableCells[] = $user;
		}
		
		return $tableCells;
	}
}

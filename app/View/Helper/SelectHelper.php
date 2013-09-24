<?php 
class SelectHelper extends AppHelper {

	public $helpers = array ('Form');

	public function replaceSelectbox($index, $value) {
		$registrationCandidate = array('0' => '未回答', '1' => '◯', '2' => '△', '3' => '×');
		return $this->Form->input("value." . $index,
			array('options' => $registrationCandidate, 'default' => $value, 'label' => false)
		);
	}

	public function replaceSelectAnswer($value) {
		$res = "";
		if ($value == 0) {
			$res = "未回答";
		} elseif ($value == 1) {
			$res = "◯";
		} elseif ($value == 2) {
			$res = "△";
		} elseif ($value == 3) {
			$res = "×";
		}
		return $res;
	}

}

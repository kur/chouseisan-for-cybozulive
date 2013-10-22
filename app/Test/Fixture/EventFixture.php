<?php

/**
 * 
 * Eventモデルのテスト用Fixture
 *
 */
class EventFixture extends CakeTestFixture {

	public $import = array(
		'model' => 'Event',
		'records' => false
	);

	public function init() {
		//		return;
		$this->records = array(
			array(
				'id' => '2345',
				'group_id' => 'GROUP,2:5145',
				'owner_id' => '1:17189',
				'name' => 'hoge',
				'description' => 'asdfsds',
				'candidate_list' => json_encode(
					array(
// 						"data" => array(
// 							"0" => "aaa",
// 							"1" => "bbb",
// 							"2" => "ccc",
// 						)
					), JSON_FORCE_OBJECT),
			),
			array(
				'id' => '3232',
				'group_id' => 'GROUP,2:5145',
				'owner_id' => '1:171891',
				'name' => 'hoge',
				'description' => 'asdfsds',
				'candidate_list' => json_encode(array()),
			),
			array(
				'id' => '320323',
				'group_id' => 'GROUP,2:51453',
				'owner_id' => '1:17189',
				'name' => 'hoge',
				'description' => 'asdfsds',
				'candidate_list' => json_encode(array()),
			),
		);

		parent::init();
	}
}

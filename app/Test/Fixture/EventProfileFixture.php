<?php

/**
 * 
 * EventProfileモデルのテスト用Fixture
 *
 */
class EventProfileFixture extends CakeTestFixture {

	public $import = array(
		'model' => 'EventProfile',
		'records' => false
	);

	public function init() {
		//		return;
		$this->records = array(
			array(
				'id' => '2345',
				'event_id' => '32',
				'profile_id' => '12345',
				'value' => json_encode(
					array(
						"GROUP,2:52135" => "aaaa",
						"GROUP,2:34242" => "bbbb",
						"GROUP,2:3667" => "cccc"
					))
			),
			array(
				'id' => '5555',
				'event_id' => '32',
				'profile_id' => '1232',
				'value' => json_encode(
					array(
						"GROUP,2:52135" => "aaaa",
						"GROUP,2:34242" => "bbbb",
						"GROUP,2:3667" => "cccc"
					))
			),
			array(
				'id' => '235',
				'event_id' => '322',
				'profile_id' => '12345',
				'value' => json_encode(
					array(
						"GROUP,2:52135" => "aaaa",
						"GROUP,2:34242" => "bbbb",
						"GROUP,2:3667" => "cccc"
					))
			),
		);
		parent::init();
	}
}

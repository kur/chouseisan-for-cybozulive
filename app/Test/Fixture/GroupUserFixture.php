<?php

/**
 * 
 * Userモデルのテスト用Fixture
 *
 */
class GroupUserFixture extends CakeTestFixture {

	public $import = array(
		'model' => 'GroupUser',
		'records' => false
	);

	public function init() {
		$this->records = array(
			array(
				'group_id' => '3',
				'user_id' => '123',
			),
			array(
				'group_id' => '333',
				'user_id' => '123',
			),
		);
		parent::init();
	}
}

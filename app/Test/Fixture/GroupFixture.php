<?php

/**
 * 
 * Userモデルのテスト用Fixture
 *
 */
class GroupFixture extends CakeTestFixture {

	public $import = array(
		'model' => 'Group',
		'records' => false
	);

	public function init() {
		$this->records = array(
			array(
					'id' => '3',
				'uri' => '2:5145',
				'name' => 'AAAA',
			),
			array(
					'id' => '33',
				'uri' => '2:3490242',
				'name' => 'BBBB',
			),
			array(
					'id' => '333',
				'uri' => '2:3909',
				'name' => 'CCCC',
			),
			array(
					'id' => '33333',
				'uri' => 'GROUP,2:5145',
				'name' => 'FFFF',
			),
		);
		parent::init();
	}
}

<?php

/**
 * 
 * Profileモデルのテスト用Fixture
 *
 */
class ProfileFixture extends CakeTestFixture {

	public $import = array(
		'model' => 'Profile',
		'records' => false
	);

	public function init() {
		$this->records = array(
				array(
						'id' => '1',
						'uri' => '2:5145',
						'screen_name' => 'AAAA',
				),
				array(
						'id' => '2',
						'uri' => '2:3490242',
						'screen_name' => 'BBBB',
				),
				array(
						'id' => '3',
						'uri' => '2:3909',
						'screen_name' => 'CCCC',
				),
				array(
						'id' => '4',
						'uri' => 'GROUP,2:5145',
						'screen_name' => 'FFFF',
				),
		);
		parent::init();
	}

}

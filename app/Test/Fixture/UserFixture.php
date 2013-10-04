<?php
/**
 * 
 * Userモデルのテスト用Fixture
 *
 */
class UserFixture extends CakeTestFixture {

	public $import = array('model' => 'User', 'records' => false);

	public function init() {
		$this->records = array(
				array(
						'uri' => '1:12345',
						'group_list' => json_encode(array(
								"GROUP,2:5145" => "aaaa",
								"GROUP,2:3490242" => "ddd",
								"GROUP,2:3909" => "eee"
						)),
						'screen_name' => '田中一郎',
				),
				array(
						'uri' => '1:333333',
						'group_list' => json_encode(array(
								"GROUP,2:52135" => "aaaa",
								"GROUP,2:34242" => "bbbb",
								"GROUP,2:3667" => "cccc"
						)),
						'screen_name' => 'hoge hoge',
				),
				array(
						'uri' => '1:98765',
						'group_list' => json_encode(array(
								"GROUP,2:524135" => "zzz",
								"GROUP,2:342432" => "xxx",
								"GROUP,2:3634567" => "yyy"
						)),
						'screen_name' => '山田太郎',
				),
		);
		parent::init();
	}
}

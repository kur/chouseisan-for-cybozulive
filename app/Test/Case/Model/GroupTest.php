<?php
App::uses('User', 'Model');
App::uses('Group', 'Model');
App::uses('Profile', 'Model');

class GroupTest extends CakeTestCase {

	public $fixtures = array(
		'app.user',
		'app.group',
		'app.group_user',
		'app.profile',
		'app.group_profile',
	);

	public function setUp() {
		parent::setUp();
		$this->Group = ClassRegistry::init('Group');
	}

	/**
	 * すべてのデータを取得する
	 */
	public function testGetAll() {
		$result = $this->Group->getAll();
		$this->assertEquals(count($result), 4);
	}

	/**
	 * 特定のグループを取得する
	 */
	public function testGetInfo() {
		$result = $this->Group->getInfo(3);
		//		debug($result);
		$this->assertEquals($result["Group"]['uri'], '2:5145');
		$this->assertArrayHasKey('User', $result);
		$this->assertArrayHasKey('Group', $result);
	}

	/**
	 * 追加する
	 */
	public function testAddUser() {
		// 新規グループの追加
		$data['1234'] = 'xxxxx';
		$data['12345'] = 'yyyyy';
		$result = $this->Group->add($data);
		$this->assertEquals(count($result), 2);
		$result = $this->Group->getAll();
		$this->assertEquals($result[4]['Group']['uri'], '1234');
		$this->assertEquals($result[4]['Group']['name'], 'xxxxx');

		//既存グループの変更
		$data['1234'] = 'yyyyy';
		$this->Group->add($data);
		$result = $this->Group->getAll();
		$this->assertEquals($result[4]['Group']['uri'], '1234');
		$this->assertEquals($result[4]['Group']['name'], 'yyyyy');

	}

	/**
	 * URIを取得する
	 */
	public function testGetUri() {
		$result = $this->Group->getUri(3);
		$this->assertEquals($result, '2:5145');
		
		// 存在しないID
		$result = $this->Group->getUri(-1);
		$this->assertEquals($result, '-1');
	}

	/**
	 * 更新する
	 */
	public function testUpdate() {
		$this->Group->update(3, "sssss", array(1,2));
		$result = $this->Group->getInfo(3);
		
		$this->assertEquals($result['Group']['name'], 'sssss');
		
		
		
	}
}

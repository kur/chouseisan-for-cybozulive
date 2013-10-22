<?php
App::uses('EventProfile', 'Model');

class EventProfileTest extends CakeTestCase {

	public $fixtures = array(
		'app.EventProfile',
	);

	public function setUp() {
		parent::setUp();
		$this->EventProfile = ClassRegistry::init('EventProfile');
	}

	/**
	 * すべてのデータを取得する
	 */
	public function testGetAll() {
		$result = $this->EventProfile->getAll();
		$this->assertEqual(count($result), 3);

	}

	/**
	 * データを取得する
	 */
	public function testGetInfo() {
		$result = $this->EventProfile->getInfo(32);
		$this->assertEqual(count($result), 2);

		$result = $this->EventProfile->getInfo(32, "12345");
		$this->assertEqual($result[0]["EventProfile"]["id"], 2345);
	}

	/**
	 * データを追加する
	 */
	public function testUpdate() {
		// 新しく追加
		$data[0]["id"] = 2;
		$data[0]["value"] = 2;
		$data[1]["id"] = 1;
		$data[1]["value"] = 1;
		$result = $this->EventProfile->update(321, "11111", $data);
		$result = $this->EventProfile->getInfo(321, "11111");
		$json = json_decode($result[0]['EventProfile']['value'], true);
		$this->assertEqual($result[0]["EventProfile"]["event_id"], 321);
		$this->assertEqual($result[0]["EventProfile"]["profile_id"], "11111");
		$this->assertEqual($data, $json);
		$originalId = $result[0]['EventProfile']["id"];

		// すでにあるデータを上書き
		$data[1]["value"] = 3;
		$result = $this->EventProfile->update(321, "2:11111", $data);
		$result = $this->EventProfile->getInfo(321, "2:11111");
		$json = json_decode($result[0]['EventProfile']['value'], true);
		$this->assertEqual($data, $json);
		$this->assertEqual($result[0]['EventProfile']["id"], $originalId);

	}
}

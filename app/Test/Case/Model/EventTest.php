<?php
App::uses('Event', 'Model');

class EventTest extends CakeTestCase {

	public $fixtures = array(
		'app.event',
	);

	public function setUp() {
		parent::setUp();
		$this->Event = ClassRegistry::init('Event');
	}

	/**
	 * すべてのデータを取得する
	 */
	public function testGetAll() {
		$result = $this->Event->getAll();
		$this->assertEquals(count($result), 3);
	}

	/**
	 * 特定のデータを取得する
	 */
	public function testGetInfo() {
		$result = $this->Event->getInfo("2345");
		$this->assertEquals($result["Event"]['id'], '2345');
	}

	/**
	 * 特定のグループに所属するイベント一覧を取得する
	 */
	public function testGetList() {
		// 存在するグループを指定する
		$result = $this->Event->getList(array(
				"GROUP,2:5145" => "hogehoge"
			));
		$this->assertEquals(count($result), 1);
		$this->assertEquals(count($result["GROUP,2:5145"]), 2);

		// 存在しないグループを指定する
		$result = $this->Event->getList(array(
				"hoge"
			));
		$this->assertEquals(count($result), 0);
		$result = $this->Event->getList(array());
		$this->assertEquals(count($result), 0);
	}

	/**
	 * イベント追加する
	 */
	public function testAddEvent() {
		$data["Event"] = array(
			'id' => '25',
			'group_id' => 'GROUP,2:515',
			'owner_id' => '1:12345',
			'name' => 'hoge',
			'description' => 'asdfsds',
			'candidate_list' => json_encode(
				array(
					"GROUP,2:52135" => "aaaa",
					"GROUP,2:34242" => "bbbb",
					"GROUP,2:3667" => "cccc"
				)),
		);
		$result = $this->Event->add($data, "1:12345");
		$result = $this->Event->getInfo("25");
		$this->assertEquals($result["Event"]["id"], 25);
	}

	/**
	 * 説明を編集する
	 */
	public function testEditDescription() {
		$this->Event->editDescription(3232, "hogehogehoge");
		$result = $this->Event->getInfo(3232);
		$this->assertEquals($result["Event"]["description"], "hogehogehoge");

		$this->Event->editDescription(3232, "hogehogehoge3");
		$result = $this->Event->getInfo(3232);
		$this->assertEquals($result["Event"]["description"], "hogehogehoge3");
	}

	/**
	 * 候補を取得する
	 */
	public function testGetCandidate() {
		$this->Event->AddCandidate(3232, "hoge1");
		$this->Event->AddCandidate(3232, "hoge2");
		$this->Event->AddCandidate(3232, "hoge3");
		$result = $this->Event->removeCandidate(3232, "2");
		$result = $this->Event->getCandidate(3232);
		$this->assertEquals(count($result['data']), 2);
		$result = $this->Event->getCandidate(3232, true);
		$this->assertEquals(count($result['data']), 3);

		$result = $this->Event->getCandidate(3232, true, true);
		$this->assertEquals(count($result), 3);
	}

	/**
	 * 候補を追加する
	 */
	public function testAddCandidate() {
		$addData = "xxxx";
		$this->Event->addCandidate(3232, $addData);
		$result = $this->Event->getInfo(3232);

		$candidateList = json_decode($result["Event"]["candidate_list"], true);

		$isExisit = false;
		foreach ($candidateList["data"] as $candidate) {
			if ($candidate["value"] == $addData) {
				$isExisit = true;
				break;
			}
		}
		$this->assertTrue($isExisit);
	}

	/**
	 * 候補を削除する
	 */
	public function testRemoveCandidate() {
		$this->Event->AddCandidate(3232, "hoge1");
		$this->Event->AddCandidate(3232, "hoge2");
		$this->Event->AddCandidate(3232, "hoge3");
		$result = $this->Event->getInfo("3232");
		$result = $this->Event->removeCandidate(3232, "2");
		$result = $this->Event->getInfo("3232");
		$candidateList = json_decode($result["Event"]["candidate_list"], true);

		$value = -1;
		foreach ($candidateList["data"] as $candidate) {
			if ($candidate["id"] == 2) {
				$value = $candidate["flag"];
				break;
			}
		}
		$this->assertEqual($value, 0);
	}
}

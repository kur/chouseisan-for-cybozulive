<?php
App::uses('AppController', 'Controller');
App::uses('EventsController', 'Controller');
App::uses('User', 'Model');
App::uses('Event', 'Model');
App::uses('EventUser', 'Model');
App::uses('Auth', 'Component');

class EventControllerTest extends ControllerTestCase {

	public $fixtures = array(
		'app.event',
		'app.eventuser',
		'app.user',
		'app.group'
	);

	public function setUp() {
		parent::setUp();
		// ログインさせる
		$data["uri"] = "1:17189";
		$data["password"] = "password";
		$result = $this->testAction('/users/loginDirect', array(
			'data' => $data,
			'method' => 'post'
		));
		$this->Event = ClassRegistry::init('Event');
	}

	/**
	 * イベントの一覧を表示させる
	 */
	public function testIndex() {
		$result = $this->testAction('/events/index');
		$this->assertTrue(isset($this->vars["groupList"]));
		$this->assertTrue(isset($this->vars["eventList"]));
	}

	/**
	 *  イベントの作成を行う
	 */
	/*
	    public function testCreate() {
	        // 作成画面表示
	        $result = $this->testAction('/events/create', array(
	                'method' => 'get'
	            ));
	        $this->assertTrue(isset($this->vars["groupList"]));
	
	        // 所属していないグループのイベントを作成
	        $result = $this->Event->getAll();
	        $tmpCount = count($result);
	        $data["Event"]["group_id"] = "GROUP,2:51452";
	        $data["Event"]["name"] = "1:12345";
	        $data["Event"]["description"] = "1:12345";
	        $result = $this->testAction('/events/create', array(
	                'data' => $data,
	                'method' => 'post'
	            ));
	        // イベントが増えていない事を確認
	        $result = $this->Event->getAll();
	        $this->assertEqual(count($result)-$tmpCount, 0);
	
	        // 所属しているグループのイベントを作成
	        $result = $this->Event->getAll();
	        $tmpCount = count($result);
	        $data["Event"]["group_id"] = "GROUP,2:5145";
	        $result = $this->testAction('/events/create', array(
	                'data' => $data,
	                'method' => 'post'
	            ));
	        $result = $this->Event->getAll();
	        $this->assertEqual(count($result)-$tmpCount, 1);
	
	    }
	 */ 
	/**
	 * 個別のイベントを表示する
	 */
	public function testView() {
		// 特定のイベントに関する情報を取得する
		$result = $this->testAction('/events/view/eventId:2345', array(
			'method' => 'get'
		));
		$this->assertArrayHasKey('registrations', $this->vars);
		$this->assertArrayHasKey('group', $this->vars);
		$this->assertArrayHasKey('event', $this->vars);
		$this->assertArrayHasKey('user', $this->vars);

		// 存在しないイベントに関する情報を取得する
		$result = $this->testAction('/events/view/eventId:23452', array(
			'method' => 'get'
		));
		$this->assertFalse($result);
		$this->assertArrayNotHasKey('event', $this->vars);

		// 自分が所属していないグループのイベントを取得する
		$result = $this->testAction('/events/view/eventId:320323', array(
			'method' => 'get'
		));
		// 		debug($result);
		// 		debug($this->vars);
		$this->assertFalse($result);
		//		debug($this->vars);

	}

	/**
	 * イベントの情報を編集する
	 */
	public function testEdit() {
		// 作成画面表示（イベントが存在しない）
		$result = $this->testAction('/events/edit/eventId:2341', array(
			'method' => 'get'
		));
		$this->assertFalse($this->result);

		// 作成画面表示（引数がない）
		$result = $this->testAction('/events/edit/', array(
			'method' => 'get'
		));
		$this->assertFalse($this->result);

		// 作成画面表示（他人のイベント）
		$result = $this->testAction('/events/edit/eventId:3232', array(
			'method' => 'get'
		));
		$this->assertFalse($this->result);

		// 作成画面表示（自分がオーナー）
		$result = $this->testAction('/events/edit/eventId:2345', array(
			'method' => 'get'
		));

		$this->assertArrayHasKey("group", $this->vars);
		$this->assertArrayHasKey("event", $this->vars);

		// 説明文を編集する（他人がオーナー）
		$data = array();
		$data["type"] = "description";
		$data["Event"]["id"] = 3232;
		$data["Event"]["description"] = "this is test.";
		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$this->assertFalse($result);
		$result = $this->Event->getInfo("3232");
		$this->assertNotEqual($result["Event"]["description"], "this is test.");

		// 説明文を編集する（自分がオーナー）
		$data = array();
		$data["type"] = "description";
		$data["Event"]["id"] = 2345;
		$data["Event"]["description"] = "this is test.";
		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$this->assertTrue($result);
		$result = $this->Event->getInfo("2345");
		$this->assertEqual($result["Event"]["description"], "this is test.");

		// 説明文を編集する（存在しないイベント）
		$data = array();
		$data["type"] = "description";
		$data["Event"]["id"] = -1;
		$data["Event"]["description"] = "this is test.";
		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$this->assertFalse($result);

		// 候補を追加する（自分がオーナー）
		$result = $this->Event->getInfo("2345");
		$result = json_decode($result["Event"]["candidate_list"], true);
		$count = 0;
		if (isset($result["data"])) {
			$count = count($result["data"]);
		}

		$data = array();
		$data["type"] = "candidate";
		$data["name"] = "this is test.";
		$data["eventId"] = 2345;

		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$result = $this->Event->getInfo("2345");
		$result = json_decode($result["Event"]["candidate_list"], true);
		$this->assertEqual(count($result["data"]) - $count, 1);

		// 候補を追加する（名前がない）
		$result = $this->Event->getInfo("2345");
		$result = json_decode($result["Event"]["candidate_list"], true);
		$count = 0;
		if (isset($result["data"])) {
			$count = count($result["data"]);
		}

		$data = array();
		$data["type"] = "candidate";
		$data["name"] = "";
		$data["eventId"] = 2345;

		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$result = $this->Event->getInfo("2345");
		$result = json_decode($result["Event"]["candidate_list"], true);
		$this->assertEqual(count($result["data"]) - $count, 0);

		// 候補を追加する（ 他人がオーナー）
		$data = array();
		$data["type"] = "candidate";
		$data["name"] = "this is test.";
		$data["eventId"] = 3232;

		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$this->assertFalse($result);

		// 候補を追加する（存在しない）
		$data = array();
		$data["type"] = "candidate";
		$data["name"] = "this is test.";
		$data["eventId"] = -1;
		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$this->assertFalse($result);

	}

	/**
	 * 候補への回答画面を表示する
	 */
	public function testRemoveCandidate() {
		// 削除するための候補を追加する
		$data = array();
		$data["type"] = "candidate";
		$data["name"] = "this is test.";
		$data["eventId"] = 2345;

		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$data["name"] = "this is test2.";
		$result = $this->testAction('/events/edit/', array(
			'method' => 'post',
			'data' => $data
		));
		$result = $this->Event->getInfo("2345");
		//debug($result);

		// 自分のイベントを削除
		$result = $this->testAction('/events/removeCandidate/eventId:2345/candidateId:2',
			array(
				'method' => 'get',
			));
		//debug($result);

		// 他人のイベントを削除
		$result = $this->testAction('/events/removeCandidate/eventId:3232/candidateId:2',
			array(
				'method' => 'get',
			));
		//debug($result);

		// 存在しない候補を削除
		$result = $this->testAction('/events/removeCandidate/eventId:3232/candidateId:-1',
			array(
				'method' => 'get',
			));

		$result = $this->Event->getInfo("2345");
		//debug($result);
	}

	/**
	 * 候補への回答画面を表示する
	 */
	public function testCreate() {

	}
	/**
	 * 候補への回答画面を表示する 
	 */
	public function testRegistration() {
		// GET
		// 回答する

	}

	public function tearDown() {
		$result = $this->testAction('/users/logout');
		//debug($result);
		parent::tearDown();

	}
}

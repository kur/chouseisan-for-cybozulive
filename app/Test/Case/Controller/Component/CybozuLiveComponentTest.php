<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('CybozuLiveComponent', 'Controller/Component');

class TestCybozuLiveController extends Controller {

	public $paginate = null;
}

class PagematronComponentTest extends CakeTestCase {

	public $CybozuLiveComponent = null;

	public $Controller = null;

	public function setUp() {
		parent::setUp();
		// コンポーネントと偽のテストコントローラをセットアップする
		$Collection = new ComponentCollection();
		$this->CybozuLiveComponent = new CybozuLiveComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestCybozuLiveController($CakeRequest, $CakeResponse);
		$this->CybozuLiveComponent->startup($this->Controller);
	}

	/**
	 * ユーザの情報を取得する
	 */
	public function testGetUserInfo() {
		$result = (array) $this->CybozuLiveComponent
			->getUserInfo('63bc53c5df5e20dda4a9db5180012c90', '6aa4f2cb1ec524c6d08a27705c21aceb');
		$this->assertArrayHasKey('author', $result);
		$this->assertArrayHasKey('entry', $result);

	}

	/**
	 * グループメンバー一覧を取得
	 */
	public function testGetGroupMember() {
		// グループメンバーの取得
		$result = (array) $this->CybozuLiveComponent
			->getGroupMember('63bc53c5df5e20dda4a9db5180012c90', '6aa4f2cb1ec524c6d08a27705c21aceb', 'GROUP,1:54658', 3);
		debug($result);
		$this->assertLessThanOrEqual(3 * 2, count($result['member']));
		$this->assertArrayHasKey('member', $result);
		$this->assertArrayHasKey('self', $result);
		$this->assertGreaterThan(0, count($result['member']));

		//存在しないグループを指定
		$result = (array) $this->CybozuLiveComponent
			->getGroupMember('63bc53c5df5e20dda4a9db5180012c90', '6aa4f2cb1ec524c6d08a27705c21aceb', 'GROUP,2:53953');
		$this->assertEqual(0, count($result['member']));
	}
	public function testGetAuthorizationUrl() {
		$result = $this->CybozuLiveComponent->getAuthorizationUrl();
		debug($result);
		$this->assertNotNull($result);
	}
 
	public function tearDown() {
		parent::tearDown();
		// 終了した後のお掃除
		unset($this->CybozuLiveComponent);
		unset($this->Controller);
	}
}

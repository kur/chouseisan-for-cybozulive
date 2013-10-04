<?php 
App::uses('User', 'Model');

class UserTest extends CakeTestCase {

	public $fixtures = array('app.user');
	
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
	}
	/**
	 * すべてのユーザデータを取得する
	 */
	public function testGetAll() {
		$result = $this->User->getAll(); 
 		$this->assertEquals(count($result), 3);
	}
	
	/**
	 * 特定のユーザデータを取得する
	 */
	public function testGetInfo() {
		$result = $this->User->getInfo("1:12345");
		$this->assertEquals($result["User"]['uri'], '1:12345');
	}
	/**
	 * 特定のユーザの所属するグループを取得する
	 */
	public function testGetGroupList() {
		$result = $this->User->getGroupList("1:12345");
		$this->assertEquals(count($result), 3);
	}
	/**
	 * ユーザを追加する
	 */
	public function testAddUser() {
		// 既存ユーザのグループ数確認
		$result = $this->User->getInfo("1:12345");
		$this->assertEquals($result["User"]["screen_name"], '田中一郎');
		
		$groupList = json_encode(array(
					"GROUP,2:52135" => "aaaa",
					"GROUP,2:34242" => "bbbb",
					"GROUP,2:3667" => "cccc",
					"GROUP,2:667" => "dddd"
		));

		// 既存ユーザを再度追加
		$uri = '1:12345';
		$screen_name = 'hoge';
		$password = 'dfakdsljfaflasdd';
		$oauth_token = 'sfasdafadd';
		$oauth_token_secret = 'sfasdfa';
		$this->User->add($uri, $screen_name, $groupList, $password, $oauth_token, $oauth_token_secret, $groupList);
		
		// 追加したユーザを取得
		$result = $this->User->getInfo("1:12345");
		$this->assertEquals($result["User"]["screen_name"], 'hoge');
	}
}

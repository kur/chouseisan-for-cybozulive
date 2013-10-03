<?php
class UsersController extends AppController {

	public $name = 'Users';

	public $uses = array('Account', 'Option', "User");

	public $components = array('Auth', 'Session', 'CybozuLive');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('callback', 'login');
	}

/**
 * リダイレクトさせる
 */
	public function index() {
		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
	}
/**
 * ログイン
 */
	public function login($confirmed = false) {
		if (isset($_GET["requesturl"])) {
			$_SESSION["requestUrl"] = $_GET["requesturl"];
		}
		if ($confirmed) {
			$authorizationUrl = $this->CybozuLive->getAuthorizationUrl();
			$this->redirect($authorizationUrl);
		}
	}
/**
 * ログイン後の画面遷移
 */
	public function logined() {
		if (isset($_SESSION["requestUrl"]) && $_SESSION["requestUrl"] != "") {
			$redirectUrl = $_SESSION["requestUrl"];
			$_SESSION["requestUrl"] = "";
			$this->redirect($redirectUrl);
		} else {
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}
	}
/**
 * ログアウト
 */
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
/**
 * サイボウズLive認証後の処理
 */
	public function callback() {
		$this->autoRender = false;

		// Access Toeken取得
		$accessToken = $this->CybozuLive->getAccessToken(
				$_SESSION['oauth_request_token'],
				$_SESSION['oauth_request_token_secret'],
				$_REQUEST['oauth_verifier']);

		// Sessionに保存
		$_SESSION['oauth_access_token'] = $accessToken['oauth_access_token'];
		$_SESSION['oauth_access_token_secret'] = $accessToken['oauth_access_token_secret'];

		// ユーザ情報の取得
		$userInfo = $this->CybozuLive->getUserInfo(
				$_SESSION['oauth_access_token'],
				$_SESSION['oauth_access_token_secret']
		);

		// 所属グループ取得
		$groupList = array();
		foreach ($userInfo->entry as $group) {
			$groupList[(string)$group->id] = (string)$group->title;
		}

		$userInfo->author->password = $this->Auth->password((string)$userInfo->author->uri);
		$hoge = $this->User->add($userInfo, $groupList);
		var_dump($hoge);
		
		if ($this->Auth->login($hoge)) {
			$this->redirect(array('action' => 'logined'));
		} else {
			echo "error";
		}
		
// 		// ユーザ登録
// 		$data = array(
// 				"User" => array(
// 						"uri" => (string)$userInfo->author->uri,
// 						"screen_name" => (string)$userInfo->author->name,
// 						"group_list" => json_encode($groupList),
// 						"password" => $this->Auth->password((string)$userInfo->author->uri),
// 						"oauth_token" => $_SESSION['oauth_access_token'],
// 						"oauth_token_secret" => $_SESSION['oauth_access_token_secret'],
// 						"flag" => 0,
// 				)
// 		);
// 		$this->User->save($data);

		// ログイン処理
// 		$logindata['User']["user_uri"] = (string)$userInfo->author->uri;
// 		$logindata['User']["password"] = $this->Auth->password((string)$userInfo->author->uri);

// 		if ($this->Auth->login($logindata)) {
// 			$this->redirect(array('action' => 'logined'));
// 		} else {
// 			echo "error";
// 		}
	}
}
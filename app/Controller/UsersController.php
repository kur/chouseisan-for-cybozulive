<?php
class UsersController extends AppController {

	public $name = 'Users';

	public $uses = array('Account', 'Option', "User");

	public $components = array('Auth', 'Session', 'CybozuLive');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'index');
		$this->Auth->logoutRedirect = array('controller' => '/', 'action' => 'index');
		$this->Auth->allow('newregistration', 'callback');

		$this->Auth->fields = array(
				'username' => 'user_uri',
				'password' => 'password'
		);

		$this->Auth->userModel = 'User';

		if ($this->Auth->User()) {
			//$this->layout = "logined";
		}
	}

	public function index() {
		var_dump($this->Auth->User());
	}
/**
 * ログイン
 */
	public function login() {
		// サイボウズLive認証URLを取得
		$authorizationUrl = $this->CybozuLive->getAuthorizationUrl();
		$this->redirect($authorizationUrl);
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
		$userinfo = $this->CybozuLive->getUserinfo(
				$_SESSION['oauth_access_token'],
				$_SESSION['oauth_access_token_secret']
		);
		// ユーザ登録
		$data = array(
				"User" => array(
						"user_uri" => (string)$userinfo->uri,
						"screen_name" => (string)$userinfo->name,
						"password" => $this->Auth->password((string)$userinfo->uri),
						"oauth_token" => $_SESSION['oauth_access_token'],
						"oauth_token_secret" => $_SESSION['oauth_access_token_secret'],
						"flag" => 0,
				)
		);
		$this->User->save($data);
		// ログイン処理
		$logindata['User']["user_uri"] = (string)$userinfo->uri;
		$logindata['User']["password"] = $this->Auth->password((string)$userinfo->uri);

		if ($this->Auth->login($logindata)) {
			$this->redirect(array('action' => 'index'));
		} else {
			echo "error";
		}
	}
}
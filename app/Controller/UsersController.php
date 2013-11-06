<?php

class UsersController extends AppController {

	public $name = 'Users';

	public $uses = array(
		'Account',
		'Option',
		"User",
		"Group",
		'GroupUser'
	);

	public $components = array(
		'Auth',
		'Session',
		'CybozuLive'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('callback', 'login');
	}

	/**
	 * リダイレクトさせる
	 */
	public function index() {
		$this->redirect(array(
			'controller' => 'pages',
			'action' => 'home'
		));
	}

	/**
	 * ログイン
	 */
	public function login($confirmed = false) {
		if ($confirmed) {
			// サイボウズLiveから認証用URLを取得
			$authorizationUrl = $this->CybozuLive->getAuthorizationUrl();
			// 認証用URLにリダイレクト
			$this->redirect($authorizationUrl);
		}
	}

	/**
	 * ログアウト
	 */
	public function logout() {
		$this->redirect($this->Auth->logout());
		return true;
	}

	/**
	 * サイボウズLive認証後の処理
	 */
	public function callback() {
		$this->autoRender = false;
		// サイボウズLiveからAccess Toeken取得
		$accessToken = $this->CybozuLive->getAccessToken($_SESSION['oauth_request_token'],
			$_SESSION['oauth_request_token_secret'], $_REQUEST['oauth_verifier']);

		// 取得したAccess TokenをSessionに保存
		$_SESSION['oauth_access_token'] = $accessToken['oauth_access_token'];
		$_SESSION['oauth_access_token_secret'] = $accessToken['oauth_access_token_secret'];

		// ユーザ情報の取得
		$userInfo = $this->CybozuLive->getUserInfo($_SESSION['oauth_access_token'],
			$_SESSION['oauth_access_token_secret']);

		// 所属グループ取得
		$groupList = array();
		foreach ($userInfo->entry as $group) {
			$groupList[(string)$group->id] = (string)$group->title;
		}
		// グループ情報を登録
		$groupIdList = $this->Group->add($groupList);

		// CakePHP ACLログイン用のパスワードを生成
		$userInfo->author->password = $this->Auth->password((string)$userInfo->author->uri);

		// ユーザとして登録
		$uri = (string)$userInfo->author->uri;
		$screenName = (string)$userInfo->author->name;
		$password = (string)$userInfo->author->password;
		$oauthToken = $_SESSION['oauth_access_token'];
		$oauthTokenSecret = $_SESSION['oauth_access_token_secret'];
		$user = $this->User->add($uri, $screenName, $groupIdList, $password, $oauthToken, $oauthTokenSecret);
		// 登録したアカウントでログイン
		if ($this->Auth->login($user)) {
			$this->redirect($this->Auth->redirect());
		} else {
			$this->redirect(array(
				'action' => 'index'
			));
		}
	}

	public function loginDirect() {
		$user["User"] = $_POST;
		return $this->Auth->login($user);
	}
}

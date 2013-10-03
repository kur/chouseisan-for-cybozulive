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
		// リダイレクト後のURLの指定があればSESSIONに保存
		if (isset($_GET["requesturl"])) {
			$_SESSION["requestUrl"] = $_GET["requesturl"];
		}
		if ($confirmed) {
			// サイボウズLiveから認証用URLを取得
			$authorizationUrl = $this->CybozuLive->getAuthorizationUrl();
			// 認証用URLにリダイレクト
			$this->redirect($authorizationUrl);
		}
	}
/**
 * ログイン後の画面遷移
 */
	public function logined() {
		// セッションにログイン後遷移URLが指定されていた場合の処理
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

		// サイボウズLiveからAccess Toeken取得
		$accessToken = $this->CybozuLive->getAccessToken(
				$_SESSION['oauth_request_token'],
				$_SESSION['oauth_request_token_secret'],
				$_REQUEST['oauth_verifier']);

		// 取得したAccess TokenをSessionに保存
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

		// CakePHP ACLログイン用のパスワードを生成
		$userInfo->author->password = $this->Auth->password((string)$userInfo->author->uri);

		// ユーザとして登録
		$user = $this->User->add($userInfo, $groupList);

		// 登録したアカウントでログイン
		if ($this->Auth->login($user)) {
			$this->redirect(array('action' => 'logined'));
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}
}
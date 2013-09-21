<?php
class CybozuLiveComponent extends Component {

/**
 * ユーザ情報を取得
 * @param unknown $oauthToken
 * @param unknown $oauthTokenSecret
 */
	public function getUserinfo($oauthToken, $oauthTokenSecret) {
		$oauth = $this->__getOauth($oauthToken, $oauthTokenSecret);

		$result = $oauth
		->sendRequest('https://api.cybozulive.com/api/group/V2',
				array(
						"unconfirmed" => "true"
				), 'GET');
		$cybozu = $result->getBody();
		$cybozulive = simplexml_load_string($cybozu);
		return $cybozulive->author;
	}
/**
 * グループのリストを取得
 * @param unknown $oauthToken
 * @param unknown $oauthTokenSecret
 * @return multitype:string
 */
	public function getGroupList($oauthToken, $oauthTokenSecret) {
		$oauth = $this->__getOauth($oauthToken, $oauthTokenSecret);

		$result = $oauth
		->sendRequest('https://api.cybozulive.com/api/group/V2',
				array(
						"unconfirmed" => "true"
				), 'GET');
		$cybozu = $result->getBody();
		$cybozulive = simplexml_load_string($cybozu);

		$groupList = array();
		foreach ($cybozulive->entry as $group) {
			$groupList[(string)$group->id] = (string)$group->title;
		}
		return $groupList;
	}
/**
 * 認証の共通処理
 * @param string $oauthRequestToken
 * @param string $oauthRequestTokenSecret
 * @return HTTP_OAuth_Consumer
 */
	private function __getOauth($oauthRequestToken="", $oauthRequestTokenSecret="") {
		/* Consumer key from cybozulive */
		$consumerKey = $_SERVER['cybouzuLiveConsumerKey'];
		/* Consumer Secret from cybozulive */
		$consumerSecret = $_SERVER['cybouzuLiveConsumerSecret'];
		$oauth = new HTTP_OAuth_Consumer($consumerKey, $consumerSecret);

		// ssl通信を可能に
		$httpRequest = new HTTP_Request2();
		$httpRequest->setConfig('ssl_verify_peer', false);
		$consumerRequest = new HTTP_OAuth_Consumer_Request;
		$consumerRequest->accept($httpRequest);
		$oauth->accept($consumerRequest);
		if ($oauthRequestToken != "" && $oauthRequestTokenSecret != "") {
			$oauth->setToken($oauthRequestToken);
			$oauth->setTokenSecret($oauthRequestTokenSecret);
		}

		return $oauth;
	}
/**
 * サイボウズLiveのAccessToeknを取得する
 * @param unknown $oauthRequestToken
 * @param unknown $oauthRequestTokenSecret
 * @param unknown $oauthVerifier
 * @return string
 */
	public function getAccessToken($oauthRequestToken, $oauthRequestTokenSecret, $oauthVerifier) {
		$oauth = $this->__getOauth($oauthRequestToken, $oauthRequestTokenSecret);

		/* cybozuliveから戻ってきた oauth_verifierをセット */
		/* Access token をリクエスト */
		$oauth->getAccessToken(
				'https://api.cybozulive.com/oauth/token',
				$oauthVerifier);

		$accessToken['oauth_access_token'] = $oauth->getToken();
		$accessToken['oauth_access_token_secret'] = $oauth->getTokenSecret();
		return $accessToken;
	}
/**
 * サイボウズLiveの認証画面のURLを取得
 * @return string
 */
	public function getAuthorizationUrl() {
		// サイボウズliveからの Callback url
		$callbackUrl = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . DS . "callback";
		$content = '';
		$authorizationUrl = "";
		try {
			$oauth = $this->__getOauth();
			// cybozuliveからrequest_tokenの取得
			$oauth->getRequestToken(
					'https://api.cybozulive.com/oauth/initiate',
					$callbackUrl);
			/* tokenをセッションに保存 */
			$_SESSION['oauth_request_token'] = $oauth->getToken();
			$_SESSION['oauth_request_token_secret'] = $oauth->getTokenSecret();
			/* ステータスをstartにセット */
			$_SESSION['oauth_state'] = "start";
			/* authorization URL を取得 */
			$authorizationUrl = $oauth->getAuthorizeURL('https://api.cybozulive.com/oauth/authorize');
		} catch (Exception $e) {
			$content = $e->getMessage();
		}
		return $authorizationUrl;
	}
}
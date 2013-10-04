<?php

/**
 * 
 * ユーザ用モデル
 *
 */
class User extends AppModel {

	public $name = 'User';

	public $primaryKey = 'uri';

	/**
	 * ユーザの追加
	 * @param unknown $uri
	 * @param unknown $screenName
	 * @param unknown $groupList
	 * @param unknown $password
	 * @param unknown $oauthToken
	 * @param unknown $oauthTokenSecret
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function add($uri, $screenName, $groupList, $password, $oauthToken, $oauthTokenSecret) {
		$data = array(
			"User" => array(
				"uri" => $uri,
				"screen_name" => $screenName,
				"group_list" => $groupList,
				"password" => $password,
				"oauth_token" => $oauthToken,
				"oauth_token_secret" => $oauthTokenSecret
			)
		);
		return $this->save($data);
	}

	/**
	 * すべてのユーザ情報を取得
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getAll() {
		return $this->find('all');
	}

	/**
	 * ユーザ情報を取得
	 * @param unknown $userUri
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getInfo($userUri) {
		$userinfo = $this->find('first', array(
				'conditions' => array(
					'User.uri' => $userUri
				)
			));
		return $userinfo;
	}

	/**
	 * 特定ユーザのグループ情報を取得
	 * @param unknown $userUri
	 * @return mixed
	 */
	public function getGroupList($userUri) {
		$userinfo = $this->getInfo($userUri);
		return json_decode($userinfo["User"]["group_list"], true);
	}

}

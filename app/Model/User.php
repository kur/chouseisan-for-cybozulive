<?php

/**
 * 
 * ユーザ用モデル
 *
 */
class User extends AppModel {

	public $name = 'User';

	public $hasAndBelongsToMany = array(
		'Group' => array(
			'className' => 'Group',
			'joinTable' => 'group_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

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
	public function add($uri, $screenName, $groupIdList, $password, $oauthToken, $oauthTokenSecret) {
		// 検索
		$result = $this->find('first', array(
			'conditions' => array(
				'User.uri' => $uri
			)
		));
		if (empty($result)) {
			$this->create();
		}

		// データを整形
		$result['User']['uri'] = $uri;
		$result['User']['screen_name'] = $screenName;
		$result['User']['password'] = $password;
		$result['User']['oauth_token'] = $oauthToken;
		$result['User']['oauth_token_secret'] = $oauthTokenSecret;
		$result['Group'] = $groupIdList;
		return $this->save($result);
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
		$result = $this->getInfo($userUri);
		$groupList = array();
		foreach ($result['Group'] as $group) {
			$groupList[$group['id']] = $group['name'];
		}

		return $groupList;
	}

}

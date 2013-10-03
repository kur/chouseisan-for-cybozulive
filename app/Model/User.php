<?php 
class User extends AppModel {

	public $name = 'User';

	public $primaryKey = 'uri';

	
	public function add($userInfo, $groupList) {
		$data = array(
				"User" => array(
						"uri" => (string)$userInfo->author->uri,
						"screen_name" => (string)$userInfo->author->name,
						"group_list" => json_encode($groupList),
						"password" => (string)$userInfo->author->password,
						"oauth_token" => $_SESSION['oauth_access_token'],
						"oauth_token_secret" => $_SESSION['oauth_access_token_secret'],
						"flag" => 0,
				)
		);
		return $this->save($data);
	}

	public function getInfo($userUri) {
		$userinfo = $this->find('first', array(
				'conditions' => array(
						'User.uri' => $userUri
				)
		));
		return $userinfo;
	}

	public function getGroupList($userUri) {
		$userinfo = $this->getInfo($userUri);
		return json_decode($userinfo["User"]["group_list"], true);
	}

}
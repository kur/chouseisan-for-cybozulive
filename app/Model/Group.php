<?php

class Group extends AppModel {

	public $name = 'Group';

	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'group_users',
			'foreignKey' => 'group_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Profile' => array(
			'className' => 'Profile',
			'joinTable' => 'group_profiles',
			'foreignKey' => 'group_id',
			'associationForeignKey' => 'profile_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
	);

	public function getAll() {
		return $this->find('all');
	}

	public function getInfo($groupId) {
		$group = $this->find('first', array(
			'conditions' => array(
				'Group.id' => $groupId
			)
		));
		return $group;
	}

	// 	public function getSelfId($groupId) {
	// 		$group = $this->getInfo($groupId);
	// 		$groupMember = json_decode($group["Group"]["member_list"], true);
	// 		return $groupMember["self"];
	// 	}

	public function add($groupList) {
		$result = array();
		foreach ($groupList as $key => $groupName) {
			$data = $this->find('first', array(
				'conditions' => array(
					'Group.uri' => $key
				),
			));
			if (empty($data)) {
				$this->create();
			}
			$data["Group"]["uri"] = $key;
			$data["Group"]["name"] = $groupName;
			$tmp = $this->save($data);
			$result[] = $tmp['Group']['id'];
		}
		return $result;
	}

	public function getUri($groupId) {
		$result = $this->find('first',
			array(
				'conditions' => array(
					'Group.id' => $groupId
				),
				'recursive' => -1
			));
		$res = -1;
		if (count($result)) {
			$res = $result['Group']['uri'];
		}
		return $res;

	}
	public function update($groupId, $groupName, $groupMemberList) {
		$groupData["Group"]["id"] = $groupId;
		$groupData["Group"]["name"] = $groupName;
		//		$groupData["Group"]["member_list"] = json_encode($groupMember);
		$groupData["Profile"] = $groupMemberList;
		return $this->save($groupData);
	}
}

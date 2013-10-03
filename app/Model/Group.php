<?php 
class Group extends AppModel {

	public $name = 'Group';

	public $hasMany = 'User';

	public function getInfo($groupId) {
		$group = $this->find('first',
				array(
						'conditions' => array(
								'Group.id' => $groupId
						),
						'limit' => 1,
						'recursive' => 1
				));
		return $group;
	}

	public function getSelfId($groupId) {
		$group = $this->getInfo($groupId);
		$groupMember = json_decode($group["Group"]["member_list"], true);
		return $groupMember["self"];
	}

	public function update($groupId, $groupName, $groupMember) {
		$groupData["Group"]["id"] = $groupId;
		$groupData["Group"]["name"] = $groupName;
		$groupData["Group"]["member_list"] = json_encode($groupMember);
		return $this->save($groupData);
	}
}
<?php 
class Event extends AppModel {

	public $name = 'Event';

	//var $belongsTo = 'Group';
	public $hasMany = 'Candidate';

	public $validate = array(
		'group_id' => array(
			'rule' => 'notEmpty'
		),
		'name' => array(
		'rule' => 'notEmpty'
		)
	);

	public function getInfo($eventId) {
		$event = $this->find('first',
				array(
						'conditions' => array(
								'Event.id' => $eventId
						),
						'limit' => 1,
						'recursive' => 2
				));
		return $event;
	}

	public function getList($groupList) {
		$conditions = array();
		// 検索式を作成する
		foreach ($groupList as $groupId => $group) {
			$tmp = array();
			$tmp["Event.group_id"] = $groupId;
			$conditions["OR"][] = $tmp;
		}
		$events = $this->find('all',
				array(
						'conditions' => $conditions,
						'recursive' => -1
				));
		$eventList = array();
		foreach ($events as $event) {
			$tmp = array();
			$tmp["id"] = $event["Event"]["id"];
			$tmp["name"] = $event["Event"]["name"];
			$tmp["description"] = $event["Event"]["description"];
			$tmp["created"] = $event["Event"]["created"];
			$eventList[$event["Event"]["group_id"]][] = $tmp;
		}
		return $eventList;
	}
	
	public function add($eventData, $userUri){
		$this->create();
		$eventData["Event"]["owner_id"] = $userUri;
		return $this->save($eventData); 
	}
	
// 	public function editDescription($eventId, $value){
	

// 	}

// 	public function addCandidate($eventId, $value){
		
// 	}

	public function removeCandidate($eventId, $candidateId) {
		$event = $this->getInfo($eventId);
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		$result = null;
		if (isset($candidateList["data"][$candidateId])) {
			unset($candidateList["data"][$candidateId]);
			$event["Event"]["candidate_list"] = json_encode($candidateList, JSON_FORCE_OBJECT);
			$result = $this->Event->save($event);
		}
		return $result;
	}
}
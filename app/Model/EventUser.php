<?php 
class EventUser extends AppModel {

	public $name = 'EventUser';

	public function getList($eventId) {
		$result = $this->find('all',
				array(
						'conditions' => array(
								'EventUser.event_id' => $eventId
						),
				));
		return $result;
	}

// 	public function getList($groupList) {
// 		$conditions = array();
// 		// 検索式を作成する
// 		foreach ($groupList as $groupId => $group) {
// 			$tmp = array();
// 			$tmp["Event.group_id"] = $groupId;
// 			$conditions["OR"][] = $tmp;
// 		}
// 		$events = $this->find('all',
// 				array(
// 						'conditions' => $conditions,
// 						'recursive' => -1
// 				));
// 		$eventList = array();
// 		foreach ($events as $event) {
// 			$tmp = array();
// 			$tmp["id"] = $event["Event"]["id"];
// 			$tmp["name"] = $event["Event"]["name"];
// 			$tmp["description"] = $event["Event"]["description"];
// 			$tmp["created"] = $event["Event"]["created"];
// 			$eventList[$event["Event"]["group_id"]][] = $tmp;
// 		}
// 		return $eventList;
// 	}
	
	public function add($eventData, $userUri){
		$this->create();
		$eventData["Event"]["owner_id"] = $userUri;
		return $this->save($eventData); 
	}
}
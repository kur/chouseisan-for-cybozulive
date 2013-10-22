<?php

class Event extends AppModel {

	public $name = 'Event';

	//var $belongsTo = 'Group';
	//public $hasMany = 'Candidate';

	public $validate = array(
		'group_id' => array(
			'rule' => 'notEmpty'
		),
		'name' => array(
			'rule' => 'notEmpty'
		)
	);

	/**
	 * すべてのイベント情報を取得
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getAll() {
		return $this->find('all');
	}

	/**
	 * 指定したイベント情報を取得
	 * @param unknown $eventId
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getInfo($eventId = -1) {
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

	/**
	 * 指定したグループに所属するイベント情報を取得
	 * @param unknown $groupList
	 * @return Ambigous <multitype:, multitype:NULL unknown >
	 */
	public function getList($groupList = null) {
		if ($groupList == null) {
			return array();
		}
		$conditions = array();
		// 検索式を作成する
		foreach ($groupList as $groupId => $groupName) {
			$tmp = array();
			$tmp["Event.group_id"] = $groupId;
			$conditions["OR"][] = $tmp;
		}
		$events = $this->find('all', array(
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

	/**
	 * イベントを追加 
	 * @param unknown $eventData
	 * @param unknown $userUri
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function add($eventData, $userUri) {
		$this->create();
		$eventData["Event"]["owner_id"] = $userUri;
		return $this->save($eventData);
	}

	/**
	 * イベントの説明を編集
	 * @param unknown $eventId
	 * @param unknown $description
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function editDescription($eventId, $description) {
		$event = $this->getInfo($eventId);
		$event["Event"]["description"] = $description;
		return $this->save($event);
	}

	/**
	 * 候補リストを取得（flag=trueのときは全部）
	 * @param unknown $eventId
	 * @param number $flag
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function getCandidate($eventId, $flag = false, $onlyData = false) {
		$event = $this->getInfo($eventId);
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		if (!$flag) {
			foreach ($candidateList["data"] as $key => $candidate) {
				if ($candidate["flag"] == 0) {
					unset($candidateList["data"][$key]);
				}
			}
		}
		if ($onlyData) {
			$candidateList = $candidateList["data"];
		}

		return $candidateList;
	}

	/**
	 * 候補を追加
	 * @param unknown $eventId
	 * @param unknown $value
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function addCandidate($eventId, $value) {
		$event = $this->getInfo($eventId);
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		$tmp["value"] = $value;
		$tmp["flag"] = 1;
		$candidateList["data"][] = $tmp;
		$candidateList["data"][count($candidateList["data"]) - 1]["id"] = count($candidateList["data"]);
		$event["Event"]["candidate_list"] = json_encode($candidateList, JSON_FORCE_OBJECT);
		return $this->save($event);
	}

	/**
	 * 候補を削除
	 * @param unknown $eventId
	 * @param unknown $candidateId
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function removeCandidate($eventId, $candidateId) {

		$event = $this->getInfo($eventId);
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		$result = false;
		
		if (!isset($candidateList["data"])) {
			return $result;
		}
		foreach ($candidateList["data"] as &$candidate) {
			if ($candidate["id"] == $candidateId) {
				$candidate["flag"] = 0;
				$result = true;
			}
		}
		if ($result) {
			$event["Event"]["candidate_list"] = json_encode($candidateList, JSON_FORCE_OBJECT);
			$result = $this->save($event);
		}
		return $result;
	}
}

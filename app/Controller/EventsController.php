<?php

class EventsController extends AppController {

	public $uses = array(
		"Event", "Group", "User", "Registration"
	);

	public $components = array('Auth', 'CybozuLive');
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		
	}
/**
 * イベントのリストを表示する(リダイレクト)
 */
	public function index() {
		$this->redirect(
				array(
					'controller' => 'events', 'action' => 'viewall'
				));
	}
/**
 * 自分が所属しているグループのイベントリストを表示する
 */
	public function viewall() {
		// 自分の所属するグループ一覧を取得する
 		$groupList = $this->__getGroupList($this->Auth->User());

 		// グループIDに一致するイベント一覧を取得する
 		// 検索式を作成する
		foreach ($groupList as $groupId => $group) {
			$tmp = array();
			$tmp["Event.group_id"] = $groupId;
			$conditions["OR"][] = $tmp;
		}
		$eventList = $this->Event->find('list',
				array(
						'conditions' => $conditions,
						'fields' => array(
								'Event.id', 'Event.name',
								'Event.group_id'
						),
						'recursive' => -1
				));
		$this->set('groupList', $groupList);
		$this->set('eventList', $eventList);
	}
/**
 * イベントの詳細を表示する
 */

	public function view() {
		$eventId = $this->params['named']['eventId'];
		$this->set('eventId', $eventId);

		// イベントに関する情報を取得する
		$event = $this->Event
			->find('all',
				array(
					'conditions' => array(
						'Event.id' => $eventId
					), 'limit' => 1, 'recursive' => 2
				));
		$this->set('eventName', $event[0]["Event"]["name"]);
		$this->set('eventDescription', $event[0]["Event"]["description"]);

		// テーブルヘッダーを生成
		$tableHeaders[] = "";
		foreach ($event[0]["Candidate"] as $candidate) {
			$tableHeaders[] = $candidate["name"];
		}
		$tableHeaders[] = "";
		$this->set('tableHeaders', $tableHeaders);

		// 各自の回答を取得する
		$registrations = $this->Registration
			->find('list',
				array(
					'conditions' => array(
						'Registration.event_id' => $eventId
					),
					'fields' => array(
						'Registration.candidate_id', 'Registration.value',
						'Registration.user_id'
					)
				));

		$tableData = array();
		foreach ($event[0]["Group"]["User"] as $user) {
			$tmpRow = array();
			$tmpRow[] = $user["name"];
			foreach ($event[0]["Candidate"] as $candidate) {
				if (isset($registrations[$user["id"]][$candidate["id"]])) {
					$tmpRow[] = $registrations[$user["id"]][$candidate["id"]];
				} else {
					$tmpRow[] = 0;
				}
			}
			$tableData[$user["id"]] = $tmpRow;
		}
		$this->set('tableData', $tableData);
		return;

		// テーブルデータを生成
		$cntUsers = count($event[0]["Group"]["User"]);
		$cntCandidates = count($event[0]["Candidate"]);
		//var_dump($registrationData);

		// [$userid][$candidate]の連想配列に
		$registrationDataArray = $this
			->__cteateRegistrationDataArray($registrationData);

		$tableCells = array();
		for ($i = 0; $i < $cntUsers; $i++) {
			$tmpRow = array();
			$tmpRow[] = $event[0]["Group"]["User"][$i]["name"];
			// candidate
			for ($j = 0; $j < $cntCandidates; $j++) {
				$targetUserId = $event[0]["Group"]["User"][$i]["id"];
				$targetCandidateId = $event[0]["Candidate"][$j]["id"];
				if (isset(
					$registrationDataArray[$targetUserId][$targetCandidateId])) {
					$tmpRow[] = $registrationDataArray[$targetUserId][$targetCandidateId];
				} else {
					$tmpRow[] = 0;
				}

			}
			$tmpRow[] = "";
			$tableCells[] = $tmpRow;
		}

		$this->set('tableCells', $tableCells);
	}

	private function __cteateRegistrationDataArray($registrationData) {
		$data = array();
		$cntRegistrationData = count($registrationData);
		for ($i = 0; $i < $cntRegistrationData; $i++) {
			$userId = $registrationData[$i]["Registration"]["user_id"];
			$candidateId = $registrationData[$i]["Registration"]["candidate_id"];
			// 			var_dump($userId);
			// 			var_dump($candidateId);
			$data[$userId][$candidateId] = $registrationData[$i]["Registration"]["value"];
		}
		return $data;
	}

	private function  __getGroupList($User) {
		$userinfo = $this->User->find('all', array(
				'conditions' => array(
						'user_uri' => $User["User"]["user_uri"]
				),
				'limit' => 1
		));
		$groupList =  $this->CybozuLive->getGroupList(
				$userinfo[0]["User"]["oauth_token"],
				$userinfo[0]["User"]["oauth_token_secret"]);
		return $groupList;
	}
/**
 * 新しいイベントを作成する
 */
	public function create() {
		$User = $this->Auth->User();
		if ($this->request->is('post')) {
			$this->Event->create();
			$this->request->data["Event"]["owner_id"] = $User["User"]["user_uri"];
			var_dump($this->request->data);
			if ($this->Event->save($this->request->data)) {
				$this->Session->setFlash('イベントが作成されました');
				$this->redirect(array(
						'action' => 'index'
					));
			} else {
				$this->Session->setFlash('イベントを作成できませんでした');
			}
		} else{
			// Cybouzu Live からイベント一覧を取得
			$groupList = $this->__getGroupList($User);
			$this->set('groupList', $groupList);
		}
		
		
		
		return;
		if ($this->request->is('post')) {
			$this->Event->create();
			if ($this->Event->save($this->request->data)) {
				$this->Session->setFlash('イベントが作成されました');
				$this->redirect(array(
						'action' => 'index'
					));
			} else {
				$this->Session->setFlash('イベントを作成できませんでした');
			}
		}
		$groupList = $this->Group
			->find('list', array(
				'recursive' => -1
			));
// 			var_dump($groupList);
// 		$this->set('groupList', $groupList);
	}

}

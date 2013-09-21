<?php

class EventsController extends AppController {

	public $uses = array(
		"Event", "Group", "User", "Registration"
	);

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
 * イベントのリストを表示する
 */
	public function viewall() {
		$this->set('events', $this->Event->find('all'));
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

/**
 * 新しいイベントを作成する
 */
	public function create() {
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
		$this->set('groupList', $groupList);
	}

}

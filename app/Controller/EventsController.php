<?php

class EventsController extends AppController {

	public $uses = array(
		"Event", "Group", "User", "EventUser",
	);

	public $components = array('Auth', 'Session', 'CybozuLive');

	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('index', 'view');
		$requestUrl = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login',
			'?' => array('requesturl' => $requestUrl));
	}

/**
 * 自分が所属しているグループのイベントリストを表示する
 */
	public function index() {
		$user = $this->Auth->User();

		// 自分の所属するグループ一覧を取得する
 		$groupList = $this->User->getGroupList($user["User"]["uri"]);
 		$this->set('groupList', $groupList);

		// グループIDに一致するイベント一覧を取得する
 		$eventList = $this->Event->getList($groupList);
 		$this->set('eventList', $eventList);
	}

/**
 * イベントの詳細を表示する
 */

	public function view() {
		$user = $this->Auth->User();
		$this->set('user', $user);
		$eventId = $this->params['named']['eventId'];
		//		$this->set('eventId', $eventId);
	
		// イベントに関する情報を取得
		$event = $this->Event->getInfo($eventId);
		$this->set('event', $event);
	
		// グループのメンバー一覧を取得
		$group = $this->Group->getInfo($event["Event"]["group_id"]);
		$groupMember = json_decode($group["Group"]["member_list"],true);
		$this->set('group', $group);
	
		//グループにおけるユーザのIDを取得する
		$this->set('userIdInGroup', $groupMember["self"]);
	
		// イベントの候補を取得
		$candidateList = json_decode($event["Event"]["candidate_list"], true);

		// イベント候補がある場合
		$tableHeaders[] = "";
		if (isset($candidateList["data"])) {
			// テーブルヘッダーを生成
			foreach ($candidateList["data"] as $key => $name) {
				$tableHeaders[] = $name;
			}
			$tableHeaders[] = "";
			$this->set('tableHeaders', $tableHeaders);
	
			// 各自の回答を取得する
			$registrations = $this->EventUser->getList($eventId);
			$registrationData = array();
			foreach ($registrations as $key => $registration) {
				$registrationData[$registration["EventUser"]["user_id"]] =
				json_decode($registration["EventUser"]["value"], true);
			}
				
			// 回答を整形する
			$tableData = array();
			foreach ($groupMember["member"] as $key => $name) {
				$tmpRow = array();
				$tmpRow[] = $name;
				foreach ($candidateList["data"] as $candidateId => $candidateName) {
					if (isset($registrationData[$key][$candidateId])) {
						$tmpRow[] = $registrationData[$key][$candidateId];
					} else {
						$tmpRow[] = 0;
					}
				}
				$tableData[$key] = $tmpRow;
			}
			$this->set('tableData', $tableData);
		}
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

// 	private function __getGroupMember($User, $groupId) {
// 		$userinfo = $this->User->find('all', array(
// 				'conditions' => array(
// 						'User.uri' => $User["User"]["uri"]
// 				),
// 				'limit' => 1
// 		));
// 		$groupInfo = $this->CybozuLive->getGroupMember(
// 				$userinfo[0]["User"]["oauth_token"],
// 				$userinfo[0]["User"]["oauth_token_secret"],
// 				$groupId);
// 		return $groupInfo;
// 	}
/**
 * 新しいイベントを作成する
 */
	public function create() {
		$user = $this->Auth->User();
		
		$groupList = $this->User->getGroupList($user["User"]["uri"]);
		$this->set('groupList', $groupList);
		if ($this->request->is('post') &&
		// ユーザが該当グループに参加しているかを確認
				isset($groupList[$this->request->data["Event"]["group_id"]])) {
			
			$isSuccess = true;
			// イベントを追加
			if (!$this->Event->Add($this->request->data, $user["User"]["uri"])) {
				$isSuccess = false;
			}
			// グループメンバーを更新
			$userInfo = $this->User->getInfo($user["User"]["uri"]);
			if(!$this->Group->update($this->request->data["Event"]["group_id"],
					$groupList[$this->request->data["Event"]["group_id"]],
					$this->__getGroupMember($userInfo, $this->request->data["Event"]["group_id"])
				)) {
				$isSuccess = false;
			}
			if ($isSuccess) {
				$this->Session->setFlash('イベントが作成されました', 'default', array('class' => 'alert alert-success'));
			}
			$this->redirect(array(
					'action' => 'index'
			));
		}
	}
	private function __checkPermission($eventId){
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);

		$event = $this->Event->getInfo($eventId);
		$result = false;
		if ($userInfo["User"]["uri"] == $event["Event"]["owner_id"]) {
				$result = true;
		}
		return $result;
	}
	
	public function removeCandidate() {
		$this->autoRender = false;
		// 認証する
		$eventId = $this->params['named']['eventId'];
		if($this->__checkPermission($eventId) == false){
			$this->redirect(array('controller' => 'events', 'action' => 'index'));
		}
		$candidateId = $this->params['named']['candidateId'];
		if($this->Event->removeCandidate($eventId, $candidateId)) {
			$this->Session->setFlash('選択肢削除されました', 'default', array('class' => 'alert alert-success'));			
		} else {
			$this->Session->setFlash('選択肢を削除できませんでした');
		}
		$this->redirect(array('controller' => 'events',
				'action' => 'edit',
				'eventId' => $eventId));
	}
	private function __editDescription(){
		$user = $this->Auth->User();
		$eventId = $this->request->data["Event"]["id"];
		$event = $this->Event->getInfo($eventId);
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		if($userInfo["User"]["uri"] != $event["Event"]["owner_id"]){
			$this->redirect(array('controller' => 'events',
					'action' => 'index'));
		}
		if ($this->Event->save($this->request->data)) {
			$this->Session->setFlash('説明が編集されました', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('controller' => 'events',
					'action' => 'edit',
					'eventId' => $this->request->data["Event"]["id"]));
		} else {
			$this->Session->setFlash('選択肢を追加できませんでした');
		}
	}
	private function __editCandidate(){
		$user = $this->Auth->User();
		$eventId = $this->request->data["event_id"];
		$event = $this->Event->getInfo($eventId);
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		if($userInfo["User"]["uri"] != $event["Event"]["owner_id"]){
			$this->redirect(array('controller' => 'events',
					'action' => 'index'));
		}
		$event = $this->Event->getInfo($this->request->data["event_id"]);
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		
		if (!isset($candidateList["max"])) {
			$candidateList["max"] = 0;
		}
		$candidateList["data"][$candidateList["max"]] = $this->request->data["name"];
		$candidateList["max"]++;
		$event["Event"]["candidate_list"] = json_encode($candidateList, JSON_FORCE_OBJECT);
		
		if ($this->Event->save($event)) {
			$this->Session->setFlash('選択肢が追加されました', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('controller' => 'events',
					'action' => 'edit',
					'eventId' => $this->request->data["event_id"]));
		} else {
			$this->Session->setFlash('選択肢を追加できませんでした');
		}
	}
	private function __editview(){
		$user = $this->Auth->User();
		// 権限確認
		$eventId = $this->params['named']['eventId'];
		$event = $this->Event->getInfo($eventId);
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		if($userInfo["User"]["uri"] != $event["Event"]["owner_id"]){
			$this->redirect(array('controller' => 'events',
					'action' => 'index'));
		}
		
		// 引数がなかった場合、イベント一覧へ
		if(!isset($this->params['named']['eventId'])){
			$this->redirect(array('controller' => 'events',
					'action' => 'index'));
		}
		$eventId = $this->params['named']['eventId'];
		// イベントに関する情報を取得
		$event = $this->Event->getInfo($eventId);
		$this->set('event', $event);
			
		
		// グループに関する情報を取得
		$group = $this->Group->getInfo($event["Event"]["group_id"]);
		$this->set('group', $group["Group"]);
		
		$tableHeaders[] = "候補日";
		$tableHeaders[] = "操作";
		$this->set('tableHeaders', $tableHeaders);
		
		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		$tableData = array();
			
		if (isset($candidateList["data"])) {
			foreach ($candidateList["data"] as $key => $name) {
				$tmpRow = array();
				$tmpRow[] = $name;
				$tmpRow[] = "del";
				$tableData[$key] = $tmpRow;
			}
		}
		$this->set('tableData', $tableData);
		
		
	}
	
/**
 * イベントを編集する
 */
	public function edit() {
		// 振り分け
		if(isset($this->params['named']['eventId'])) {
			$this->__editview();
		} elseif ($this->request->is('post')) {
			if($this->request->data["type"] == "description") {
				$this->__editDescription();
			} elseif($this->request->data["type"] == "candidate" && $this->request->data["name"] != "") {
				$this->__editCandidate();
			}
		}
		return;
		// 権限確認
				
		// 		$eventId = null;
		// 		if(isset($this->request->data["Event"]["id"])) {
		// 			$eventId = $this->request->data["Event"]["id"];
		// 		} elseif(isset($this->params['named']['eventId'])) {
		// 			$eventId = $this->params['named']['eventId'];
		// 		}
		// 		$event = $this->Event->getInfo($eventId);
		// 		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		// 		if($userInfo["User"]["uri"] != $event["Event"]["owner_id"]){
		// 			$this->redirect(array('controller' => 'events',
		// 					'action' => 'index'));
		// 		}
		// 		// POSTの場合
		// 		if ($this->request->is('post') ) {
		// 			if($this->request->data["type"] == "description") {
		// 			} elseif ($this->request->data["type"] == "candidate" && $this->request->data["name"] != "") {
		// 			}else{
		// 				$this->redirect(array('controller' => 'events',
		// 						'action' => 'edit',
		// 						'eventId' => $this->request->data["event_id"]));
		// 			}
		// 		// POSTで無い場合
		// 		} else {
		// 		}
	}
/**
 * 回答確認及び登録画面
 */
	public function register() {
		// データ受信時の処理
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$eventId = $this->params["data"]["event_id"];
			$userId = $this->params["data"]["user_id"];

			// 現在の登録データを取得
			$registrations = $this->EventUser->find('all', array(
					'conditions' => array(
							'EventUser.event_id' => $eventId,
							'EventUser.user_id' => $userId,
					),
					'recursive' => -1,
			));
			if (count($registrations) == 0) {
				$registrations[0]["EventUser"]["event_id"] = $eventId;
				$registrations[0]["EventUser"]["user_id"] = $userId;
				$registrationData = array();
			} else {
				$registrationData = json_decode($registrations[0]["EventUser"]["value"], true);
			}
			foreach ($this->params["data"]["value"] as $key => $value) {
				// 存在しない場合 or 異なる場合
				if (!isset($registrationData[$key])
					|| $registrationData[$key] != $value) {
					$registrationData[$key] = $value;
				}
			}

			$registrations[0]["EventUser"]["value"] = json_encode($registrationData, JSON_FORCE_OBJECT);
			$this->EventUser->save($registrations[0]);
			$this->Session->setFlash('選択肢が追加されました', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('controller' => 'events', 'action' => 'register',
					"userId" => $userId,
					"eventId" => $eventId));
		} else {
			$userId = $this->params['named']['userId'];
			$eventId = $this->params['named']['eventId'];
			$this->set('userId', $userId);
			$this->set('eventId', $eventId);

			// event_idを取得
			$event = $this->Event->find('all', array(
					'conditions' => array('Event.id' => $eventId),
					'recursive' => 1
			));
			$this->set('event', $event);
			$this->set('eventName', $event[0]["Event"]["name"]);
			$this->set('eventOwnerId', $event[0]["Event"]["owner_id"]);
			$this->set('eventDescription', $event[0]["Event"]["description"]);

			$group = $this->Group
			->find('all',
					array(
							'conditions' => array(
									'Group.id' => $event[0]["Event"]["group_id"]
							), 'limit' => 1, 'recursive' => 1
					));
			$groupMember = (array)json_decode($group[0]["Group"]["member_list"]);
			$this->set('groupName', $group[0]["Group"]["name"]);
			// テーブルヘッダーを生成
			$user = $this->User->find('all', array(
					'conditions' => array('User.uri' => $userId),
					'recursive' => -1
			));
			$tableHeaders[] = "候補";
			$tableHeaders[] = "回答";
			$this->set('tableHeaders', $tableHeaders);
			// テーブル本体を生成
			$registration = $this->EventUser->find('all', array(
					'conditions' => array(
							'EventUser.event_id' => $eventId,
							'EventUser.user_id' => $userId,
					),
					'recursive' => -1
			));

			$candidateList = json_decode($event[0]["Event"]["candidate_list"], true);
			$registrationData = array();
			if (count($registration) > 0) {
				$registrationData = json_decode($registration[0]["EventUser"]["value"], true);
			}
			$tableData = array();
			foreach ($candidateList["data"] as $key => $name) {
				$tmpCells = array();
				$tmpCells[] = $name;
				if (isset($registrationData[$key])) {
					$tmpCells[] = $registrationData[$key];
				} else {
					$tmpCells[] = "";
				}

				$tableData[$key] = $tmpCells;
			}
			$this->set('tableData', $tableData);

		}
	}
}

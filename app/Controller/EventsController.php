<?php

class EventsController extends AppController {

	public $uses = array(
		"Event",
		"Group",
		"User",
		"EventProfile",
		"Profile"
	);

	public $components = array(
		'Auth',
		'Session',
		'CybozuLive'
	);

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

		return true;
	}

	/**
	 * イベントの詳細を表示する
	 */
	public function view() {
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		$this->set('user', $user);
		$this->set('userInfo', $userInfo);
		$eventId = $this->params['named']['eventId'];
		$event = $this->Event->getInfo($eventId);

		$groupList = $this->User->getGroupList($user["User"]["uri"]);

		// 権限を確認する
		// イベントが存在しない場合　または自分が所属しないグループのイベントの場合は、イベント一覧へ
		if (!isset($event) || empty($event) || !isset($groupList[$event['Event']['group_id']])) {
			$this->redirect(array(
				'controller' => 'events',
				'action' => 'index'
			));
			return false;
		}
		$this->set('event', $event);
		// グループのメンバー一覧を取得
		$group = $this->Group->getInfo($event["Event"]["group_id"]);
		$this->set('group', $group);

		// イベントの候補を取得
		$registrations = $this->EventProfile->getInfo($eventId);
		$this->set('registrations', $registrations);
		return true;

		// イベント候補がある場合
		// 		$tableHeaders[] = "";
		// 		if (isset($candidateList["data"])) {
		// 			// テーブルヘッダーを生成
		// 			foreach ($candidateList["data"] as $key => $name) {
		// 				$tableHeaders[] = $name;
		// 			}
		// 			$tableHeaders[] = "";
		// 			$this->set('tableHeaders', $tableHeaders);

		// 			// 各自の回答を取得する
		// 			$registrations = $this->EventUser->getList($eventId);
		// 			$registrationData = array();
		// 			foreach ($registrations as $key => $registration) {
		// 				$registrationData[$registration["EventUser"]["user_id"]] = json_decode(
		// 					$registration["EventUser"]["value"], true);
		// 			}

		// 			// 回答を整形する
		// 			$tableData = array();
		// 			foreach ($groupMember["member"] as $key => $name) {
		// 				$tmpRow = array();
		// 				$tmpRow[] = $name;
		// 				foreach ($candidateList["data"] as $candidateId => $candidateName) {
		// 					if (isset($registrationData[$key][$candidateId])) {
		// 						$tmpRow[] = $registrationData[$key][$candidateId];
		// 					} else {
		// 						$tmpRow[] = 0;
		// 					}
		// 				}
		// 				$tableData[$key] = $tmpRow;
		// 			}
		// 			$this->set('tableData', $tableData);
		// 		}
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

	private function __getGroupMember($User, $groupId) {
		$userInfo = $this->User->getInfo($User["User"]["uri"]);
		$groupInfo = $this->CybozuLive->getGroupMember($userInfo["User"]["oauth_token"],
			$userInfo["User"]["oauth_token_secret"], $groupId);
		return $groupInfo;
	}

	/**
	 * 新しいイベントを作成する
	 */
	public function create() {
		// ユーザ情報を取得
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		// ユーザの所属するグループリストを取得
		$groupList = $this->User->getGroupList($user["User"]["uri"]);
		if ($this->request->is('post')) {

			// ユーザが該当グループに参加しているかを確認
			if (isset($groupList[$this->request->data["Event"]["group_id"]]) == false) {
				return false;
			}
			$isSuccess = true;

			// イベントを追加
			if (!$this->Event->Add($this->request->data, $user["User"]["id"])) {
				$isSuccess = false;
			}

			// group Profileを取得
			$groupMembers = $this->CybozuLive->getGroupMember($userInfo["User"]["oauth_token"],
				$userInfo["User"]["oauth_token_secret"],
				$this->Group->getUri($this->request->data["Event"]["group_id"]));
			debug($groupMembers);
			$groupMemberList = array();
			foreach ($groupMembers['member'] as $uri => $name) {
				// 同じURLがあるかを確認する
				$tmpMember = $this->Profile->find('first',
					array(
						'conditions' => array(
							'Profile.uri' => $uri
						)
					));

				if (empty($tmpMember)) {
					$this->Profile->create();
					$profileData = array(
						'Profile' => array(
							'uri' => $uri,
							'screen_name' => $name
						)
					);
					$tmpMember = $this->Profile->save($profileData);
				}
				$groupMemberList[] = $tmpMember['Profile']['id'];
			}
			debug($groupMemberList);
			// グループメンバーを更新
			$this->Group->update($this->request->data["Event"]["group_id"],
					$groupList[$this->request->data["Event"]["group_id"]], $groupMemberList);

			if ($isSuccess) {
				$this->Session->setFlash('イベントが作成されました', 'default',
					array(
						'class' => 'alert alert-success'
					));
			}
			$this->redirect(array(
				'action' => 'index'
			));
			return $isSuccess;
		}
		$this->set('groupList', $groupList);

	}
	private function __checkPermission($eventId) {

		return $result;
	}

	public function removeCandidate() {
		$this->autoRender = false;
		// 自分のイベントかどうかを判断する
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);

		// 引数を確認し、イベント情報を取得する
		$eventId = -1;
		if (isset($this->params['named']['eventId'])) {
			$eventId = $this->params['named']['eventId'];
		}
		$event = $this->Event->getInfo($eventId);

		// イベントが存在しない場合　または オーナーが自分ではない場合は、イベント一覧へ
		if (!isset($event) || !$event || $userInfo["User"]["id"] != $event["Event"]["owner_id"]) {
			$this->redirect(array(
				'controller' => 'events',
				'action' => 'index'
			));
			return false;
		}
		$result = false;
		$candidateId = $this->params['named']['candidateId'];
		if ($this->Event->removeCandidate($eventId, $candidateId)) {
			$this->Session->setFlash('選択肢削除されました', 'default', array(
				'class' => 'alert alert-success'
			));
			$result = true;
		} else {
			$this->Session->setFlash('選択肢を削除できませんでした');
		}

		$this->redirect(array(
			'controller' => 'events',
			'action' => 'edit',
			'eventId' => $eventId
		));
		return $result;
	}
	private function __editDescription() {
		$user = $this->Auth->User();
		$eventId = -1;
		if (isset($this->request->data["Event"]["id"])) {
			$eventId = $this->request->data["Event"]["id"];
		}
		$event = $this->Event->getInfo($eventId);
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		$result = false;
		if (!isset($event) || !$event || $userInfo["User"]["uri"] != $event["Event"]["owner_id"]) {
			$this->redirect(array(
				'controller' => 'events',
				'action' => 'index'
			));
			return $result;
		}

		$result = $this->Event->editDescription($eventId, $this->request->data["Event"]["description"]);

		if ($result) {
			$this->Session->setFlash('説明が編集されました', 'default', array(
				'class' => 'alert alert-success'
			));
			$this->redirect(
				array(
					'controller' => 'events',
					'action' => 'edit',
					'eventId' => $this->request->data["Event"]["id"]
				));
			$result = true;
		} else {
			$this->Session->setFlash('選択肢を追加できませんでした');
		}
		return $result;
	}
	private function __editCandidate() {
		// 		$user = $this->Auth->User();

		// 		$eventId = -1;
		// 		if (isset($this->request->data["eventId"])) {
		// 			$eventId = $this->request->data["eventId"];
		// 		}
		// 		$event = $this->Event->getInfo($eventId);
		// 		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		// 		if (!isset($event) || !$event || $userInfo["User"]["uri"] != $event["Event"]["owner_id"]) {
		// 			$this->redirect(array(
		// 				'controller' => 'events',
		// 				'action' => 'index'
		// 			));
		// 			return false;
		// 		}
		// 		$event = $this->Event->getInfo($this->request->data["eventId"]);

		// 追加
		$result = $this->Event->addCandidate($eventId, $this->request->data["name"]);

		if ($result) {
			$this->Session->setFlash('選択肢が追加されました', 'default', array(
				'class' => 'alert alert-success'
			));
			$this->redirect(
				array(
					'controller' => 'events',
					'action' => 'edit',
					'eventId' => $this->request->data["eventId"]
				));
		} else {
			$this->Session->setFlash('選択肢を追加できませんでした');
		}
		return $result;
	}
	/**
	 * GETアクセスが有った場合の処理
	 * @return void|boolean
	 */
	private function __editview() {
		$user = $this->Auth->User();

		// 引数を確認し、イベント情報を取得する
		if (isset($this->params['named']['eventId'])) {
			$eventId = $this->params['named']['eventId'];
			$event = $this->Event->getInfo($eventId);
			$this->set('event', $event);
		}

		// 権限を確認する
		$userInfo = $this->User->getInfo($user["User"]["uri"]);

		// イベントが存在しない場合　または オーナーが自分ではない場合は、イベント一覧へ
		if (!isset($event) || !$event || $userInfo["User"]["uri"] != $event["Event"]["owner_id"]) {
			$this->redirect(array(
				'controller' => 'events',
				'action' => 'index'
			));
			return false;
		}
		// グループに関する情報を取得
		$group = $this->Group->getInfo($event["Event"]["group_id"]);
		$this->set('group', $group["Group"]);

		// 		$tableHeaders[] = "候補日";
		// 		$tableHeaders[] = "操作";
		// 		$this->set('tableHeaders', $tableHeaders);

		//		$candidateList = json_decode($event["Event"]["candidate_list"], true);
		// 		$tableData = array();

		// 		if (isset($candidateList["data"])) {
		// 			foreach ($candidateList["data"] as $key => $name) {
		// 				$tmpRow = array();
		// 				$tmpRow[] = $name;
		// 				$tmpRow[] = "del";
		// 				$tableData[$key] = $tmpRow;
		// 			}
		// 		}
		//		$this->set('candidateList', $candidateList);

	}

	/**
	 * イベントを編集する
	 */
	public function edit() {
		// 自分のイベントかどうかを判断する
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);

		// 引数を確認し、イベント情報を取得する
		$eventId = -1;
		if (isset($this->params['named']['eventId'])) {
			$eventId = $this->params['named']['eventId'];
		} elseif (isset($this->request->data["eventId"])) {
			$eventId = $this->request->data["eventId"];
		} elseif (isset($this->request->data["Event"]["id"])) {
			$eventId = $this->request->data["Event"]["id"];
		}
		$event = $this->Event->getInfo($eventId);
		$this->set('event', $event);

		// イベントが存在しない場合　または オーナーが自分ではない場合は、イベント一覧へ
		if (!isset($event) || empty($event) || $userInfo["User"]["id"] != $event["Event"]["owner_id"]) {
			$this->redirect(array(
				'controller' => 'events',
				'action' => 'index'
			));
			return false;
		}
		// 自分のイベントであるなら
		$result = false;
		if ($this->request->is('post')) {
			if ($this->request->data["type"] == "description") {
				$result = $this->Event->editDescription($eventId, $this->request->data["Event"]["description"]);

				if ($result) {
					$this->Session->setFlash('説明が編集されました', 'default',
						array(
							'class' => 'alert alert-success'
						));
					$result = true;
				} else {
					$this->Session->setFlash('選択肢を追加できませんでした');
				}
				// 候補の追加
			} elseif ($this->request->data["type"] == "candidate" && $this->request->data["name"] != "") {
				$result = $this->Event->addCandidate($eventId, $this->request->data["name"]);
				if ($result) {
					$this->Session->setFlash('選択肢が追加されました', 'default',
						array(
							'class' => 'alert alert-success'
						));

				} else {
					$this->Session->setFlash('選択肢を追加できませんでした');
				}
			}
			//			if ($result) {
			$this->redirect(
				array(
					'controller' => 'events',
					'action' => 'edit',
					'eventId' => $eventId
				));
			//			}

		} else {
			// グループに関する情報を取得
			$group = $this->Group->getInfo($event["Event"]["group_id"]);
			$this->set('group', $group["Group"]);

		}

		return $result;
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
		$user = $this->Auth->User();
		$userInfo = $this->User->getInfo($user["User"]["uri"]);
		$this->set('user', $user);
		$this->set('userInfo', $userInfo);

		// データ受信時の処理
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$eventId = $this->params["data"]["event_id"];
			$profileId = $this->params["data"]["profile_id"];
		

			
			// 現在の登録データを取得
			$registrations = $this->EventProfile->getInfo($eventId, $profileId);
			if (count($registrations) == 0) {
				$registrations[0]["EventProfile"]["event_id"] = $eventId;
				$registrations[0]["EventProfile"]["profile_id"] = $profileId;
				$registrationData = array();
			} else {
				$registrationData = json_decode($registrations[0]["EventProfile"]["value"], true);
			}
			foreach ($this->params["data"]["value"] as $key => $value) {
				// 存在しない場合 or 異なる場合
				if (!isset($registrationData[$key]) || $registrationData[$key] != $value) {
					$registrationData[$key] = $value;
				}
			}

			$registrations[0]["EventProfile"]["value"] = json_encode($registrationData, JSON_FORCE_OBJECT);

			$this->EventProfile->save($registrations[0]);
			$this->Session->setFlash('回答が更新されました', 'default', array(
				'class' => 'alert alert-success'
			));
			$this->redirect(
				array(
					'controller' => 'events',
					'action' => 'register',
					"profileId" => $profileId,
					"eventId" => $eventId
				));
		} else {

			$profileId = $this->params['named']['profileId'];
			$eventId = $this->params['named']['eventId'];
			$this->set('profileId', $profileId);
			$this->set('eventId', $eventId);

			// event_idを取得
			$event = $this->Event->getInfo($eventId);
			//debug($event);
			$this->set('event', $event);

			$group = $this->Group->getInfo($event["Event"]["group_id"]);
			//debug($group);
			$this->set('group', $group);
			// テーブルヘッダーを生成
			$registrations = $this->EventProfile->getInfo($eventId);
			$this->set('registrations', $registrations);
//			debug($registrations);


		}
	}
}

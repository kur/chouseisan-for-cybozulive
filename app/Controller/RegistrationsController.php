<?php


class RegistrationsController extends AppController {
	public $uses = array("Event","Group","User","Registration","Candidate");

	public $components = array('Auth', 'CybozuLive');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index(){
		
	}
/**
 * 回答確認及び登録画面
 */
	public function view(){
		// データ受信時の処理
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$this->change();
			return;
		}
		$userId = $this->params['named']['userId'];
		$eventId = $this->params['named']['eventId'];
		$this->set('userId', $userId);
		$this->set('eventId', $eventId);
		
		// event_idを取得
		$event = $this->Event->find('all', array(
				'conditions' => array('Event.id' => $eventId),
				//'limit' => 1,
				'recursive' => 1
		));

		$this->set('event', $event);
		
		// テーブルヘッダーを生成
		$user = $this->User->find('all', array(
				'conditions' => array('User.uri' => $userId),
				//'limit' => 1,
				'recursive' => -1
		));
		
		$tableHeaders[] ="候補";
		$tableHeaders[] ="回答";
		$this->set('tableHeaders', $tableHeaders);

		// テーブル本体を生成
		$registration = $this->Registration->find('list', array(
				'conditions' => array(
						'Registration.event_id' => $eventId,
						'Registration.user_id' => $userId,
				),
				'fields' => array('Registration.candidate_id','Registration.value'),
				'recursive' => 1
		));

		$tableData = array();
		foreach ($event[0]["Candidate"] as $candidate){
			$tmpCells = array();
			$tmpCells[] = $candidate["name"];
			if (isset($registration[$candidate["id"]])) {
				$tmpCells[] = $registration[$candidate["id"]];				
			} else {
				$tmpCells[] = "";
			}

			$tableData[$candidate["id"]] = $tmpCells;  
		}		
		$this->set('tableData', $tableData);
	}
	private function getRegistrationData($registration, $candidateId){
		$cntRegistration = count($registration);
		for($i = 0; $i < $cntRegistration; $i++){
			$value = 0;
			if($registration[$i]["Registration"]["candidate_id"] == $candidateId){
				$value = $registration[$i]["Registration"]["value"];
				break;
			}
		}	
		return $value;
	}
	private function change(){
		$eventId =  $this->params["data"]["event_id"];
		$userId =  $this->params["data"]["user_id"];
		
		// 現在の登録データを取得
		$registrations = $this->Registration->find('all', array(
				'conditions' => array(
						'Registration.event_id' => $eventId,
						'Registration.user_id' => $userId,
				),
				'recursive' => 1,

		));
		//var_dump($registrations);
		// 整形
		$registrationData = array();
		foreach ($registrations as $registration){
			$tmpCandidateId = $registration["Registration"]["candidate_id"];
			$tmpValue = $registration["Registration"]["value"];
			$tmpId = $registration["Registration"]["id"];
			$registrationData[$tmpCandidateId]["value"] = $tmpValue;
			$registrationData[$tmpCandidateId]["id"] = $tmpId;
		}
		foreach ($this->params["data"]["value"] as $key => $value){
			// 存在しない場合 or 異なる場合
			//var_dump($registrationData);
			if(!isset($registrationData[$key]) || $registrationData[$key]["value"] != $value){
				$this->Registration->create();
				if(isset($registrationData[$key]["id"])){
					$data["Registration"]["id"] = $registrationData[$key]["id"];
				}				
				$data["Registration"]["event_id"] = $eventId;
				$data["Registration"]["user_id"] = $userId;
				$data["Registration"]["candidate_id"] = $key;
				$data["Registration"]["value"] = $value;
				$this->Session->setFlash('変更されました');
				$this->Registration->save($data);
			}
			
		}

 		$this->redirect(array('controller' => 'registrations', 'action' => 'view',
 				"userId" => $userId,
 				"eventId" => $eventId));
	}
	public function  create(){
		
	}

}
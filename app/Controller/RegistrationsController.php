<?php


class RegistrationsController extends AppController {
	public $uses = array("Event","Group","User","Registration","Candidate");

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
				'conditions' => array('User.id' => $userId),
				//'limit' => 1,
				'recursive' => -1
		));
		$tableHeaders[] ="選択肢";
		$tableHeaders[] =$user[0]["User"]["name"];
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
// 		var_dump($registration);
// 		var_dump($event[0]["Candidate"]);
		
		$tableData = array();
		foreach ($event[0]["Candidate"] as $candidate){
			$tmpCells = array();
			$tmpCells[] = $candidate["name"];
			$tmpCells[] = $registration[$candidate["id"]];
			$tableData[$candidate["id"]] = $tmpCells;  
		}		
		$this->set('tableData', $tableData);
		
		
		
		//$cntCandidate = count($event[0]["Candidate"]);
// 		$tableIndex = array();
// 		foreach ($event[0]["Candidate"] as $candidate){
// 			$tmpRow = array();
// 			$tableIndex[] = $candidate["id"];
// 			$tmpRow[] = $candidate["name"];
// 			$tmpRow[] = $this->getRegistrationData($registration, $candidate["id"]);
// 			$tableCells[] = $tmpRow;
// 		}
// 		for($i = 0;$i < $cntCandidate; $i++ ){
// 			$tmpRow = array();
// 			$tableIndex[] = $event[0]["Candidate"][$i]["id"];
// 			$tmpRow[] = $event[0]["Candidate"][$i]["name"];
// 			$tmpRow[] = $this->getRegistrationData($registration, $event[0]["Candidate"][$i]["id"]);			
// 			$tableCells[] = $tmpRow;
// 		}
//		$this->set('tableIndex', $tableIndex);
//		$this->set('tableCells', $tableCells);
		
		return;
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
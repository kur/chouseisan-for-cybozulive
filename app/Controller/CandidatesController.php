<?php
class CandidatesController extends AppController {
	public $uses = array("Event","Group","User","Registration","Candidate");

	public function index(){
		$this->redirect(array('controller' => 'events', 'action' => 'viewall'));
	}
	// list
	public function viewall(){
		$res = $this->Event->find('all');
		//var_dump($res);
		$this->set('events', $res);
	}
	public function view(){
		$eventId = $this->params['named']['eventId'];
		$this->set('eventId', $eventId);
		
		
		// event_idを取得
		$event = $this->Event->find('all', array(
				'conditions' => array('Event.id' => $eventId),
				//'limit' => 1,
				'recursive' => 1
		));
		$this->set('event', $event);
		
		
		// テーブルヘッダーを生成
		$tableHeaders[] ="候補日";
		$tableHeaders[] ="操作";
		$this->set('tableHeaders', $tableHeaders);
		
		
		// テーブル本体を生成
		$cntCandidate = count($event[0]["Candidate"]);
		$tableIndex = array();
		$tableCells = array();
		for($i = 0;$i < $cntCandidate; $i++ ){
			$tmpRow = array();
			$tableIndex[] = $event[0]["Candidate"][$i]["id"];
			$tmpRow[] = $event[0]["Candidate"][$i]["name"];
			$tmpRow[] ="del";
			$tableCells[] = $tmpRow;
		}
		$this->set('tableIndex', $tableIndex);
		$this->set('tableCells', $tableCells);
		
	}
	private function cteateRegistrationDataArray($registrationData){
		$data = array();
		$cntRegistrationData = count($registrationData);
		for($i = 0; $i < $cntRegistrationData; $i++){
			$userId = $registrationData[$i]["Registration"]["user_id"];
			$candidateId = $registrationData[$i]["Registration"]["candidate_id"];
// 			var_dump($userId);
// 			var_dump($candidateId);
			$data[$userId][$candidateId]  = $registrationData[$i]["Registration"]["value"];
		}
		return $data;
	}
	private function getRegistrationData($registrationData, $userId, $candidateId){
 		//$cntRegistration = count($registrationData);
 		
// 		for($i = 0; $i < $cntRegistration; $i++){
 			$value = 0;
// 			if($registration[$i]["Registration"]["candidate_id"] == $candidateId){
// 				$value = $registration[$i]["Registration"]["value"];
// 				break;
// 			}
// 		}

		return $value;
	}
	public function  add(){
		if ($this->request->is('post')) {
			$this->Candidate->create();
			if ($this->Candidate->save($this->request->data)) {
				$this->Session->setFlash('選択肢が追加されました');
				$this->redirect(array('controller' => 'events',
						'action' => 'view',
						'eventId'=>$this->request->data["Candidate"]["event_id"]));
			} else {
				$this->Session->setFlash('選択肢を追加できませんでした');
			}
		}
		$eventId = $this->params['named']['eventId'];
		$this->set('eventId', $eventId);
		
	}

	public function  change($eventId = 0){
		
	}	
}
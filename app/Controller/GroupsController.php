<?php


class GroupsController extends AppController {

	public $uses = array("Event", "Group", "User", "Registration", "Candidate");

	// 	public function add() {
	// 		if ($this->request->is('post')) {
	// 			$this->Group->create();
	// 			if ($this->Group->save($this->request->data)) {
	// 				$this->Session->setFlash('グループが追加されました');
	// 				$this->redirect(array('controller' => 'groups',
	// 						'action' => 'view'));
	// 			} else {
	// 				$this->Session->setFlash('選択肢を追加できませんでした');
	// 			}
	// 		}
	// 	}

	public function view() {
		$groups = $this->Group->find('all');
		$this->set('groups', $groups);
	}

}
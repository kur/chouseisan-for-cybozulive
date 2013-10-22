<?php

class EventProfile extends AppModel {

	public $name = 'EventProfile';

	/**
	 * すべて取得
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getAll() {
		return $this->find('all');
	}

	/**
	 * 特定のイベントの特定のユーザに関する情報を取得
	 * @param unknown $eventId
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getInfo($eventId, $profileId = null) {
		$conditions['EventProfile.event_id'] = $eventId;
		if ($profileId) {
			$conditions['EventProfile.profile_id'] = $profileId;
		}

		$result = $this->find('all', array(
			'conditions' => array(
				'AND' => $conditions
			),
		));
		return $result;
	}
	/**
	 * データを更新する
	 * @param unknown $eventId
	 * @param unknown $profileId
	 * @param unknown $value
	 * @return Ambigous <mixed, boolean, multitype:>
	 */
	public function update($eventId, $profileId, $value) {
		$result = $this->getInfo($eventId, $profileId);
		// すでに存在する場合
		if (!$result) {
			$result["EventProfile"]["event_id"] = $eventId;
			$result["EventProfile"]["profile_id"] = $profileId;
		}
		$result["EventProfile"]["value"] = json_encode($value);

		return $this->save($result);
	}
}

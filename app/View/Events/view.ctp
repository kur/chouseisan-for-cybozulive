<?php
echo $this->Menu->getMenu(-1, $isLogin);
?>
<h3><?php echo $event["Event"]["name"] . " - " . $group["Group"]["name"]; ?></h3>
<div><?php echo $event["Event"]["description"]; ?></div>
<?php

// debug($event);
// debug($group);
// debug($registrations);
foreach ($registrations as $r) {
	$registrationsData[$r['EventProfile']['profile_id']] = json_decode($r['EventProfile']['value'], true);
}
if (empty($event["Event"]["candidate_list"])) {
?>
<div class="alert alert-info">
候補が登録されていません。候補を登録してください。
</div>
<?php
} else {
	$tableHeaders = array(
		''
	);

	// テーブルヘッダー作成
	$candidate_list = json_decode($event["Event"]["candidate_list"], true);
	foreach ($candidate_list['data'] as $candidate) {
		$tableHeaders[] = $candidate['value'];
	}
	//	debug($candidate_list);
	// テーブル本体作成

	$tableCells = array();
//	debug($group);
	//$groupMember = json_decode($group['Group']['member_list'], true);

	foreach ($group['Profile'] as $profile) {
		//debug($profile);
		$tmp = array();
		// ユーザ名
		$tmp[] = $profile['screen_name'];
		// 
		//$registrations;
		//debug($registrationsData);

		// ユーザの情報を確認する
		foreach ($candidate_list['data'] as $candidate) {
//			debug($candidate);

			if (isset($registrationsData[$profile['id']][$candidate['id']])) {
				$tmp[] = $this->Select->replaceSelectAnswer($registrationsData[$profile['id']][$candidate['id']]);
			} else {
				$tmp[] = $this->Select->replaceSelectAnswer(0);

			}
			
		}
		$tmp[] = $this->Html->link("回答を編集する",
				array(
						'controller' => 'events',
						'action' => 'register',
						"profileId" => $profile['id'],
						"eventId" => $event["Event"]['id']
				));
// 		// ユーザのデータを取得
// 		foreach ($registrations as $EventUser) {
// 			$hoge = array();
// 			if ($EventUser['EventUser']['user_id'] == $key) {
// 				$hoge = json_decode($EventUser['EventUser']['value'], true);
// 			}

// 			// 順番確認
// 			foreach ($candidate_list['data'] as $candidate) {
// 				if (isset($hoge[$candidate['id']])) {
// 					$tmp[] = $this->Select->replaceSelectAnswer($hoge[$candidate['id']]);
// 				} else {
// 					$tmp[] = $this->Select->replaceSelectAnswer(0);

// 				}

// 			}
// 			// 本人確認

			

// 		}

		$tableCells[] = $tmp;
	}

?>

	<table class="table">
	<thead>
	<?php
	echo $this->Html->tableHeaders($tableHeaders);
	?>
	</thead>
	<tbody>
	<?php
	echo $this->Html->tableCells($tableCells);
	// 	echo $this->Html->tableCells(
	// 		$this->Table->getTableCellsData($tableData, $event["Event"]['id'], $user["User"]["uri"]));
	?>
	</tbody>
	</table>
<?php
}
?>

<?php

if ($event["Event"]['owner_id'] == $user["User"]["id"]) {
	echo $this->Html->link("イベントの情報を編集する",
		array(
			'controller' => 'events',
			'action' => 'edit',
			"eventId" => $event["Event"]['id']
		), array(
			'class' => 'btn'
		));
} else {
	echo "<span class='btn'>イベントの情報を編集する（イベント作成者のみが編集可能です）</span>";
}

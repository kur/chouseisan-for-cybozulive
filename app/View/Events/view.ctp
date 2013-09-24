<h1><?php echo $eventName; ?></h1>
<div><?php echo $eventDescription; ?></div>

<table>
<?php
echo $this->Html->tableHeaders($tableHeaders);
$tableCells = array();
foreach ($tableData as $key => &$user) {
	foreach ($user as $key2 => &$cell) {
		if ($key2 == 0) {
			continue;
		}
		$cell = $this->Select->replaceSelectAnswer($cell);
	}
	// 自分自身の回答だけを編集できる
	if ($key == $userId) {
		$user[] = $this->Html->link(
					"回答を編集する",
					array('controller' => 'registrations', 'action' => 'view',
							"userId" => $key,
							"eventId" => $eventId)
			);
	} else {
		$user[] = "";
	}
	$tableCells[] = $user;
}
echo $this->Html->tableCells($tableCells);

?>
</table>


<br>
<?php 
if ( $eventOwnerId == $User["User"]["user_uri"]) {
	echo $this->Html->link(
		"候補を編集する",
		array('controller' => 'candidates', 'action' => 'view', "eventId" => $eventId));
} else {
	echo "候補を編集する（イベント作成者のみが編集可能です）";
}
?>
<br>
<?php 
echo $this->Html->link(
				"back",
				array('controller' => 'events', 'action' => 'index')
		);

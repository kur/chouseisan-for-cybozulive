<?php 
echo $this->Menu->getMenu(-1, $isLogin);
?>
<h3><?php echo $event["Event"]["name"] . " - " . $group["Group"]["name"]; ?></h3>
<div><?php echo $event["Event"]["description"]; ?></div>

<?php 
if (isset($tableHeaders) && isset($tableData)) {
	?>
	<table class="table">
	<thead>
	<?php
	echo $this->Html->tableHeaders($tableHeaders);
	?>
	</thead>
	<tbody>
	<?php
	echo $this->Html->tableCells(
	$this->Table->getTableCellsData($tableData, $event["Event"]['id'], $user["User"]["uri"])
	);
	?>
	</tbody>
	</table>
	<?php 
} else {
	?>
	<br>
	<div class="alert alert-info">
	候補が登録されていません。候補を登録してください。
	</div>
	<?php
}
	?>
<br>
<?php
if ( $event["Event"]['owner_id'] == $user["User"]["uri"]) {
	echo $this->Html->link(
		"イベントの情報を編集する",
		array('controller' => 'events', 'action' => 'edit', "eventId" => $event["Event"]['id']),
		array('class' => 'btn'));
} else {
	echo "イベントの情報を編集する（イベント作成者のみが編集可能です）";
}

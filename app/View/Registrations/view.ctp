<?php
// 前処理
foreach ($tableData as $key => &$value) {
	foreach ($value as $key2 => &$cell) {
		if ($key2 == 0) {
			continue;
		}
		$cell = $this->Select->replaceSelectbox($key, $cell);
	}
	$tableCells[] = $value;
}
?>
<h1><?php echo $event[0]["Event"]["name"]; ?></h1>
<div><?php echo $event[0]["Event"]["description"]; ?></div>
<?php
echo $this->Form->create(false, array('type' => 'post', 'action' => 'view'));
echo $this->Form->hidden('event_id', array('value' => $eventId));
echo $this->Form->hidden('user_id', array('value' => $userId));
?>
<table>
<?php
echo $this->Html->tableHeaders($tableHeaders);
echo $this->Html->tableCells($tableCells);
?>
</table>
<?php echo $this->Form->end('Change') ?>

<?php 
echo $this->Html->link(
				"back",
				array('controller' => 'events', 'action' => 'view',
						"eventId" => $eventId)
		);

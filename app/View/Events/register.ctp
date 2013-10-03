<?php 
echo $this->Menu->getMenu(-1, $isLogin);
?>
<h3><?php echo "$eventName - $groupName"; ?></h3>
<div><?php echo $eventDescription; ?></div>
<?php
// 前処理

?>
<?php
echo $this->Form->create(false, array('type' => 'post', 'action' => 'register'));
echo $this->Form->hidden('event_id', array('value' => $eventId));
echo $this->Form->hidden('user_id', array('value' => $userId));
?>
<table class="table">
<thead>
<?php
echo $this->Html->tableHeaders($tableHeaders);
?>
</thead>
<tbody>
<?php
$tableCells = $this->Table->getEventRegisterTableCellsData($tableData);
echo $this->Html->tableCells($tableCells);
?>
</tbody>
</table>
<?php echo $this->Form->end('登録する') ?>

<?php 
echo $this->Html->link(
				"back",
				array('controller' => 'events', 'action' => 'view',
						"eventId" => $eventId)
		);

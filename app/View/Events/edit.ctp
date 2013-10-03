<?php 
echo $this->Menu->getMenu(-1, $isLogin);
?>
<h3><?php echo $event["Event"]['name'] . " - " . $group['name']; ?></h3>
<?php 
	echo $this->Form->create(false, array('type' => 'post', 'action' => 'edit'));
	echo $this->Form->hidden('Event.id', array('value' => $event["Event"]["id"]));
	echo $this->Form->hidden('type', array('value' => "description"));
	echo $this->Form->textarea('Event.description', array('value' => $event["Event"]['description']));
echo $this->Form->end('説明文を変更する');
?>
<?php 
if ($tableData == null) {
	?>
	<br><div class="alert alert-info">候補を登録してください。</div>
	<?php 
} else {
	?>
	<table class="table">
	<thead>
	<?php
	echo $this->Html->tableHeaders($tableHeaders);
	?>
	</thead>
	<tbody>
	<?php 
	$tableCells = array();
	foreach ($tableData as $key => &$candidate) {
		$candidate[1] = $this->Html->link(
			"候補を削除する",
			array('controller' => 'events', 'action' => 'removeCandidate', "eventId" => $event["Event"]["id"], "candidateId" => $key));
		$tableCells[] = $candidate;
		
	}
	echo $this->Html->tableCells($tableCells);
	?>
	</tbody>
	</table>

	<?php 
}
	echo $this->Form->create(false, array('type' => 'post', 'action' => 'edit'));
	echo $this->Form->hidden('event_id', array('value' => $event["Event"]["id"]));
	echo $this->Form->hidden('type', array('value' => "candidate"));
	echo $this->Form->input("name", array('label' => false));
	echo $this->Form->end('候補を追加する');
	echo $this->Html->link(
			"戻る",
			array('controller' => 'events', 'action' => 'view', "eventId" => $event["Event"]["id"]));
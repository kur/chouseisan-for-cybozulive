<?php
echo $this->Menu->getMenu(-1, $isLogin);
$candidates = json_decode($event['Event']['candidate_list'], true);
$tableHeaders = array(
	"候補",
	"操作"
);
?>
<h3><?php echo $event["Event"]['name'] . " - " . $group['name']; ?></h3>
<?php
echo $this->Form->create(false, array(
	'type' => 'post',
	'action' => 'edit'
));
echo $this->Form->hidden('Event.id', array(
	'value' => $event["Event"]["id"]
));
echo $this->Form->hidden('type', array(
	'value' => "description"
));
echo $this->Form->textarea('Event.description', array(
	'value' => $event["Event"]['description']
));
echo $this->Form->end('説明文を変更する');

$tableCells = array();

if (isset($candidates['data'])) {
	foreach ($candidates['data'] as $key => &$candidate) {
		if ($candidate['flag'] == 1) {
			$tmp = array();
			$tmp[] = $candidate['value'];
			$tmp[] = $this->Html->link("候補を削除する",
				array(
					'controller' => 'events',
					'action' => 'removeCandidate',
					"eventId" => $event["Event"]["id"],
					"candidateId" => $candidate['id']
				));
			$tableCells[] = $tmp;
		}
	}
}

if (!isset($tableCells) || empty($tableCells)) {
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
	echo $this->Html->tableCells($tableCells);
	?>
	</tbody>
	</table>

	<?php
}
echo $this->Form->create(false, array(
	'type' => 'post',
	'action' => 'edit'
));
echo $this->Form->hidden('eventId', array(
	'value' => $event["Event"]["id"]
));
echo $this->Form->hidden('type', array(
	'value' => "candidate"
));
echo $this->Form->input("name", array(
	'label' => false
));
echo $this->Form->end('候補を追加する');
echo $this->Html->link("戻る",
	array(
		'controller' => 'events',
		'action' => 'view',
		"eventId" => $event["Event"]["id"]
	));

<?php
echo $this->Menu->getMenu(-1, $isLogin);
?>
<h3><?php echo $event["Event"]["name"] . " - " . $group["Group"]["name"]; ?></h3>
<div><?php echo $event["Event"]["description"]; ?></div>
<?php
// debug($event);
// debug($group);
// debug($user);
// debug($registrations);
$tableHeaders = array(
	"候補",
	"回答"
);
?>
<?php
echo $this->Form->create(false, array(
	'type' => 'post',
	'action' => 'register'
));
echo $this->Form->hidden('event_id', array(
	'value' => $event['Event']['id']
));
echo $this->Form->hidden('profile_id', array(
	'value' => $profileId
));
$candidate_list = json_decode($event['Event']['candidate_list'], true);
// 整形
foreach ($registrations as $r) {
	$registrationsData[$r['EventProfile']['profile_id']] = json_decode($r['EventProfile']['value'], true);
}

$tableCells = array();
foreach ($candidate_list['data'] as $candidate) {
	$tmp = array();
	$tmp[] = $candidate['value'];
	if (isset($registrationsData[$profileId][$candidate['id']])) {
		$tmp[] = $this->Select->replaceSelectbox($candidate['id'], $registrationsData[$profileId][$candidate['id']]);
		//$tmp[] = $registrations[$candidate['id']];

	} else {
		$tmp[] = $this->Select->replaceSelectbox($candidate['id'], 0);
		;
	}

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
?>
</tbody>
</table>
<?php echo $this->Form->end('登録する') ?>

<?php
echo $this->Html->link("back", array(
	'controller' => 'events',
	'action' => 'view',
	"eventId" => $eventId
));

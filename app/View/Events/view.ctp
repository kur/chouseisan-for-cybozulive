<h1><?php echo $eventName; ?></h1>
<div><?php echo $eventDescription; ?></div>

<table>
<?php
echo $this->Html->tableHeaders($tableHeaders);
$tableCells = array();
foreach ($tableData as $key => &$user) {
	foreach ($user as $key2 => &$cell) {
		//var_dump($cell);
		if ($key2==0) {
			continue;
		}
		$cell = $this->Select->replaceSelectAnswer($cell);
	}
	$user[] = $this->Html->link(
				"Edit",
				array('controller' => 'registrations', 'action' => 'view',
						"userId" => $key,
						"eventId" => $eventId)
		);
	$tableCells[] = $user;
}
echo $this->Html->tableCells($tableCells);
 
?>
</table>


<br>
<?php 
echo $this->Html->link(
		"edit",
		array('controller' => 'candidates', 'action' => 'view',"eventId" => $eventId)
);
?>
<br>
<?php 
echo $this->Html->link(
				"back",
				array('controller' => 'events', 'action' => 'index')
		);
?>


<table>
<?php
echo $this->Html->tableHeaders($tableHeaders);
echo $this->Html->tableCells($tableCells);
?>
</table>
<?php 
echo $this->Html->link(
		"Add Candidate",
		array('controller' => 'candidates', 'action' => 'add',"eventId" => $event[0]["Event"]["id"])
);
?>
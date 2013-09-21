<?php
foreach ($events as $event){
	echo "[" . $event["Event"]["id"] .  "]";
	echo $this->Html->link(
			$event["Event"]["name"],
			array('controller' => 'events', 'action' => 'view',"eventId" => $event["Event"]["id"])
	);
	echo "[" . $event["Group"]["name"] .  "]";
	echo "<br>";
}


echo $this->Html->link(
		"新しいイベントを作成する",
		array('controller' => 'events', 'action' => 'create')
);
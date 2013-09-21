<?php
foreach ($eventList as $groupId => $group) {
	echo "<h1>";
	echo $groupList[$groupId];
	echo "</h1>";
	foreach ($group as $eventId => $events) {
		echo "[" . $eventId . "]";
		echo $this->Html->link(
				$events,
				array('controller' => 'events', 'action' => 'view', "eventId" => $eventId)
		);
		echo "<br>";
	}
}

echo $this->Html->link(
		"新しいイベントを作成する",
		array('controller' => 'events', 'action' => 'create')
);
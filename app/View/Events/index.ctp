<?php
	echo $this->Menu->getMenu(4, $isLogin);
	echo $this->Html->div('row',
			$this->Html->div('span3 offset6',
					$this->Html->link(
							$this->Form->button("新しいイベントを作成する",
									array('class' => 'btn btn-primary')
							),
							array('controller' => 'events', 'action' => 'create'),
							array('escape' => false)
					)
			)
			);
	$tableHeaders = array("イベント名", "説明" ,"作成日");
?>

<?php 
foreach ($groupList as $groupId => $group) {
	echo "<h3>$group</h3>";
	if (isset($eventList[$groupId])) {
		$tableCells = array();
		foreach ($eventList[$groupId] as $event) {
			$tmp = array();
			$tmp[] = $this->Html->link($event["name"],
					array('controller' => 'events', 'action' => 'view', "eventId" => $event["id"])
			);
			$tmp[] = $event["description"];
			$tmp[] = $event["created"];
			$tableCells[] = $tmp;
		}
		?>
		<table class="table"">
		<thead>
		<?php echo $this->Html->tableHeaders($tableHeaders); ?>
		</thead>
		<tbody>
		<?php
			echo $this->Html->tableCells($tableCells);
		?>
		</tbody>
		</table>
	<?php
	} else {
		echo "登録されてるイベントがありません";
	}
}




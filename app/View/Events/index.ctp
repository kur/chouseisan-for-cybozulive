<?php
//	上部メニュー出力
echo $this->Menu->getMenu(4, $isLogin);

// 新しいイベント作成ボタン出力
echo $this->Html->div('row',
	$this->Html->div('span3 offset6',
		$this->Html->link($this->Form->button("新しいイベントを作成する", array(
				'class' => 'btn btn-primary'
			)), array(
				'controller' => 'events',
				'action' => 'create'
			), array(
				'escape' => false
			))));

// テーブルヘッダー設定
$tableHeaders = array(
	"イベント名",
	"説明",
	"作成日"
);
foreach ($groupList as $groupId => $group) {
?>	
	<h3><?php echo h($group); ?></h3>
	<?php
	if (isset($eventList[$groupId])) {
		$tableCells = array();
		foreach ($eventList[$groupId] as $event) {
			$tmp = array();
			$tmp[] = $this->Html->link(h($event["name"]),
				array(
					'controller' => 'events',
					'action' => 'view',
					"eventId" => $event["id"]
				));
			$tmp[] = h($event["description"]);
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

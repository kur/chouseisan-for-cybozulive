<?php
echo $this->Form->create(false, array('type' => 'post', 'action' => 'create'));

echo $this->Form->input("Event.group_id",
		array('options' => $groupList, 'empty' => true)
	);
echo $this->Form->input("Event.name");
echo $this->Form->input("Event.description");
?>
<?php echo $this->Form->end('Create') ?>
<?php 
echo $this->Html->link(
		"イベント一覧",
		array('controller' => 'events', 'action' => 'viewall')
);

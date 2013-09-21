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
		"グループ管理",
		array('controller' => 'groups', 'action' => 'add')
);

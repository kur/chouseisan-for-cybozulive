<?php




echo $this->Menu->getMenu(4, $isLogin);
echo $this->Form->create(false, array(
	'type' => 'post',
	'action' => 'create'
));

echo $this->Form->input("Event.group_id", array(
	'label' => "グループ",
	'options' => $groupList,
	'empty' => true
));
echo $this->Form->input("Event.name", array(
	'label' => "イベント名"
));
echo $this->Form->input("Event.description", array(
	'label' => "イベントの説明"
));
?>
<?php echo $this->Form->end('Create');

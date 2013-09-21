<?php
echo $this->Form->create(false, array('type' => 'post', 'action' => 'add'));
//echo $this->Form->hidden('user_id',array('value' => $userId));
echo $this->Form->input("Group.name");
echo $this->Form->end('Create');
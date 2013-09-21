<?php
//var_dump($events);
echo $this->Form->create(false, array('type' => 'post', 'action' => 'add'));
echo $this->Form->hidden('Candidate.event_id',array('value' => $eventId));
//echo $this->Form->hidden('user_id',array('value' => $userId));


echo  $this->Form->input("Candidate.name");
?>
<?php echo $this->Form->end('Create') ?>
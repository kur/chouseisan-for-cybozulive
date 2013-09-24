サイトの説明を書く
サイボウズLiveIDでログインする

<br>
<?php

echo $this->Html->link(
				"Login",
				array('controller' => 'users', 'action' => 'login')
		);
?>
<br>
<?php 
echo $this->Html->link(
		"イベント一覧",
		array('controller' => 'events', 'action' => 'viewall')
);
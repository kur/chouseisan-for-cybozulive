ログインが必要です。ログインしますか？
<?php 
//var_dump($_SERVER);
echo $this->Html->link(
		"ログインする",
		array('controller' => 'users', 'action' => 'login', 'confirmed')
);

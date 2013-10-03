<?php
echo $this->Menu->getMenu(-1);
?>
<p>
調整さんforサイボウズLiveを利用するためには、サイボウズLiveのアカウントで認証を行う必要があります。
認証を行うには下記の「認証する」ボタンを押して。画面の指示に従ってください。
</p>
<p>
<?php 
echo $this->Html->link(
		"認証する",
		array('controller' => 'users', 'action' => 'login', 'confirmed'),
		array('class'=>'btn btn-large btn-success')
);
?>
</p>
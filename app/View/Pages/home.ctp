<?php
echo $this->Menu->getMenu(0, $isLogin);
?>
<div class="jumbotron">
<h1>調整さん</h1>
<h2>for サイボウズLive</h2>

	<?php
	if ($isLogin == false) {
	?>
	<p class="lead">
	サイボウズLiveをお使いであれば、<br />
	下記のボタンを押して認証するだけですぐに使う事が出来ます。</p>
	<?php
	echo $this->Html->link(
			"使いはじめる",
			array('controller' => 'users', 'action' => 'login'),
			array('class' => 'btn btn-large btn-success')
	);
	?>
	<?php
	} else {
	?>
	<p class="lead">
	下記のボタンを押すと、<br />
	あなたの所属しているグループに関するイベントの一覧が表示されます。</p>
	<?php
	echo $this->Html->link(
			"イベント一覧を見る",
			array('controller' => 'events', 'action' => 'index'),
			array('class' => 'btn btn-large btn-success')
	);
	 
	}
?>
</div>

<hr>

<div class="row-fluid marketing">
<div class="span6">
<h4>Subheading</h4>
<p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

<h4>Subheading</h4>
<p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

<h4>Subheading</h4>
<p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
</div>

<div class="span6">
<h4>Subheading</h4>
<p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

<h4>Subheading</h4>
<p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

<h4>Subheading</h4>
<p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
</div>
</div>

<hr>

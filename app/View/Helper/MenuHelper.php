<?php 
class MenuHelper extends AppHelper {

	public $helpers = array ('Form', 'Html');

	public function getMenu($index=-1, $isLogin=false) {
		$str = '<div class="masthead">';
		$str .= '<ul class="nav nav-pills pull-right">';
		if ($index == 0) {
			$str .= '<li class="active">' . $this->Html->link("Home", array('controller' => 'pages', 'action' => '')) . '</li>';
		} else {
			$str .= '<li>' . $this->Html->link("Home", array('controller' => 'pages', 'action' => '')) . '</li>';
		}
		if ($index == 1) {
			$str .= '<li class="active">' . $this->Html->link("このサイトについて", array('controller' => 'pages', 'action' => 'about')) . '</li>';
		} else {
			$str .= '<li >' . $this->Html->link("このサイトについて", array('controller' => 'pages', 'action' => 'about')) . '</li>';
		}
		if ($isLogin == true) {
			if ($index == 4) {
				$str .= '<li class="active">' . $this->Html->link("イベント一覧", array('controller' => 'events', 'action' => 'index')) . '</li>';
			} else {
				$str .= '<li >' . $this->Html->link("イベント一覧", array('controller' => 'events', 'action' => 'index')) . '</li>';
			}
				$str .= '<li >' . $this->Html->link("ログアウト", array('controller' => 'users', 'action' => 'logout')) . '</li>';
		}
		if ($index == 2) {
			$str .= '<li class="active">' . $this->Html->link("お問い合わせ", array('controller' => 'pages', 'action' => 'contact')) . '</li>';
		} else {
			$str .= '<li>' . $this->Html->link("お問い合わせ", array('controller' => 'pages', 'action' => 'contact')) . '</li>';
		}
		$str .= '</ul>';
		$str .= '<h3 class="muted">';
		$str .= $this->Html->link(
				"調整さん",
				array('controller' => '/', 'action' => ''));
		$str .= '</h3></div><hr>';

		return $str;
	}
}

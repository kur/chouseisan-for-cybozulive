<?php 
class User extends AppModel {
	public $name = 'User';
	var $belongsTo = 'Group';
	//public $hasOne = 'Group';
}
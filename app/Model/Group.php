<?php 
class Group extends AppModel {
	public $name = 'Group';
	public $hasMany = 'User';
	//public $hasOne = 'Group';
}
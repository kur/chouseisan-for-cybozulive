<?php 
class Event extends AppModel {

	public $name = 'Event';

	//var $belongsTo = 'Group';
	public $hasMany = 'Candidate';

	public $validate = array(
		'group_id' => array(
			'rule' => 'notEmpty'
		),
		'name' => array(
		'rule' => 'notEmpty'
		)
	);
}
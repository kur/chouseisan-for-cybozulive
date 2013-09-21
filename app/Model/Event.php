<?php 
class Event extends AppModel {
	var $name = 'Event';
	var $belongsTo = 'Group';
	var $hasMany = 'Candidate';
	
	public $validate = array(
        'group_id' => array(
            'rule' => 'notEmpty'
        ),
        'name' => array(
            'rule' => 'notEmpty'
        )
    );
}
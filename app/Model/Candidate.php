<?php 
class Candidate extends AppModel {
	public $name = 'Candidate';
	
	public $validate = array(
        'event_id' => array(
            'rule' => 'notEmpty'
        ),
        'name' => array(
            'rule' => 'notEmpty'
        )
    );
}
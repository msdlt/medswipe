<?php
class EventsUser extends AppModel {
	var $name = 'EventsUser';
	//var $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	var $validate = array(
		);

	var $belongsTo = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>
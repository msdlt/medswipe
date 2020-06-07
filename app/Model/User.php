<?php
class User extends AppModel {
	var $name = 'User';
	var $displayField = 'username';
	//var $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'CoursesUser' => array(
			'className' => 'CoursesUser',
			'foreignKey' => 'user_id',
			'dependent' => true,  //no point keeping these if user gone
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'EventsUser' => array(
			'className' => 'EventsUser',
			'foreignKey' => 'user_id',
			'dependent' => false,  //need to keep these - in fact never allow deletion of user if some records exist
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>
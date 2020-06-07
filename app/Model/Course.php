<?php
class Course extends AppModel {
	var $name = 'Course';
	var $displayField = 'name';
	//var $actsAs   = array('Transactional'); 
	
	var $validate = array(
			'name' => array(
				'rule' => array('minLength', '3'),
				'message' => 'Name must be at least 3 characters long'
				)
			);
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'course_id',
			'dependent' => true,  //want to delete events if course is deleted BUT only if no eventsusers for event
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CoursesUser' => array(
			'className' => 'CoursesUser',
			'foreignKey' => 'course_id',
			'dependent' => true, //always want these to be deleted
			'conditions' => '',
			'fields' => '',
			'order' => 'user_type',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>
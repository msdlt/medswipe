<?php
class Event extends AppModel {
	var $name = 'Event';
	var $displayField = 'name';
	var $validate = array(
			'name' => array(
				'rule' => array('minLength', '3'),
				'message' => 'Name must be at least 3 characters long'
				),
			//'duration' => array(
				//'rule' => 'numeric',
				//'message' => 'You must specify a duration'
				//)
			'end_date' => array(
				'comparison' => array(
					'rule'=>array('field_comparison', '>', 'date'), 
					'message' => 'Your End Date must be later than your Start date'
					)
				)
			);
	
	//see https://groups.google.com/forum/?fromgroups#!topic/cake-php/C-TetJiPT3U
	function field_comparison($check1, $operator, $field2) {
		foreach($check1 as $key=>$value1) {
			$value2 = $this->data[$this->alias][$field2];
			if (!Validation::comparison($value1, $operator, $value2))
				return false;
		}
		return true;
	}
	
	var $belongsTo = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'EventsUser' => array(
			'className' => 'EventsUser',
			'foreignKey' => 'event_id',
			'dependent' => false,    //there won't be any of these if event is allowed to be deleted
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
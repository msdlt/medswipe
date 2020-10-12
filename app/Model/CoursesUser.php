<?php
class CoursesUser extends AppModel {
	var $name = 'CoursesUser';
	//ensure only one entry exists for each:
	var $validate = array(
			'course_id' => array('rule' => 'uniqueCombi','message'  => 'This user is already a member of this course'),
			'user_id'  => array('rule' => 'uniqueCombi','message'  => 'This user is already a member of this course')
	);
	
	function uniqueCombi() {
		$combi = array(
				"{$this->alias}.course_id" => $this->data[$this->alias]['course_id'],
						"{$this->alias}.user_id"  => $this->data[$this->alias]['user_id']
		);
		debug($this->data[$this->alias]['user_id']." "."{$this->alias}.user_id");
		return $this->isUnique($combi, false);
	}

	var $belongsTo = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_id',
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
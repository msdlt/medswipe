<?php
/* Course Test cases generated on: 2011-09-26 17:09:18 : 1317049938*/
App::import('Model', 'Course');

class CourseTest extends CakeTestCase {
	var $fixtures = array('app.course', 'app.event', 'app.courses_user');

	function startTest() {
		$this->Course =& ClassRegistry::init('Course');
	}

	function endTest() {
		unset($this->Course);
		ClassRegistry::flush();
	}

}
?>
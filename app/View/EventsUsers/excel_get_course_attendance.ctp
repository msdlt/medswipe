<?php
//debug($course_events);
//debug($course_users);
//echo $this->element('sql_dump');

$this->PhpExcel->createWorksheet(); 
$this->PhpExcel->setDefaultFont('Calibri', 12); 


$course_event_ids = array();

// define table cells 
$table = array( 
    array('label' => __('Last name'), 'width' => 'auto', 'filter' => true), 
    array('label' => __('First name'), 'width' => 'auto'), 
    array('label' => __('No of Events'), 'width' => 'auto', 'filter' => true), 
   );
foreach($course_events as $course_event){
	array_push($table, array('label' => __($course_event['Event']['name']), 'width' => 'auto', 'filter' => true));
	array_push($course_event_ids,'event_'.$course_event['Event']['id']);
	}
////debug($table);	

// heading 
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true)); 

// data 
foreach ($course_users['rows'] as $course_user) { 
	//debug($course_user);
	$row_array = array($course_user['last_name'],$course_user['first_name'],$course_user['no_of_events']);
	for ($i=0;$i<count($course_event_ids);$i++){
		if(isset($course_user[$course_event_ids[$i]])){
			array_push($row_array,$course_user[$course_event_ids[$i]]);
			}
		else{
			array_push($row_array,'n/a');
			}
		}
    $this->PhpExcel->addTableRow($row_array); 
}

$this->PhpExcel->addTableFooter();

//debug($this->PhpExcel)
$this->PhpExcel->output(); 
?>

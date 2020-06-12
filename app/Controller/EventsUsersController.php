<?php
//http://www.php.net/manual/en/function.array-search.php#106107
function multidimensional_search($parents, $searched) 
		{ 
	  	if (empty($searched) || empty($parents)) 
	  		{ 
	    	return false; 
	  		} 
		foreach ($parents as $key => $value) 
			{ 
	    	$exists = true; 
	    	foreach ($searched as $skey => $svalue) 
	    		{ 
	      		$exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
	    		} 
	    	if($exists){ return $key; } 
	  		} 
	  	return false; 
		} 

//App::import('Vendor', 'Worksheet', array('file' => 'PhpSpreadsheet'.DS.'Worksheet'.DS.'Worksheet.php'));

//App::import('Vendor', 'Spreadsheet', array('file' => 'PhpOffice'.DS.'PhpSpreadsheet'.DS.'Spreadsheet'.DS.'Spreadsheet.php'));

use PhpOffice\PhpSpreadsheet\Spreadsheet;
		
class EventsUsersController extends AppController {

	var $name = 'EventsUsers';
	public $components = array('RequestHandler');
	public $helpers = array('PhpExcel');

	function index() {
		$this->EventsUser->recursive = 0;
		$this->set('eventsUsers', $this->paginate());
	}
	
/*	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid events user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('eventsUser', $this->EventsUser->read(null, $id));
	}*/
	
	function view_attendees($event_id = null) {
		if (!$event_id) {
			$this->Session->setFlash(__('Invalid event', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('event', $this->EventsUser->Event->read(null, $event_id));
		$this->set('navigation', 'courses');
	}
	
	function view_course_attendees($course_id = null) {
		if (!$course_id) {
			$this->Session->setFlash(__('Invalid course', true));
			$this->redirect(array('action' => 'index'));
		}
		if($this->EventsUser->Event->Course->CoursesUser->find('count', array(
					'conditions'=>array(
						'CoursesUser.course_id'=>$course_id,
						'CoursesUser.user_type'=>'attendee'
						)
					)
				)>0
			)
			{
			$this->set('course_has_members', true);
			}
		else
			{
			$this->set('course_has_members', false);
			}
		$this->set('course', $this->EventsUser->Event->Course->read(null, $course_id));
		$this->set('navigation', 'courses');
	}
	
	function ajax_count_distinct_attendees()
		{
		if(isset($this->request['named']['event_id']))
			{
			$event_id = $this->request['named']['event_id'];
			if($this->RequestHandler->isAjax()) 
				{
				//people who have swiped
				$distinct_attendees = $this->EventsUser->find('count', array(
						'fields'=>'DISTINCT EventsUser.user_id',
						'conditions'=>array(
							'EventsUser.event_id'=>$event_id				
							)
						)
					);
				$this->disableCache(); //prevents IE caching results
				$this->set('distinct_attendees', $distinct_attendees);
				$this->render('ajax_count_distinct_attendees', 'ajax');//which ctp, which layout
				}
			}
		}
		
	function ajax_count_distinct_participants()
		{
		if(isset($this->request['named']['event_id'])&&isset($this->request['named']['course_id']))
			{
			$course_id = $this->request['named']['course_id'];
			if($this->RequestHandler->isAjax()) {
				//people expected
				$users_results = $this->EventsUser->User->CoursesUser->find('count', array(
				'conditions'=>array(
					'CoursesUser.course_id'=>$course_id,
					'CoursesUser.user_type'=>'attendee'
					)
				));
	  			}
	  		$this->disableCache(); //prevents IE caching results
	  		$this->set('distinct_participants', $users_results);
			$this->render('ajax_count_distinct_participants', 'ajax');//which ctp, which layout
			}
		}
		
	function ajax_count_distinct_attendees_who_are_expected()
		{
		if(isset($this->request['named']['event_id'])&&isset($this->request['named']['course_id']))
			{
			$event_id = $this->request['named']['event_id'];
			$course_id = $this->request['named']['course_id'];
			if($this->RequestHandler->isAjax()) 
				{
				//people who have swiped
				$this->EventsUser->User->recursive=2;
				$distinct_attendees_who_are_expected = $this->EventsUser->User->find('count', array(
						'fields'=>'DISTINCT EventsUser.user_id',
						'conditions'=>array(
							'EventsUser.event_id'=>$event_id,
							'CoursesUser.course_id'=>$course_id,
							'CoursesUser.user_type'=>'attendee'
							),
						'joins' => array(
							array(
								'table' => 'courses_users',
								'type' => 'LEFT',
								'alias' => 'CoursesUser',
								'conditions' => array(
										'User.id = CoursesUser.user_id'
										)
								),
							array(
								'table' => 'events_users',
								'type' => 'LEFT',
								'alias' => 'EventsUser',
								'conditions' => array(
										'User.id = EventsUser.user_id'
									)
								)
							)
						)
					);
				$this->disableCache(); //prevents IE caching results
				$this->set('distinct_attendees_who_are_expected', $distinct_attendees_who_are_expected);
				$this->render('ajax_count_distinct_attendees_who_are_expected', 'ajax');//which ctp, which layout
				}
			}
		}
				
	function ajax_get_events_attendance()
		{
		//debug($this->request);
		if(isset($this->request['named']['event_id']))
			{
			$event_id = $this->request['named']['event_id'];
			if(isset($this->request['url']['sidx']))
				{
				$sort_index = $this->request['url']['sidx'];
				if(isset($this->request['url']['sord']))
					{
					$asc_desc = strtoupper($this->request['url']['sord']);
					}
				else
					{
					$asc_desc = "ASC";	
					}
				if($sort_index=="last_name"||$sort_index=="first_name"){
					$order = array('User.'.$sort_index." ".$asc_desc); //can only sort on first and last name
					}
				else
					{
					$order = array('EventsUser.'.$sort_index." ".$asc_desc);
					}
				}
			else
				{
				$order = array('User.last_name'." ".$asc_desc);
				}
			//Configure::write('debug', 0);
	  		if($this->RequestHandler->isAjax()) {
				$events_users_results = $this->EventsUser->find('all', array(
				'fields'=>array('EventsUser.user_id', 'EventsUser.registration_type', 'EventsUser.attended_at', 'EventsUser.ip_address', 'User.last_name', 'User.first_name'),
				'conditions'=>array(
					'EventsUser.event_id'=>$event_id
					),
				'order' => $order
				));
	  			}
	  		$events_users["rows"]=array();
	  		foreach($events_users_results as $events_users_result)
	  			{
	  			$date = new DateTime($events_users_result['EventsUser']['attended_at']);
				$event_user = array(
	  				'user_id'=>$events_users_result['EventsUser']['user_id'],
	  				'last_name'=>$events_users_result['User']['last_name'],
	  				'first_name'=>$events_users_result['User']['first_name'],
	  				'attended_at'=>$date->format('d/m/Y H:i'), //$events_users_result['EventsUser']['attended_at'],
	  				'ip_address'=>$events_users_result['EventsUser']['ip_address'],
	  				'registration_type'=>$events_users_result['EventsUser']['registration_type']
	  				);
	  			$events_users["rows"][]=$event_user;
	  			}
	  		$this->set('events_users', $events_users);
			$this->render('ajax_get_events_attendance', 'ajax');//which ctp, which layout
			}
		}
	function ajax_get_events_participants()
		{
		//debug($this->request);
		if(isset($this->request['named']['event_id'])&&isset($this->request['named']['course_id']))
			{
			$event_id = $this->request['named']['event_id'];
			$course_id = $this->request['named']['course_id'];
			if(isset($this->request['url']['sidx']))
				{
				$sort_index = $this->request['url']['sidx'];
				if(isset($this->request['url']['sord']))
					{
					$asc_desc = strtoupper($this->request['url']['sord']);
					}
				else
					{
					$asc_desc = "ASC";	
					}
				if($sort_index=="last_name"||$sort_index=="first_name"){
					$order = array('User.'.$sort_index." ".$asc_desc); //can only sort on first and last name
					}
				else
					{
					$order = array('EventsUser.'.$sort_index." ".$asc_desc);
					}
				}
			else
				{
				$order = array('User.last_name'." ".$asc_desc);
				}
			//Configure::write('debug', 0);
	  		if($this->RequestHandler->isAjax()) {
				$users_results = $this->EventsUser->User->find('all', array(
				'fields'=>array('User.id','User.last_name', 'User.first_name'),
				'conditions'=>array(
					'CoursesUser.course_id'=>$course_id,
					'CoursesUser.user_type'=>'attendee'
					),
				'joins' => array(
					array(
	 					'table' => 'courses_users',
					 	'type' => 'INNER',
						'alias' => 'CoursesUser', 
						'conditions' => array(
	 						'CoursesUser.user_id = User.id'
	 						)
	 					)
	 				), 
				'order' => $order
				));
	  			}
	  		$users["rows"]=array();
	  		//debug($users_results);
			foreach($users_results as $users_result)
	  			{
	  			//first make sure this user hasn't attended this event
				$attended='false';
				foreach($users_result['EventsUser'] as $events_user)
					{
					if($events_user['event_id']==$event_id) $attended='true';
					}
				if($attended=='false')
					{
					$event_user = array(
						'id'=>$users_result['User']['id'],
						'last_name'=>$users_result['User']['last_name'],
						'first_name'=>$users_result['User']['first_name']
						);
					$users["rows"][]=$event_user;
					}
	  			}
	  		$this->set('users', $users);
			$this->render('ajax_get_events_participants', 'ajax');//which ctp, which layout
			}
		}
		
	function ajax_get_course_attendance()
		{
		//debug($this->request);
		if(isset($this->request['named']['course_id']))
			{
			$course_id = $this->request['named']['course_id'];
			if(isset($this->request['named']['type'])&&$this->request['named']['type']=='expected'){
				$expected=true;
				}
			else{ 
				$expected=false;
				}
			//debug($course_id);
			$asc_desc = "ASC";	//JHM modified 2012-06-11
			if(isset($this->request['url']['sidx']))
				{
				$sort_index = $this->request['url']['sidx'];
				if(isset($this->request['url']['sord']))
					{
					$asc_desc = strtoupper($this->request['url']['sord']);
					}
				else
					{
					$asc_desc = "ASC";	
					}
				if($this->request['url']['sidx']!="no_of_events"){
					$order = array('User.'.$sort_index." ".$asc_desc); //can only sort on first and last name
					}
				else
					{
					$order = array('User.last_name'." ".$asc_desc);
					}
				}
			else
				{
				$order = array('User.last_name'." ".$asc_desc);
				}
			if($expected){
				$this->EventsUser->recursive = -1;
				
				$expected_users = $this->EventsUser->User->CoursesUser->find('all', array(
						'fields'=>array('User.id', 'User.last_name', 'User.first_name'), 
						'order' => $order,
						'conditions' => array(
							'CoursesUser.user_type'=>'attendee',
							'CoursesUser.course_id'=>$course_id,
							)
						)
					);
				
				$this->EventsUser->User->CoursesUser->recursive = -1;
				$course_users_results = $this->EventsUser->User->CoursesUser->find('all', array(
						'fields'=>array('User.id', 'User.last_name', 'User.first_name','EventsUser.event_id','EventsUser.id','EventsUser.registration_type', 'EventsUser.attended_at', 'Event.id', 'Event.name'), //'EventsUser.event_id', 'EventsUser.attended_at', , 'Event.id', 'Event.name'
						'order' => $order,
						'conditions' => array(
								//'Event.course_id'=>$course_id,
								'CoursesUser.user_type'=>'attendee',
								'CoursesUser.course_id'=>$course_id,
								'Event.course_id'=>$course_id
						),
						'joins'	=> array(
								array(
										'table' => 'users',
										'alias' => 'User',
										'type' => 'LEFT OUTER',
										'conditions' => array(
												'User.id = CoursesUser.user_id',
										)
								),
								array(
										'table' => 'events_users',
										'alias' => 'EventsUser',
										'type' => 'LEFT OUTER',
										'conditions' => array(
												'User.id = EventsUser.user_id'
										)
								),
								array(
										'table' => 'events',
										'alias' => 'Event',
										'type' => 'LEFT OUTER',
										'conditions' => array(
												'Event.id = EventsUser.event_id'
										)
								),
								
								
						)
					));
				}
			else{
				$course_users_results = $this->EventsUser->find('all', array(
					'fields'=>array('User.id', 'User.last_name', 'User.first_name','EventsUser.event_id','EventsUser.id','EventsUser.registration_type', 'EventsUser.attended_at', 'Event.id', 'Event.name'), //'EventsUser.event_id', 'EventsUser.attended_at', , 'Event.id', 'Event.name'
					'conditions'=>array(
						'Event.course_id'=>$course_id
						),
					'order' => $order
					));
				}
			if($expected){
	  			//pre-populate with expected users
	  			foreach($expected_users as $expected_user){
	  				$course_user = array(
  						'user_id'=>$expected_user['User']['id'],
  						'last_name'=>$expected_user['User']['last_name'],
  						'first_name'=>$expected_user['User']['first_name'],
  						'no_of_events'=>0,
  						);
	  				$course_users["rows"][]=$course_user;
	  				}
	  			}
	  		else{
	  			$course_users["rows"]=array();
	  			}
	  		foreach($course_users_results as $course_users_result)
	  			{
	  			//if($expected && count($course_users_result['User']['CoursesUser'])==0) continue; //ie skip any non attendees
	  			//let's see if this user already exists
	  			$found_user = multidimensional_search($course_users["rows"], array('user_id'=>$course_users_result['User']['id']));
	  			$date = new DateTime($course_users_result['EventsUser']['attended_at']);
				if($found_user!==false)
	  				{
	  				//check whether user is already registered on this event
	  				if(isset($course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']]))
	  					{
	  					//already have a key for this event, update date if necessary
	  					if($course_users_result['EventsUser']['attended_at']>$course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']])
	  						{
	  						$course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']]=$date->format('d/m/Y')." (".$course_users_result['EventsUser']['registration_type'].")";
							//$course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']]="Y";
							}
	  					}
	  				else
	  					{
	  					//let''s add this event
	  					$course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']] = $date->format('d/m/Y')." (".$course_users_result['EventsUser']['registration_type'].")";
	  					//$course_users["rows"][$found_user]['event_'.$course_users_result['Event']['id']] = "Y";
	  					$course_users["rows"][$found_user]['no_of_events']++;
	  					}
	  				}
	  			else
	  				{
	  				$course_user = array(
	  					'user_id'=>$course_users_result['User']['id'],
	  					'last_name'=>$course_users_result['User']['last_name'],
	  					'first_name'=>$course_users_result['User']['first_name'],
	  					'no_of_events'=>1,
	  					'event_'.$course_users_result['Event']['id']=>$date->format('d/m/Y')." (".$course_users_result['EventsUser']['registration_type'].")"
						//'event_'.$course_users_result['Event']['id']=>"Y"
	  					);	
	  				$course_users["rows"][]=$course_user;
	  				}
	  			}
	  		if(isset($this->request['url']['sidx'])&&$this->request['url']['sidx']=="no_of_events")
	  			{
	  			$course_users_sorted["rows"] = Set::sort($course_users["rows"], '{n}.no_of_events', $this->request['url']['sord']);
	  			$this->set('course_users', $course_users_sorted);
	  			}
	  		else 
	  			{
	  			$this->set('course_users', $course_users);
	  			}
	  		if(isset($this->request['named']['download'])&&$this->request['named']['download']==true)
	  			{
	  			//download to excel
	  			//also need a list of course events
	  			$this->EventsUser->Event->recursive = 0;
	  			$course_events = $this->EventsUser->Event->find('all',array(
	  						'conditions'=>array(
	  							'Event.course_id'=>$course_id
	  							),
	  						'order' => 'Event.date'
	  						)
  						);
	  			
				$this->set('course_events', $course_events);
				//instead of rendering a .ctp file, let's just write out directly
				$spreadsheet = new Spreadsheet();
				

				//http://abakalidis.blogspot.com/2018/08/how-to-use-latest-phpofficespreadsheet.html

	  			//$this->render('excel_get_course_attendance', 'ajax');//which ctp, which layout
	  			}
	  		else 
	  			{
	  			//return JSON
	  			$this->render('ajax_get_course_attendance', 'ajax');//which ctp, which layout
	  			}
			}
		}
		
	function add() 
		{
		$oktosave = false;
		$returnArray=array();
		$returnArray['error_text']="";
		if(empty($this->request->data))
			{
			if(isset($this->request['named']['event_id']))
				{
				$event_id = $this->request['named']['event_id'];
				$event = $this->EventsUser->Event->find('first', array('conditions'=>array('Event.id'=>$event_id)));
				$this->set('event',$event);
				}
			else
				{
				//$this->Session->setFlash(__('Invalid event', true));
				$this->log('Unknown event - no id passed','medswipe_errors'.date("d-m-Y"));
				$returnArray['error_text']="Unknown event";
				$this->set('returnArray', $returnArray);
				$this->render('add_return', 'ajax');//which ctp, which layout
				//$this->redirect(array('action' => 'index'));
				}
			}
		else 
			{
			$user = $this->EventsUser->User->find('first', array('conditions' => array('User.username' => $this->request->data['User']['username'])));  //rtrim removes extra full stop
			// if not present, create user
			if($user)
				{
				//user with this username already exists
				$this->request->data['EventsUser']['user_id'] = $user['User']['id'];
				$first_name = $user['User']['first_name'];  //needed for display back to user
				$last_name = $user['User']['last_name'];
				$this->log('Existing user '. $this->request->data['User']['username'] .' - ' . 'begin add by username to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
				//CakeLog::write('debug', $user['User']['id'].' already exists');
				unset($this->request->data['User']);  //don't want to write any user data
				$oktosave = true;
				}
			else 
				{
				//user might not exist or may already exist with no recorded username or a different username
				//Lookup user on Oak LDAP
				$oak_person_id = null;
				//putenv("KRB5CCNAME=/var/lib/webauth/krb5cc_ldap"); //old Webauth variable
				putenv("KRB5CCNAME=/var/www/krb5cc_oak-ldap");
				$base = 'ou=people,dc=oak,dc=ox,dc=ac,dc=uk';
				$ldap = false;
				$ldap = ldap_connect('ldaps://ldap.oak.ox.ac.uk');
				//CakeLog::write('debug', $this->request->data['User']['username'].' does not already exists');
				if ( $ldap === false ) 
					{
					//$this->Session->setFlash(__('Could not contact LDAP server, please try again.', true));
					$returnArray['error_text']="Could not contact LDAP server, please try again.";
					$this->log('Could not contact LDAP server. Trying to add by username: '.$this->request->data['User']['username'].' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
					//CakeLog::write('error', 'Could not contact LDAP server, please try again.');
					}
				else
					{
					//debug($ldap);
					//CakeLog::write('debug', 'LDAP server contacted, now try bind');
					ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
					$ldap_bind_result = ldap_sasl_bind($ldap, NULL, NULL, 'GSSAPI');
					if ( $ldap_bind_result === false ) 
						{
						//$this->Session->setFlash(__('Could not bind to LDAP server, please try again.', true));
						$returnArray['error_text']="Could not bind to LDAP server, please try again.";
						$this->log('Could not bind to LDAP server. Trying to add by username: '.$this->request->data['User']['username'].' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
						//CakeLog::write('error', 'Could not bind to LDAP server, please try again.');
						}
					else
						{
						//CakeLog::write('debug', 'LDAP server bound to, now try search by username');
						$search = ldap_search($ldap, $base, '(oakPrincipal=krbPrincipalName='.strtolower($this->request->data['User']['username']).'@OX.AC.UK,cn=OX.AC.UK,cn=KerberosRealms,dc=oak,dc=ox,dc=ac,dc=uk)');
						//$search = ldap_search($ldap, $base, '(sn=hoggarth)');
						$first_name = 'first';
					   	$last_name = 'last';
						if($search)
							{
							$entries = ldap_get_entries($ldap,$search);
							if ($entries['count']>0) 
								{
								//CakeLog::write('debug', 'Found user '.$entries[0]["givenname"][0].' '.$entries[0]["sn"][0]);
								$this->log('Found user '. $this->request->data['User']['username'] .' in LDAP when adding to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
								$first_name = $entries[0]["givenname"][0]; //'Not in';//
								$last_name = $entries[0]["sn"][0]; //$this->request->data['User']['username']; //
								$barcode = $entries[0]["oakuniversitybarcodefull"][0]; //'13245365';//
								$oak_person_id = $entries[0]["oakprimarypersonid"][0]; //'13245365';//
								}
							else
								{
								//$this->Session->setFlash(__("Can't find user, please try again.", true));
								$returnArray['error_text']="Can't find user, please try again.";
								$this->log('Couldnt find user '. $this->request->data['User']['username'] .' in LDAP when adding to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
								//CakeLog::write('error', 'Search succeeded but no entries');
								}
							}
						else
							{
							//CakeLog::write('error', 'Search failed');
							//$this->Session->setFlash(__("Can't find user, please try again.", true));
							$returnArray['error_text']="Can't find user, please try again.";
							$this->log('Search failed looking for user: '. $this->request->data['User']['username'] .' in LDAP when adding to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
							}
						} 
					}
				
				if($oak_person_id)
					{
					//CakeLog::write('debug', 'Have oak_person_id, look up in EventsUser');
					$this->log('Have oak_person_id: '. $oak_person_id .'. Looking up in Users for EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
					$oktosave = true;
					//let's check whether this user already in db
					$user = $this->EventsUser->User->find('first', array('conditions' => array('User.oak_person_id' => $oak_person_id)));  
					if($user)
						{
						//CakeLog::write('debug', 'Already exists, update');
						$this->log('User already exists by OakPersonId: '. $oak_person_id .' - User is: '.$user['User']['id'].'. Updating request data for EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
						//user does already exist but we need to update username
						$this->request->data['EventsUser']['user_id'] = $user['User']['id'];
						$this->request->data['User']['id'] = $user['User']['id']; //by passing id, should updfate rather than create a new one
						$this->request->data['User']['barcode'] = Security::hash($barcode);
						}
					else 
						{
						//CakeLog::write('debug', 'Doesnt exist, create. oak_person_id= '.$oak_person_id);
						$this->log('User doesnt already exists by OakPersonId: '. $oak_person_id .'. Creating user: '.$first_name.''.$last_name.' in request data for EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
						//user doesn't already exist - let's create them
						$this->request->data['User']['oak_person_id'] = $oak_person_id;
						$this->request->data['User']['first_name'] = $first_name;
					    $this->request->data['User']['last_name'] = $last_name;
					    $this->request->data['User']['barcode'] = Security::hash($barcode);
						}
					}
				}
			$this->request->data['EventsUser']['attended_at'] = DboSource::expression('NOW()');
			$this->request->data['EventsUser']['ip_address'] = $this->RequestHandler->getClientIp();
			$this->request->data['EventsUser']['registration_type'] = 'sso';
			
			$this->autoRender = false;
			if($oktosave)
				{
				if ($this->EventsUser->saveAll($this->request->data)) 
					{
					$this->log('User '.'('.$first_name.' '.$last_name.') - ' . 'success add by useername to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
					$returnArray['first_name']=$first_name;
					$returnArray['last_name']=$last_name;
					$returnArray['event_id']=$this->request->data['EventsUser']['event_id'];
					$returnArray['course_id']=$this->request->data['Course']['id'];
					if(file_exists(ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS."img/attendees/".strtolower(str_replace(" ","_",$last_name))."_".strtolower(str_replace(" ","_",$first_name)).".jpg")){
						$returnArray['image_name']= strtolower(str_replace(" ","_",$last_name))."_".strtolower(str_replace(" ","_",$first_name));
						}
					else
						{
						$returnArray['image_name']="missing";
						}
					}
				else 
					{
					//actually make success.ctp show list of logged in users
					$returnArray['error_text']="Couldn't save user";
					$this->log('User '.'('.$first_name.' '.$last_name.':'.$this->request->data['User']['username'].') - ' . 'couldnt save user (not able to EventsUser->saveAll) to EventId '.$this->request->data['EventsUser']['event_id'].': '.$returnArray['error_text'],'medswipe_errors'.date("d-m-Y"));
					}
				}
			else
				{
				if($returnArray['error_text']==""){
					$returnArray['error_text']="Something has gone wrong, please try again.";
					}
				$this->log('User '.'('.$first_name.' '.$last_name.':'.$this->request->data['User']['username'].') - ' . 'couldnt save user (not oktosave) to EventId '.$this->request->data['EventsUser']['event_id'].': '.$returnArray['error_text'],'medswipe_errors'.date("d-m-Y"));
				}
			$this->set('returnArray', $returnArray);
			$this->render('add_return', 'ajax');//which ctp, which layout
			}
		}
		
	function ajax_add() 
		{
		$returnArray=array();
		$returnArray['error_text']="";
		$oktosave = false;
		if (!empty($this->request->data)&&preg_match("/^\d{7,}.$/",$this->request->data['User']['barcode'])) //7 or more numbers and . at end allows any character e.g X, %, .
			{
			$barcode = $this->request->data['User']['barcode'];
			$this->log('User '. $barcode .' - ' . 'begin add by barcode to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
			//CakeLog::write('debug', 'User with barcode: '.$barcode);
			//Lookup barcode
			$user = $this->EventsUser->User->find('first', array('conditions' => array('User.barcode' => Security::hash($barcode))));  
			if($user)
				{
				//CakeLog::write('debug', 'User already exists');
				//user with this barcode already exists
				$this->log('User with '. $barcode .' already exists - User: '. $user['User']['first_name'].' '.$user['User']['last_name']. '. Populate request data to add to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
				$this->request->data['EventsUser']['user_id'] = $user['User']['id'];
				$first_name = $user['User']['first_name'];  //needed for display back to user
				$last_name = $user['User']['last_name'];
				unset($this->request->data['User']);  //don't want to write any user data
				$oktosave = true;
				}
			else 
				{
				$oak_person_id = null;
				//user might not exist or may already exist with a different barcode
				//CakeLog::write('debug', 'User might not exist or may already exist with a different barcode');
				//Lookup user on Oak LDAP
				putenv("KRB5CCNAME=/var/lib/webauth/krb5cc_ldap");
				$base = 'ou=people,dc=oak,dc=ox,dc=ac,dc=uk';
				$ldap = false;
				$ldap = ldap_connect('ldaps://ldap.oak.ox.ac.uk');
				if (!$ldap) 
					{
					//$this->Session->setFlash(__('Could not contact LDAP server, please try again.', true));
					$returnArray['error_text']="Could not contact LDAP server, please try again.";
					//CakeLog::write('error', 'Could not contact LDAP server, please try again.');
					$this->log('Could not contact LDAP server. Trying to add by barcode: '.$barcode.' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
					}
				else
					{
					CakeLog::write('debug', 'LDAP server contacted, now try bind');
					ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
					$ldap_bind_result = ldap_sasl_bind($ldap, NULL, NULL, 'GSSAPI');
					if (!$ldap_bind_result) 
						{
						//$this->Session->setFlash(__('Could not bind to LDAP server, please try again.', true));
						$returnArray['error_text']="Could not bind to LDAP server, please try again.";
						//CakeLog::write('error', 'Could not bind to LDAP server, please try again.');
						$this->log('Could not bind to LDAP server. Trying to add by barcode: '.$barcode.' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
						}
					else
						{
						//CakeLog::write('debug', 'LDAP server bound to, now try search by barcode');
						$search = ldap_search($ldap, $base, '(oakUniversityBarcodeFull='.$barcode.')');
						if ($search) 
							{
							$entries = ldap_get_entries($ldap,$search);
							if($entries['count']>0)
								{
								//CakeLog::write('debug', 'Found user '.$entries[0]["givenname"][0].' '.$entries[0]["sn"][0]);
								$first_name = $entries[0]["givenname"][0]; //'Not in';//
								$last_name = $entries[0]["sn"][0]; //$barcode; //
								$oak_person_id = $entries[0]["oakprimarypersonid"][0]; //'13245365';//
								$username = $entries[0]["oakoxfordssousername"][0]; //'mdiv0256';//
								$this->log('Found user '. $first_name.' '. $last_name.' in LDAP when adding to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
								}
							else
								{
								//$this->Session->setFlash(__("Can't find user, please try again.", true));
								$returnArray['error_text']="Can't find user, please try again.";
								//CakeLog::write('error', 'Search succeeded but no entries');
								$this->log('Search succeeded but no entries. Trying to add by barcode: '.$barcode.' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
								}
							}
						else
							{
							$returnArray['error_text']="Can't find user, please try again.";
							//$this->Session->setFlash(__("Can't find user, please try again.", true));
							//CakeLog::write('error', 'Search failed');
							$this->log('Search failed. Trying to add by barcode: '.$barcode.' to EventId '.$this->request->data['EventsUser']['event_id'],'medswipe_errors'.date("d-m-Y"));
							}
						} 
					}
				if($oak_person_id)
					{
					$oktosave = true;
					//let's check whether this user already in db
					$user = $this->EventsUser->User->find('first', array('conditions' => array('User.oak_person_id' => $oak_person_id)));  
					if($user)
						{
						//CakeLog::write('debug', 'Already exists, update');
						$this->log('User already exists by Barcode: '. $barcode .' - User is: '.$user['User']['id'].'. Updating request data for EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
						//user does already exist but we need to update barcode
						$this->request->data['EventsUser']['user_id'] = $user['User']['id'];
						$this->request->data['User']['id'] = $user['User']['id']; //by passing id, should updfate rather than create a new one
						$this->request->data['User']['barcode'] = Security::hash($barcode);
						}
					else 
						{
						//CakeLog::write('debug', 'Doesnt exist, create. oak_person_id= '.$oak_person_id);
						$this->log('User doesnt already exist by OakPersonId: '. $oak_person_id .'. Creating user: '.$username.' in request data for EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
						//user doesn't already exist - let's create them
						$this->request->data['User']['oak_person_id'] = $oak_person_id;
						$this->request->data['User']['username'] = $username; //populate with some dummy values as we don't read these from OAK
					    $this->request->data['User']['password'] = $username."_".$username; //populate with some dummy values as we don't read these from OAK
					    $this->request->data['User']['first_name'] = $first_name;
					    $this->request->data['User']['last_name'] = $last_name;
					    $this->request->data['User']['barcode'] = Security::hash($barcode);
						}
					}
				}
			$this->request->data['EventsUser']['attended_at'] = DboSource::expression('NOW()');
			$this->request->data['EventsUser']['ip_address'] = $this->RequestHandler->getClientIp();
			$this->request->data['EventsUser']['registration_type'] = 'barcode';
			
			$this->autoRender = false;
			if($oktosave)
				{
				if ($this->EventsUser->saveAll($this->request->data)) 
					{
					//$this->set('first_name', $first_name);
					//$this->set('last_name', $last_name);
					//$this->set('event_id', $this->request->data['EventsUser']['event_id']);
					//$this->set('course_id', $this->request->data['Course']['id']);
					//$this->render('add_success', 'ajax');//which ctp, which layout
					$this->log('User '. $barcode .'('.$first_name.' '.$last_name.') - ' . 'success add by useername to EventId '.$this->request->data['EventsUser']['event_id'],'activity'.date("d-m-Y"));
					$returnArray['first_name']=$first_name;
					$returnArray['last_name']=$last_name;
					$returnArray['event_id']=$this->request->data['EventsUser']['event_id'];
					$returnArray['course_id']=$this->request->data['Course']['id'];
					if(file_exists(ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS."img/attendees/".strtolower(str_replace(" ","_",$last_name))."_".strtolower(str_replace(" ","_",$first_name)).".jpg")){
						$returnArray['image_name']= strtolower(str_replace(" ","_",$last_name))."_".strtolower(str_replace(" ","_",$first_name));
						}
					else
						{
						$returnArray['image_name']="missing";
						}
					}
				else 
					{
					//actually make success.ctp show list of logged in users
					//$this->render('add_failure', 'ajax');//which ctp, which layout
					$returnArray['error_text']="Couldn't save user";
					$this->log('User '. $barcode .'('.$first_name.' '.$last_name.') - ' . 'couldnt save user to EventId '.$this->request->data['EventsUser']['event_id'].': '.$returnArray['error_text'],'medswipe_errors'.date("d-m-Y"));
					}
				}
			else
				{
				if($returnArray['error_text']==""){
					$returnArray['error_text']="Something has gone wrong, please try again.";
					}
				$this->log('User '. $barcode .'('.$first_name.' '.$last_name.') - ' . 'Not okToSave save user to EventId '.$this->request->data['EventsUser']['event_id'].': '.$returnArray['error_text'],'medswipe_errors'.date("d-m-Y"));
				}
			}
		else
			{
			$returnArray['error_text']="Couldn't recognise that barcode";
			$this->log('Couldnt recognise barcode: '. $this->request->data['User']['barcode']. ' to save user to EventId '.$this->request->data['EventsUser']['event_id'].': '.$returnArray['error_text'],'medswipe_errors'.date("d-m-Y"));
			//$this->autoRender = false;
			//actually make success.ctp show list of logged in users
			//$this->render('add_failure_barcode', 'ajax');//which ctp, which layout
			}
		$this->set('returnArray', $returnArray);
		$this->render('add_return', 'ajax');//which ctp, which layout
		}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for events user', true));
			$this->redirect(array('action'=>'index'));
		}
		/*if ($this->EventsUser->delete($id)) {
			$this->Session->setFlash(__('Events user deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Events user was not deleted', true));
		$this->redirect(array('action' => 'index'));*/
	}
}
?>
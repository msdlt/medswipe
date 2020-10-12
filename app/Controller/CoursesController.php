<?php
class CoursesController extends AppController {

	var $name = 'Courses';
	var $helpers = array('FormEnum');
	public $actsAs = array('Containable');
	public $components = array('RequestHandler');

	function index() {
		//will only show courses to those who are admin or staff
		$this->Course->recursive = -1;
		$this->Course->Behaviors->attach('Containable'); //allows 'contain' below
		$this->paginate = array(
			'fields'=>array('DISTINCT(Course.id)', 'Course.name'),
			'conditions'=>array(
 				'CoursesUser.user_id' => $this->Auth->user('id'), //ie only show courses to users who are in CoursesUser table 
				'CoursesUser.user_type' => array('admin','staff')
				),
			'order'=>'Course.name ASC',
			'joins' => array(
				array(
 					'table' => 'courses_users',
				 	'type' => 'LEFT',
					'alias' => 'CoursesUser', 
					'conditions' => array(
 						'Course.id = CoursesUser.course_id'
 						)
 					)
 				),
 			'contain' => array(
				'CoursesUser'=>array(
 					'fields'=>array('user_type','user_id'),
					'conditions'=>array('CoursesUser.user_id' => $this->Auth->user('id'))
 					)
				) 
 			); 
 		$this->set('title_for_layout', 'Courses');
		$this->set('courses', $this->paginate('Course'));
		$this->set('navigation', 'courses');
	}
	
	function add() {
		//anyone can add a course
		//debug($this->request->data);
		if (!empty($this->request->data)) {
			if ($this->Course->saveAll($this->request->data, array('validate'=>'first')))
				{
				$this->Session->setFlash(__('The course has been saved', true));
				$this->redirect(array('action' => 'edit', $this->Course->id));
				}
			else 
				{
				$this->Session->setFlash(__('The course could not be saved. Please correct any errors and try again.', true));
				}
			}
		$this->set('title_for_layout', 'Add course');
		$this->set('navigation', 'courses');
		}
		
	function copy() {
		$returnArray=array();
		$returnArray['error_text']="";
		//anyone can add a course
		if (!empty($this->request->data)) {
			//first read existing course and users
			$existingCourse=$this->Course->find('first', array(
				'contain'=>array(
						'CoursesUser'=>array(
							'fields'=>array('user_id','user_type')
							)
						),
				'fields'=>array('name'),
				'conditions'=>array('Course.id'=>$this->request->data['Course']['copy_id'])
				)
			);
			//debug($existingCourse);
			foreach($existingCourse['CoursesUser'] as &$courseUser){ //pass by reference otherwise unset below only unsets within function
				unset($courseUser['course_id']);  //don't want these in here as we're going to create new associations with saveAssociated
				}
			unset($existingCourse['Course']['id']);
			$existingCourse['Course']['name']=$this->request->data['Course']['name'];
			//debug($existingCourse);
			//now save our new course with existing users
			if ($this->Course->saveAssociated($existingCourse,array('validate'=>'true'))){
				}
			else{
				$returnArray['error_text']='The course could not be saved. Please correct any errors and try again. '.$this->Course->validationErrors;
				}
		}
		$this->set('returnArray', $returnArray);
		$this->set('_serialize', 'returnArray');//output json without view
		}
		
	
	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid course', true));
			$this->redirect(array('action' => 'index'));
		}
		//make sure this user has admin rights over this course
		$allowed_user = $this->Course->CoursesUser->find('first',array(
				'conditions' => array(
					'Course.id'=>$id,
					'User.id'=>$this->Auth->user('id'),
					'CoursesUser.user_type'=>'admin'
					)
				)
			);
		if(!$allowed_user)
			{
			$this->Session->setFlash(__("You don't have permission to edit that course", true));
			$this->redirect(array('action' => 'index'));
			}
		if (!empty($this->request->data)) {
			//save request data to hand back on failure
			$saved_request_data = $this->request->data;
			//get list of existing users for this course from db
			$this->Course->CoursesUser->recursive = 2;
			$existing_users = $this->Course->CoursesUser->find('list',array(
				'fields' => array('CoursesUser.id'),
				'conditions' => array(
					'CoursesUser.user_id !='=>$this->Auth->user('id'), //exlude current user from possible deletion
					'CoursesUser.course_id' => $id
					)
			));
			if(isset($this->request->data['CoursesUser'])&&count($this->request->data['CoursesUser'])>0)
				{
				foreach($this->request->data['CoursesUser'] as $key=>$value)
					{
					if(isset($value['id']))
						{
						//newly created users won't have ids
						$current_users[$value['id']] = $value['id'];
						debug($value['id']);
						}
					elseif(isset($value['username']))
						{
						//now need to find details of newly added users
						//look up user in Users table
						$user = $this->Course->CoursesUser->User->find('first', array('conditions' => array('User.username' => $value['username'])));  //rtrim removes extra full stop
						if($user)
							{
							//user with this username already exists
							//course_id and user_type already in this->dat, just need to add user_id
							$this->request->data['CoursesUser'][$key]['user_id']=$user['User']['id'];
							}
						else 
							{
							//create user - look up necessaruy details first
							//Lookup user on Oak LDAP
							putenv("KRB5CCNAME=/var/lib/webauth/krb5cc_ldap");
							$base = 'ou=people,dc=oak,dc=ox,dc=ac,dc=uk';
							$ldap = false;
							$ldap = ldap_connect('ldaps://ldap.oak.ox.ac.uk');
							if ( $ldap === false ) {
								$this->Session->setFlash(__('Could not contact LDAP server to lookup user, please try again.', true));
								$this->request->data = $saved_request_data;
								}
							else
								{
								ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
								$ldap_bind_result = ldap_sasl_bind($ldap, NULL, NULL, 'GSSAPI');
								if ( $ldap_bind_result === false ) {
									$this->Session->setFlash(__('Could not bind to LDAP server to lookup user, please try again.', true));
									$this->request->data = $saved_request_data;
									}
								else
									{
									$search = ldap_search($ldap, $base, '(oakPrincipal=krbPrincipalName='.$value['username'].'@OX.AC.UK,cn=OX.AC.UK,cn=KerberosRealms,dc=oak,dc=ox,dc=ac,dc=uk)');
									//$search = ldap_search($ldap, $base, '(sn=hoggarth)');
									$first_name = 'first';
								   	$last_name = 'last';
									if ($search) {
										$entries = ldap_get_entries($ldap,$search);
										print_r($entries);
										$first_name = $entries[0]["givenname"][0]; //'Pauline';////'Pauline';//
								    	$last_name = $entries[0]["sn"][0]; //'Woolley'; ////'Woolley'; //
								    	$oak_person_id = $entries[0]["oakprimarypersonid"][0]; //'13245365';////'13245365';//
										}
									else
										{
										$this->Session->setFlash(__("Can't find user ". $value['username'] .", please try again.", true));
										$this->request->data = $saved_request_data;
										}
									} 
								}
							
							if($oak_person_id)
								{
								//user doesn't already exist - let's create them
								$user_to_insert['User']['oak_person_id'] = $oak_person_id;
								$user_to_insert['User']['username'] = $value['username']; //populate with some dummy values as we don't read these from OAK
							    $user_to_insert['User']['password'] = $value['username'].'_'.$value['username']; //populate with some dummy values as we don't read these from OAK
							    $user_to_insert['User']['first_name'] = $first_name;
							    $user_to_insert['User']['last_name'] = $last_name;
								$this->Course->CoursesUser->User->create();
								if ($this->Course->CoursesUser->User->save($user_to_insert)) 
									{
									$this->request->data['CoursesUser'][$key]['user_id']=$this->Course->CoursesUser->User->id;
									} 
								else 
									{
									$this->Session->setFlash(__('The user could not be saved. Please correct any errors and try again.', true));
									$this->request->data = $saved_request_data;
									}
							    }
														
							}	
										
						}
					}
				$not_present_in_current_courses_users = array_diff($existing_users,$current_users);
				}
			else 
				{
				//i.e. delete them all
				$not_present_in_current_courses_users = $existing_users;
				}
			//get list of existing events for this course from db
			$existing_events = $this->Course->Event->find('list',array(
				'fields' => array('Event.id'),
				'conditions' => array(
					'Event.course_id' => $this->request->data['Course']['id']
					)
			));
			//get list of existing events for this course from db which have events users attached and can't be deleted
			$existing_events_with_users = $this->Course->Event->find('list',array(
				'fields' => array('Event.id'),
				'joins' => array(
					array(
	 					'table' => 'events_users',
					 	'type' => 'INNER',
						'alias' => 'EventsUser', 
						'conditions' => array(
	 						'Event.id = EventsUser.event_id'
	 						)
	 					)
	 				), 
				'conditions' => array(
					'Event.course_id' => $this->request->data['Course']['id']
					)
			));
			$existing_events_liable_to_be_deleted = array_diff($existing_events,$existing_events_with_users);
			//now work through events in $this->request->data to create $new_events
			if(isset($this->request->data['Event'])&&count($this->request->data['Event'])>0)
				{
				$current_events = array();
				foreach($this->request->data['Event'] as $key=>$event)
					{
					//debug($event);
					//convert dates to database format
					$date = DateTime::createFromFormat('d/m/y H:i', $event['date']);
					//debug($date);
					$this->request->data['Event'][$key]['date'] = date_format($date,'Y-m-d H:i:s');
					//debug($this->request->data['Event'][$key]['date']);
					$date = DateTime::createFromFormat('d/m/y H:i', $event['end_date']);
					$this->request->data['Event'][$key]['end_date'] = date_format($date,'Y-m-d H:i:s');
					//$this->request->data['Event'][$key]['date'] = $date->format('Y-m-d H:i:s');
					if(isset($event['id']))
						{
						//newly addded events won't have an id yet
						$current_events[$event['id']] = $event['id'];
						}
					}
				$not_present_in_current = array_diff($existing_events_liable_to_be_deleted,$current_events);
				}
			else 
				{
				$not_present_in_current = $existing_events_liable_to_be_deleted;
				}
			//debug($this->request->data);
			$this->Course->Event->begin(); // Start our transaction 
			if($this->Course->Event->deleteAll(array(
						'Event.id'=>$not_present_in_current
						)
					)&& $this->Course->CoursesUser->deleteAll(array(
						'CoursesUser.id'=>$not_present_in_current_courses_users
						)
					)
				)
				{
				//debug($this->request->data);
				if ($this->Course->saveAll($this->request->data, array('validate'=>'first')))//, array('validate'=>'first')))
					{
					$this->Course->Event->commit();
					$this->Session->setFlash(__('The course has been saved', true));
					$this->redirect(array('action' => 'index'));
					}
				else 
					{
					$this->Course->Event->rollback();  
					$this->Session->setFlash(__('The course could not be saved. Please correct any errors and try again.', true));
					$this->request->data = $saved_request_data;
					}
				}
			else
				{
				$this->Course->Event->rollback(); 
				$this->request->data = $saved_request_data;
				}
			}
		else{
			$this->Course->Behaviors->attach('Containable'); //allows 'contain' below
			$this->Course->id = $id;
			$this->Course->contain(array('Event','CoursesUser','CoursesUser'=>array('User'=>array('order' => 'User.last_name DESC'))));
			$this->request->data = $this->Course->read();
			foreach ($this->request->data['Event'] as $key=>$event)
				{
				$this->request->data['Event'][$key]['date'] = date("d/m/y H:i", strtotime($event['date']));
				$this->request->data['Event'][$key]['end_date'] = date("d/m/y H:i", strtotime($event['end_date']));
				}
			$this->request->data['CoursesUser'] = Set::sort($this->request->data['CoursesUser'], '{n}.User.last_name', 'asc');
			//prevent delete button for events with data
			//get list of existing events for this course from db which have events users attached and can't be deleted
			$existing_events_with_users = $this->Course->Event->find('list',array(
				'fields' => array('Event.id'),
				'joins' => array(
					array(
	 					'table' => 'events_users',
					 	'type' => 'INNER',
						'alias' => 'EventsUser', 
						'conditions' => array(
	 						'Event.id = EventsUser.event_id'
	 						)
	 					)
	 				), 
				'conditions' => array(
					'Event.course_id' => $id
					)
				));
			}
		//$this->set('courses_',$this->Course->CoursesUser->find('list', array('conditions'=>array('CoursesUser.course_id'=>$id,'CoursesUser.user_type'=>'staff'))));
		//}
		//get list of user_types
		//$userTypes = $this->Course->find('list', array(
				//'fields' => array('User.id', 'User.name'),
				//'conditions' => array('Article.status !=' => 'pending'),
				//'recursive' => 0
		//));
		
		
		$this->set('errors', $this->Course->validationErrors);
		$this->set('existing_events_with_users', $existing_events_with_users);	
		$this->set('title_for_layout', 'Edit course');
		$this->set('navigation', 'courses');
	}
	
	function add_many_users() 
		{
		$course_id = null;
		if(isset($this->params['named']['course_id'])|| isset($this->request->data['Course']['id']))
			{
			if(isset($this->params['named']['course_id']))$course_id=$this->params['named']['course_id'];
			if(isset($this->request->data['Course']['id']))$course_id=$this->request->data['Course']['id'];
			//make sure this user has admin rights over this course
			$allowed_user = $this->Course->CoursesUser->find('first',array(
					'conditions' => array(
						'Course.id'=>$course_id,
						'User.id'=>$this->Auth->user('id'),
						'CoursesUser.user_type'=>'admin'
						)
					)
				);
			if(!$allowed_user)
				{
				$this->Session->setFlash(__("You don't have permission to edit that course", true));
				$this->redirect(array('action' => 'index'));
				}
			if (!empty($this->request->data)) 
				{
				if(isset($this->request->data['Course']['usernames']))
					{
					//now need to read Oxford usernames from the textarea into an array
					$users=explode("\n",$this->request->data['Course']['usernames']);
					//print_r($users);
					foreach($users as $key=>$value)
						{
						$value=trim($value);
						if(strlen($value)>0)
							{
							//now need to find details of newly added users
							//look up user in Users table
							$user = $this->Course->CoursesUser->User->find('first', array('conditions' => array('User.username' => $value)));  //rtrim removes extra full stop
							if($user)
								{
								//user with this username already exists
								//course_id and user_type already in this->dat, just need to add user_id
								$this->request->data['CoursesUser'][$key]['user_id']=$user['User']['id'];
								}
							else 
								{
								//create user - look up necessaruy details first
								//Lookup user on Oak LDAP
								putenv("KRB5CCNAME=/var/lib/webauth/krb5cc_ldap");
								$base = 'ou=people,dc=oak,dc=ox,dc=ac,dc=uk';
								$ldap = false;
								$ldap = ldap_connect('ldaps://ldap.oak.ox.ac.uk');
								if ( $ldap === false ) {
									$this->Session->setFlash(__('Could not contact LDAP server to lookup user, please try again.', true));
									}
								else
									{
									ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
									$ldap_bind_result = ldap_sasl_bind($ldap, NULL, NULL, 'GSSAPI');
									if ( $ldap_bind_result === false ) {
										$this->Session->setFlash(__('Could not bind to LDAP server to lookup user, please try again.', true));
										}
									else
										{
										//$search = ldap_search($ldap, $base, '(oakPrincipal=krbPrincipalName=ball3136@OX.AC.UK,cn=OX.AC.UK,cn=KerberosRealms,dc=oak,dc=ox,dc=ac,dc=uk)');
										$search = ldap_search($ldap, $base, '(oakPrincipal=krbPrincipalName='.$value.'@OX.AC.UK,cn=OX.AC.UK,cn=KerberosRealms,dc=oak,dc=ox,dc=ac,dc=uk)');
										//$search = ldap_search($ldap, $base, '(sn=hoggarth)');
										$first_name = 'first';
										$last_name = 'last';
										if ($search) {
											$entries = ldap_get_entries($ldap,$search);
											//print_r($entries);
											$first_name = $entries[0]["givenname"][0]; //'Pauline';////'Pauline';//
											$last_name = $entries[0]["sn"][0]; //'Woolley'; ////'Woolley'; //
											$oak_person_id = $entries[0]["oakprimarypersonid"][0]; //'13245365';////'13245365';//
											}
										else
											{
											$this->Session->setFlash(__("Can't find user ". $value .", please try again.", true));
											}
										} 
									}
								
								if($oak_person_id)
									{
									//user doesn't already exist - let's create them
									$user_to_insert['User']['oak_person_id'] = $oak_person_id;
									$user_to_insert['User']['username'] = $value; //populate with some dummy values as we don't read these from OAK
									$user_to_insert['User']['password'] = $value.'_'.$value; //populate with some dummy values as we don't read these from OAK
									$user_to_insert['User']['first_name'] = $first_name;
									$user_to_insert['User']['last_name'] = $last_name;
									$this->Course->CoursesUser->User->create();
									if ($this->Course->CoursesUser->User->save($user_to_insert)) 
										{
										//echo $this->Course->CoursesUser->User->id;
										$this->request->data['CoursesUser'][$key]['user_id']=$this->Course->CoursesUser->User->id;
										} 
									else 
										{
										$this->Session->setFlash(__('The user could not be saved. Please correct any errors and try again.', true));
										}
									}
								}
							//debug($key);
							$this->request->data['CoursesUser'][$key]['user_type']='attendee'; //ie we can only add members with this method
							$this->request->data['CoursesUser'][$key]['course_id']=$course_id;								
							}
						}
					}
				if ($this->Course->saveAll($this->request->data))//, array('validate'=>'first')))
					{
					$this->Session->setFlash(__('The course has been saved', true));
					$this->redirect(array('controller'=>'courses', 'action' => 'edit', $course_id));//
					}
				else 
					{
					$this->Session->setFlash(__('The course could not be saved. Please correct any errors and try again.', true));
					$this->redirect(array('controller'=>'courses', 'action' => 'edit', $course_id));//
					}
				}
			}
		$this->layout = 'ajax';
		//$this->set('errors', $this->Course->validationErrors);
		$this->set('course_id', $course_id);
		}
	

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for course', true));
			$this->redirect(array('action'=>'index'));
		}
		//make sure this user has admin rights over this course
		$allowed_user = $this->Course->CoursesUser->find('first',array(
				'conditions' => array(
					'Course.id'=>$id,
					'User.id'=>$this->Auth->user('id'),
					'CoursesUser.user_type'=>'admin'
					)
				)
			);
		if(!$allowed_user)
			{
			$this->Session->setFlash(__("You don't have permission to edit that course", true));
			$this->redirect(array('action' => 'index'));
			}
		//now make sure course doesn't contain any events with events_users	
		//below commented out for now!!
			
		//if ($this->Course->delete($id)) {
			//$this->Session->setFlash(__('Course deleted', true));
			//$this->redirect(array('action'=>'index'));
		//}
		//$this->Session->setFlash(__('Course was not deleted', true));
		//$this->redirect(array('action' => 'index'));
	}
}
?>
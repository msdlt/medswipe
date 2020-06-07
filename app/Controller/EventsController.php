<?php
class EventsController extends AppController {

	var $name = 'Events';

	public function index_by_date() {
		$username= $_SERVER['REMOTE_USER']; //"mdiv0044"; //
		//MSDLT_LOCAL $username= "mdiv0044";	 - relace with line above on live server
		if(!AuthComponent::user('id')){ //ie user isn't logged in
			if(isset($username))
				{
				//Find user_id
				$user = $this->Event->EventsUser->User->find('first', array('conditions' => array('User.username' => $username), 'fields' => array('User.id')));
				if(!empty($user))
					{
					$user_id = $user['User']['id'];
					$this->request->data = array(
							'User' => array(
									'username' => $username
									//need to read these long-term
									//'first_name' => $first_name,
									//'last_name' => $last_name,
							)
					);
					$this->Event->EventsUser->User->id = $user_id;
					$this->Event->EventsUser->User->saveField('last_login', DboSource::expression('NOW()'));
					}
				else
					{
					//need to create this user
					$this->Event->EventsUser->User->create();
					$this->request->data = array(
				    		'User' => array(
				        		'username' => $username,
				        		'password' => $username."_".$username,
				        		'last_login' => DboSource::expression('NOW()')
				        		//need to read these long-term
				        		//'first_name' => $first_name,
				        		//'last_name' => $last_name,
				        		)
				    		);
					$this->Event->EventsUser->User->save($this->request->data);
					$user_id = $this->Event->EventsUser->User->id;
					}
				//auth component login method expects user_id in array format as part of user object
				//$this->request->data['User'] = array();
				$this->request->data['User'] = array_merge($this->request->data['User'], array('id' => $user_id));
				//if($this->Auth->login($this->request->data['User']))
					//{
					//$this->Session->setFlash('Logged in');
					//}
				$logged_in = $this->Auth->login($this->request->data['User']);
				}
			}
		$this->Event->recursive = 0;
		$this->paginate = array(
				'order'=>array(
	 				'Event.date'=>'asc'
					),
	 			'conditions'=>array(
					//"Event.date = NOW()"
	 				//"Event.date >=" => date('Y-m-d H:i:s', strtotime("Today")),
	 				//"Event.date <" => date('Y-m-d H:i:s', strtotime("Tomorrow"))
					"Event.date <=" => date('Y-m-d H:i:s', strtotime("+30min")),
	 				"Event.end_date >=" => date('Y-m-d H:i:s', strtotime("-30min"))
	 				)
	 			);
		//if(!isset($this->params['named']['oaked'])||$this->params['named']['oaked']=='false')
			//{
			//temporary step to redirect to https://msdlt.physiol.ox.ac.uk/oak-test/oak-test.php until Neil sorts out permissions issue
			//$this->redirect('https://msdlt.physiol.ox.ac.uk/oak-test/oak-test.php'); //MSDLT_LOCAL - reinstate on live server
			//}
		$this->set('events', $this->paginate('Event'));
		$this->set('navigation', 'events');
	}

	public function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid event', true));
			$this->redirect(array('action' => 'index'));
		}
		$event = $this->Event->read(null, $id);
		//is user an administrator or staff for this course
		$role = $this->Event->Course->CoursesUser->find('first', array('fields'=>'user_type', 'conditions'=>array('CoursesUser.course_id'=>$event['Course']['id'],'CoursesUser.user_id'=>AuthComponent::user('id'))));
		$this->set('role', $role);
		$this->set('event', $event);
		$this->set('navigation', 'events');
	}
}
?>
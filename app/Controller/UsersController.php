<?php
class UsersController extends AppController {

	var $name = 'Users';
	
	function beforeFilter() {
	//$this->Auth->autoRedirect = false;
	}
	
	/**
	* The AuthComponent provides the needed functionality
	* for login, so you can leave this function blank.
	*/
	/*function login() {
		$username= "mdiv0044";
		//if(isset($_SERVER['WEBAUTH_USER']))
		if(isset($username))
			{
			//Find user_id
			$user = $this->User->find('first', array('conditions' => array('User.username' => $username), 'fields' => array('User.id')));
			if(!empty($user))
				{
				$user_id = $user['User']['id'];
				$this->User->id = $user_id;
				$this->User->saveField('last_login', DboSource::expression('NOW()'));
				}
			else
				{
				//need to create this user
				$this->User->create();
				$this->request->data = array(
			    		'User' => array
			        		(
			        		'username' => $username,
			        		'password' => $username."_".$username,
			        		'last_login' => DboSource::expression('NOW()')
			        		//need to read these long-term
			        		//'first_name' => $first_name,
			        		//'last_name' => $last_name,
			        		)
			    		);
				$this->User->save($this->request->data);
				$user_id = $this->User->id;
				}
			$logged_in = $this->Auth->login($user_id);
			//redirect to the page you were trying to9o get to before Auth called.
			//print_r($this->Session);
			$this->autoRender = false;
			//$this->redirect(array('controller'=>'events', 'action'=>'index_by_date'));
			}
		}*/
	function logout() {
	$this->redirect($this->Auth->logout());
	}

	/*function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}*/

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$user = $this->User->read(null, $id);
		if ($this->request->is('requested')) {
			return $user;
			}
		else{
			$this->layout = 'ajax';
			$this->set('user', $user);		
			}
		
	}
	
	function add() {
		if (!empty($this->request->data)) {
			$this->User->create();
			$this->request->data['User']['barcode'] = Security::hash($this->request->data['User']['barcode']);
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'add'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}

	/*function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}*/
}
?>
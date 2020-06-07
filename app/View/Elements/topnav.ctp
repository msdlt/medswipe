<?php 
//$user = $this->requestAction('users/view/'.AuthComponent::user('id'));
//$user = AuthComponent::user('username');
//echo "Logged in as: ".$user['User']['username'];
echo "Logged in as: ".AuthComponent::user('username');
?>

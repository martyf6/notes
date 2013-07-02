<?php

define('INCLUDE_CHECK',true);
require 'lib.php';

if(isset($_POST['submit']) && $_POST['submit']=='LOG IN')
{
	// Checking whether the Login form has been submitted
	
	$err = array();
	// Will hold our errors
	
	
	if(!$_POST['username'] || !$_POST['password'])
		$err[] = 'All the fields must be filled in!';
	
	if(!count($err)) {
		$post_username = mysql_real_escape_string($_POST['username']);
		$post_password = mysql_real_escape_string($_POST['password']);
		$pw_hashed = sha1($post_password);
		$post_remember = (int) $_POST['rememberMe'];
		if (!login($post_username,$pw_hashed,$post_remember)) {
			$err[] = 'Wrong username and/or password!';
		} else {
			header("Location: http://notes.thejungleblog.com/home.php");
		}
	}
	
	// Save the error messages in the session
	if($err) $_SESSION['msg']['login-err'] = implode('<br />',$err);
}

?>
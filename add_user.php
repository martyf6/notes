<?php

define('INCLUDE_CHECK',true);
require 'functions.php';


if(isset($_POST['submit']) && $_POST['submit']=='REGISTER') {

	$username = $_POST['username'];
	$pw = $_POST['password'];
	$pw2 = $_POST['password2'];
	$email = $_POST['email'];

	// Will hold our errors
	$err = array();

	if ($pw == $pw2) {
		$pw_hashed = sha1($pw);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$err[] = "'".$email."' is not a valid email address.";
		} else if (is_null($username)) {
			$err[] = "A username must be supplied.";
		} else if (is_null($pw)) {
			$err[] = "A password must be supplied.";
		} else {
			list ($result, $err_msg) = register($username,$pw_hashed,$email);
			if ($result){
				// send email with confirmation link
				header("Location: http://notes.thejungleblog.com/login.php");
			} else {
				$err[] = $err_msg;
			}
		}
	} else {
		$err[] = "Passwords do not match.";
	}

	// Save the error messages in the session
	if($err) $_SESSION['msg']['register-err'] = implode('<br />',$err);
}
?>
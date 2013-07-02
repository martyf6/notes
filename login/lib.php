<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

/*
 for generic use:
- update the query commands to fit appropriate user database
- update the domain name for creating the session cookie
- if intending on using admin cookies - update the 2 lines for getting the user level
and setting the session variable for user level as well as 1 line in the logout function
- make sure that all pages calling this page has the appropriate 'INCLUDE_CHECK' variable set

note:
- cookie name: 'auth_key'
- session vars: 'user_id', 'user_name', 'user_lastactive', 'user_level'
*/

require_once 'db_fns.php';

function get_visitor_ip() {
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))	{
		return trim($_SERVER['HTTP_X_FORWARDED_FOR']);
	} else {
		return trim($_SERVER['REMOTE_ADDR']);
	}
}


function checkLogin() {
	if(!isset($_SESSION)) {
		session_start();
	};
	
	if(isset($_SESSION['user_name'])) {
		$_SESSION['user_lastactive'] = time();
		return true;
	} else {
		
		// Check that cookie is set
		if(isset($_COOKIE['auth_key'])) {
			$auth_key = $_COOKIE['auth_key'];
			
			// Select user from database where auth key matches (auth keys are unique)
			$conn = db_connect();
			$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE auth_key = ? LIMIT 1");
			$stmt->bind_param('s', $auth_key);
			if (!$stmt->execute()) return false;
			$result = $stmt->get_result();
			if($result) {
				while ($row = $result->fetch_object()) {
					
					// Go ahead and log in
					// Assign variables to session
					session_regenerate_id(true);
					$session_id = $row->id;
					$session_username = $row->name;
					
					// if we intend on having various user levels (admin, etc.) this is where we would do it.
					//$session_level = $u[user_level];
						
					$_SESSION['user_id'] = $session_id;
					//$_SESSION['user_level'] = $session_level;
					$_SESSION['user_name'] = $session_username;
					$stmt->close();
					return true;
				}
			} else {
				setcookie("auth_key", "", time() - 3600, "/", "notes.thejungleblog.com");
				return false;
			}
		}
	}
	return false;
}

function rand_string( $length ) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	$size = strlen( $chars );
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}

	return $str;
}

function session_encrypt($string) {
	$salt = 'welcometothejungle';
	return md5($salt . $string);
}

function login($username, $password, $remember = false) {
	if(!isset($_SESSION)) {
		session_start();
	}

	$conn = db_connect();
	$stmt = $conn->prepare("SELECT id,auth_key FROM users WHERE name = ? AND password = ? LIMIT 1");
	$stmt->bind_param('ss', $username, $password);
	if (!$stmt->execute()) return false;
	$result = $stmt->get_result();
	
	// If there are no matches then the username and password do not match
	if(!$result) return false;
	else {
		while ($row = $result->fetch_object()) {
			
			// Check if user wants account to be saved in cookie
			if($remember && ($row->auth_key == 0)) {
				
				$stmt->close();
								
				// Generate new auth key for each log in (so old auth key can not be used multiple times in case of cookie hijacking)
				$cookie_auth= rand_string(15) . $username . $password;
				$auth_key = session_encrypt($cookie_auth);
				$stmt = $conn->prepare("UPDATE users SET auth_key = ? WHERE name = ?");
				$stmt->bind_param('ss', $auth_key, $username);
				if (!$stmt->execute()) return false;
				setcookie("auth_key", $auth_key, time() + 60 * 60 * 24 * 7, "/", "notes.thejungleblog.com", false, true);
			} else if ($row->auth_key != 0) {
				
				// Use the current auth key to create a new cookie for the new ip
				$auth_key = $row->auth_key;
				$stmt->close();
				
				setcookie("auth_key", $auth_key, time() + 60 * 60 * 24 * 7, "/", "notes.thejungleblog.com", false, true);
			}
			
			// Assign variables to session
			session_regenerate_id(true);
			$session_id = $row->id;
			$session_username = $username;
			
			// if we intend on having various user levels (admin, etc.) this is where we would do it.
			//$session_level = $u[user_level];

			$_SESSION['user_id'] = $session_id;
			//$_SESSION['user_level'] = $session_level;
			$_SESSION['user_name'] = $session_username;
			$_SESSION['user_lastactive'] = time();
			return true;
		}
	}
}

function initiate() {
	if(!isset($_SESSION)) {
		session_start();
	}

	$logged_in = false;
	if(isset($_SESSION['user_name'])) {
		$logged_in = true;
	}

	// Check that cookie is set
	if(isset($_COOKIE['auth_key'])) {
		$auth_key = $_COOKIE['auth_key'];

		if($logged_in === false) {
			
			// Select user from database where auth key matches (auth keys are unique)
			$conn = db_connect();
			$stmt = $conn->prepare("SELECT name, password FROM users WHERE auth_key = ? LIMIT 1");
			$stmt->bind_param('s', $auth_key);
			if (!$stmt->execute()) return;
			$result = $stmt->get_result();
			if(!$result) {
				
				// If auth key does not belong to a user delete the cookie
				setcookie("auth_key", "", time() - 3600, "/", "notes.thejungleblog.com");
			} else {
				while ($row = $result->fetch_object()) {
					
					// Go ahead and log in
					login($row->name, $row->password, true);
				}
			}
			$stmt->close();
		} else {
			//setcookie("auth_key", "", time() - 3600, "/", "notes.thejungleblog.com");
		}
	}
}

function logout() {
	
	// Need to delete auth key from database so cookie can no longer be used
	$username = $_SESSION['user_name'];
	
	setcookie("auth_key", "", time() - 3600, "/", "notes.thejungleblog.com");
	//$auth_query = mysql_query("UPDATE users SET auth_key = 0 WHERE name = '" . $username . "'");
	
	// If auth key is deleted from database proceed to unset all session variables
	if (true) {
		unset($_SESSION['user_id']);
		//unset($_SESSION['user_level']);
		unset($_SESSION['user_name']);
		unset($_SESSION['user_lastactive']);
		session_unset();
		session_destroy();
		return true;
	} else {
		return false;
	}
}


// Check if session is still active and if it keep it alive
function keepalive() {
	
	// If session is supposed to be saved or remembered ignore following code
	if(!isset($_COOKIE['auth_key'])) {
		$oldtime = $_SESSION['user_lastactive'];
		if(!empty($oldtime)) {
			$currenttime = time();
			// This is equivalent to 30 minutes
			$timeoutlength = 5 * 600;
			if($oldtime + $timeoutlength >= $currenttime) {
				
				// Set new user last active time
				$_SESSION['user_lastactive'] = $currenttime;
			} else {
				
				// If session has been inactive too long logout
				logout();
			}
		}
	}
}

?>

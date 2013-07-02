<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

require_once 'db_fns.php';
require_once "login/lib.php";
require_once 'logfile.php';

// Logfile for simple debugging output and record keeping
$lf = new logfile();

function register($un,$pw,$email) {
	
	// Connect to the database
	$conn = db_connect();
	
	// Check if user with email already exists
	$email_query = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 0,1");
	$email_query->bind_param('s', $email);
	if (!$email_query->execute()) return array (false, "Registration failed. Please try again later.");
	$result = $email_query->get_result();
	if ($result && $result->num_rows == 0) {
		
		// Check if the username is already taken
		$user_query = $conn->prepare("SELECT * FROM users WHERE name = ? LIMIT 0,1");
		$user_query->bind_param('s', $un);
		if (!$user_query->execute()) return array (false, "Registration failed. Please try again later.");
		$result = $user_query->get_result();
		if ($result && $result->num_rows == 0) {
			
			// If no user with email already exists, add one
			$stmt = $conn->prepare("INSERT INTO users (name,password,email) VALUES (?, ?, ?)");
			$stmt->bind_param('sss', $un, $pw, $email);
			if ($stmt->execute()) return array (true, "");
			else return array (false, "Registration failed. Please try again later.");
			
		} else {
			$user_query->close();
			return array (false, "A user is already listed with the username '$un'.");
		}
	} else {
		$email_query->close();
		return array (false, "A user is already listed with the email address '$email'.");
	}
}

function get_note_names() {
	$note_names = array();
	
	// Make sure a user is logged in
	if (!checkLogin()) return $note_names;
	$uid = $_SESSION['user_id'];
	
	$conn = db_connect();
	
	$stmt = $conn->prepare("SELECT title, id FROM entries WHERE author = ? ORDER BY location");
	$stmt->bind_param('i', $uid);
	if (!$stmt->execute()) return $note_names;
	$result = $stmt->get_result();
	while($row = $result->fetch_object()) {
		$note_names[] = array(
				"name" => $row->title,
				"id"   => $row->id
				);
	}
	return $note_names;
}

function get_note($note_id) {

	// Make sure a user is logged in
	if (!checkLogin()) return "";
	$uid = $_SESSION['user_id'];

	$conn = db_connect();
	$stmt = $conn->prepare("SELECT entry FROM entries WHERE author = ? AND id = ?");
	$stmt->bind_param('ii', $uid, $note_id);
	if (!$stmt->execute()) return "";
	$result = $stmt->get_result();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_object()) {
			$stmt->close();
			return $row->entry;
		}
	} else {

		// The user has no note with this id
		return "";
	}
}

// TODO: Return error messages for failed updates
function update_note($note_id, $note_name, $new_title, $note_contents) {
	
	// Make sure a user is logged in
	if (!checkLogin()) return false;
	$uid = $_SESSION['user_id'];
	
	$conn = db_connect();
	
	// Check if the title has been updated
	if ($note_name == $new_title) {
		
		// Update the existing note
		$stmt = $conn->prepare("UPDATE entries SET entry = ? WHERE author = ? AND id = ?");
		$stmt->bind_param('sii', $note_contents, $uid, $note_id);
		if ($stmt->execute()) return true;
		else return false;
	} else {

		// Changing note name... make sure the new note name is available
		$stmt = $conn->prepare("SELECT title FROM entries WHERE author = ? AND title = ? AND id != ? LIMIT 0,1");
		$stmt->bind_param('isi', $uid, $new_title, $note_id);
		if (!$stmt->execute()) return false;
		$result = $stmt->get_result();
		if($result && $result->num_rows > 0) {
			$stmt->close();
				
			// New title taken, fail to save
			return false;
		} else {
			
			// Update the existing note including the new note title
			$stmt = $conn->prepare("UPDATE entries SET entry = ?, title = ? WHERE author = ? AND id = ?");
			$stmt->bind_param('ssii', $note_contents, $new_title, $uid, $note_id);
			if ($stmt->execute()) return true;
			else return false;
		}
	}
}

function update_note_order($note_id, $note_location) {
	// Make sure a user is logged in
	if (!checkLogin()) return false;
	$uid = $_SESSION['user_id'];
	
	$conn = db_connect();
	
	// Update the existing note including the new note index
	$stmt = $conn->prepare("UPDATE entries SET location = ? WHERE author = ? AND id = ?");
	$stmt->bind_param('iii', $note_location, $uid, $note_id);
	if ($stmt->execute()) return true;
	else return false;
}

function new_note($note_name, $note_contents = "") {
	// Make sure a user is logged in
	if (!checkLogin()) return -1;
	$uid = $_SESSION['user_id'];
	
	$conn = db_connect();
	// make sure the new note name is available
	$stmt = $conn->prepare("SELECT title FROM entries WHERE author = ? AND title = ?");
	$stmt->bind_param('is', $uid, $note_name);
	if (!$stmt->execute()) return -1;
	$result = $stmt->get_result();
	if($result && $result->num_rows > 0) {
		$stmt->close();
		
		// New title taken, fail to save
		return -1;
	} else {
		
		// Add the new note
		$stmt = $conn->prepare("INSERT INTO entries (title,entry,author) VALUES (?, ?, ?)");
		$stmt->bind_param('ssi', $note_name, $note_contents, $uid);
		if ($stmt->execute()) {
			$new_note_id = $stmt->insert_id;
			return $new_note_id;
		}
		else return -1;
	}
}

function delete_note($note_id) {
	// Make sure a user is logged in
	if (!checkLogin()) return false;
	$uid = $_SESSION['user_id'];
	
	$conn = db_connect();
	$stmt = $conn->prepare("DELETE FROM entries WHERE author = ? AND id = ?");
	$stmt->bind_param('ii', $uid, $note_id);
	if ($stmt->execute()) return true;
	else return false;
}

/* 
 * place-holder for incorporating email functionality
 */
function send_mail($from,$to,$subject,$body) {
	$headers = '';
	$headers .= "From: $from\n";
	$headers .= "Reply-to: $from\n";
	$headers .= "Return-Path: $from\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";

	mail($to,$subject,$body,$headers);
}

?>

<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser) {
	$note_order = $_POST['neworder'];
	foreach ($note_order as $note) {
		$note_id = $note["id"];
		$note_location = $note["location"];
		$success = update_note_order($note_id, $note_location);
	}
} else {
	echo false;
}
?>
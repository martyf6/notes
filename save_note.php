<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser && isset($_POST['id']) && isset($_POST['noteName']) && isset($_POST['newTitle']) && isset($_POST['entry'])) {
	$id = $_POST['id'];
	$noteName = $_POST['noteName'];
	$newTitle = $_POST['newTitle'];
	$entry = $_POST['entry'];
	$result = update_note($id,$noteName,$newTitle,$entry);
	$response = $result ? true : false;
	echo $result;
} else {
	echo false;
}
?>

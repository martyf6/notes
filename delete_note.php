<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser) {
	$noteName = $_POST['note'];
	$result = delete_note($noteName);
	echo $result;
} else {
	echo false;
}
?>
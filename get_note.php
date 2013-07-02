<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser) {
	$note_id = $_GET['note'];
	$contents = get_note($note_id);
	echo $contents;
} else {
	echo false;
}
?>
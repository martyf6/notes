<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser) {
	$noteName = $_POST['note'];
	$noteContents = $_POST['entry'];
	$result = new_note($noteName,$noteContents);
	echo $result;
} else {
	echo false;
}
?>
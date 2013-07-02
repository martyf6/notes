<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
$isUser = checkLogin();
if ($isUser) {
	$contents = get_note_names();
	echo $contents;
} else {
	echo false;
}
?>
<?php
define('INCLUDE_CHECK',true);
require 'functions.php';
initiate();	
if (!checkLogin() || (isset($_GET['logoff']) && logout())) {
	header("Location: http://notes.thejungleblog.com/login.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quick Notes</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css"/>
<link rel="stylesheet" href="css/style.css" type="text/css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="js/notes.js"></script>
</head>
<body>
<div id="title-bar">
<div id="title-bar-title">
Quick Notes
</div>
<div id="title-bar-menu">
<a href="?logoff">Log off</a>
</div>
</div>
<div id="content">
<div class="left">
<div id="nav-holder-sub">
	<div class="title-top">
	Notes:
	</div>
	
	<div id="nav-sub">
<ul id="note-names">
<?php
$note_names = get_note_names();
foreach ($note_names as $note) {
	$name = $note['name'];
	$id = $note['id'];
    echo "<li class='notename' id='".$id."' title='".$name."'><a href='#'>".$name."</a></li>";
}
?>
<li><a id="new-note" href="#">+</a></li>
</ul>
</div>
</div>
</div>
<div id="note-data">
<input id="note-title" type="text" size=35 value="Enter new note title here." />
<br />
<textarea id="note-area" rows=35>
Type your note here.
</textarea>
<br />
<text id="note-area-info">(Ctrl+S and tabs are enabled in Chrome)</text>
<br /><br />
<input type="button" id="save-button" value="Save" />&nbsp;
<input type="button" id="delete-button" value="Delete" />
</div>
</div>
</body>
</html>
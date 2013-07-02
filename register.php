<?php include "add_user.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Register for Quick Notes</title>    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

<div id="title-bar">
<div id="title-bar-title">
Quick Notes
</div>
<div id="title-bar-menu">
Not a member? &nbsp;<a href="/register.php">Register</a>
</div>
</div>

<!-- Register Panel -->
<div id="register-panel">
	<form action="" method="post" id="register-form">     
	               
        	<?php
				if(isset($_SESSION) && $_SESSION['msg']['register-err']) {
					echo '<div class="err">'.$_SESSION['msg']['register-err'].'</div>';
					unset($_SESSION['msg']['register-err']);
				}
 			?>
			<fieldset>
				<p>
					<label for="username">username</label> 
					<input type="text" name="username" id="register-username" class="round full-width-input" autofocus="" />
				</p>
				<p>
					<label for="password">password</label> 
					<input type="password" name="password" id="register-password" class="round full-width-input" />
				
				</p>
				<p>
					<label for="password2">confirm password</label> 
					<input type="password" name="password2" id="register-password2" class="round full-width-input" />
				
				</p>
				<p>
					<label for="email">email</label> 
					<input type="text" name="email" id="register-email" class="round full-width-input" autofocus="" />
				</p>
				<!-- <a href="dashboard.html" class="button round blue image-right ic-right-arrow">LOG IN</a> -->
				<input type="submit" name="submit" value="REGISTER" class="button round blue image-right ic-right-arrow" />
			</fieldset>
		</form>
</div> <!--loginpanel -->
</body>
</html>
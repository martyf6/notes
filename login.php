<?php include "login/login.php";
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login to Quick Notes</title>    
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

<!-- Login Panel -->
<div id="login-panel">
	<form action="" method="post" id="login-form">                    
        	<?php
			if(isset($_SESSION) && !isset($_SESSION['id']) && $_SESSION['msg']['login-err']) {
				echo '<div class="err">'.$_SESSION['msg']['login-err'].'</div>';
				unset($_SESSION['msg']['login-err']);
			}
		?>
		<!-- 
		<label class="grey" for="username">Username:</label>
		<input class="field" type="text" name="username" id="username" value="" size="23" />
		<label class="grey" for="password">Password:</label>
		<input class="field" type="password" name="password" id="password" size="23" />
	        <label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;Remember me</label>
        	<div class="clear"></div>
		<input type="submit" name="submit" value="Login" class="bt_login" />
		-->
			<fieldset>
				<p>
					<label for="username">username</label> 
					<input type="text" name="username" id="login-username" class="round full-width-input" autofocus="" />
				</p>
				<p>
					<label for="password">password</label> 
					<input type="password" name="password" id="login-password" class="round full-width-input" />
				
				</p>
				<p>
					<label class="remember"> 
					<input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" />
							remember me 
					</label>
				</p>
				<p>
					<a href="#">forgot password</a>
				</p>
				<!-- <a href="dashboard.html" class="button round blue image-right ic-right-arrow">LOG IN</a> -->
				<input type="submit" name="submit" value="LOG IN" class="button round blue image-right ic-right-arrow" />
			</fieldset>
		</form>
</div> <!--loginpanel -->
</body>
</html>
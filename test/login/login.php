<?php
require_once("../../../includes/initialize.php");

if($session->is_logged_in()) {
  redirect_to("../player/index.php");
}

// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) { // Form has been submitted.

  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  
  // Check database to see if username/password exist.
	$found_user = User::authenticate($username, $password);
	
  if ($found_user) {
  	$message = "";
    $session->login($found_user);
    redirect_to("index.php");
  } else {
    // username/password combo was not found in the database
    $message = "Username/password combination incorrect.";
  }
  
} else { // Form has not been submitted.
  $username = "";
  $password = "";
}

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!-- meta info -->
<meta charset="UTF-8">
<meta name="keywords" content="the discovery app login">
<meta name="description" content="login for the discovery app website">
<meta name="author" content="Matthew Bridgeman">
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- css -->

<!-- style -->
<link href="css/style.css" rel="stylesheet" type="text/css">

<!-- javascript -->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<!-- further HTML5 and new technologies fixes-->
<script src=../player/js/modernizr-1.6.min.js" type="text/javascript"></script>

<title>The Discovery App | login</title>
</head>
<body>
<header id="site login">
		
		<?php if ($message != "") { ?>
		<div id="message-wrapper">
		<div id="message">
		<?php echo output_message($message . "  " . $username); ?>
		</div>
		</div>
		<?php } ?>
		<div id="container">
		<h2 class="logo">Login</h2>
		<form action="login.php" method="post">
		  
		      <label for="username-input" id="username-label">Username:</label>
		      
		        <input class="text-box" type="text" id="username-input" name="username" maxlength="30" value="<?php echo htmlentities($username); ?>" />
		      
		    
		      <label for="password-input" id="password-label">Password:</label>
		      
		        <input class="text-box" type="password" id="password-input" name="password" maxlength="30" value="<?php echo htmlentities($password); ?>" />
		     
		    
		        <input id="submit" type="submit" name="submit" value="Login" />
		    
		</form>
		
		</div>
</header>
</body>
</html>


<?php
require_once('../../includes/initialize.php');
if (isset($_GET['anon'])) {
	header("Location: ../index.php?anon=1&done=1");
}
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<script>
$(document).ready(function() {
	window.fbAsyncInit = function() {
	    FB.init({appId: '154550127940245', status: true, cookie: true,
	             xfbml: true});
	    FB.getLoginStatus(function(response) {
  		  if (!response.session) {
  				//user is not connected
  			top.location.href='../';
  		  } else {
			//they are logged in
  			FB.logout(function(response) {
  				// user is now logged out
  				top.location.href='../?message=thankyou&done=1';
  			});
  		  }
  		});
	  };
	  (function() {
  	    var e = document.createElement('script'); e.async = true;
  	    e.src = document.location.protocol +
  	      '//connect.facebook.net/en_US/all.js';
  	    document.getElementById('fb-root').appendChild(e);
  	  }());
});
</script>
</head>
<body>
<div id="fb-root"></div>
</body>
</html>
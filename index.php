<?php
// Awesome Facebook Application
//
// Name: Discovery
//
require_once("../includes/initialize.php");

$message = false;

//add into an object
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
$pageURL = curPageURL();
$array;
if (count($array = explode("www", $pageURL)) > 1) {
} else {
	header("Location: http://www.".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
}
function getMessage () {
	if ($_GET['message'] == "thankyou") {
		$message = true;
	}
}

require_once 'facebook-php-sdk/src/facebook.php';

$app_id = 154550127940245;
$app_secret = "08751756393722bcc0ec3ad06a20d12f";
$my_url = curPageURL(); 
$code = $_REQUEST["code"];
$scope = "email,user_likes";
// Create our Application instance.
$facebook = new Facebook(array(
  'appId' => $app_id,
  'secret' => $app_secret,
  'cookie' => true,
));

    if(empty($code)) {
		$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
		. $app_id . "&scope=". $scope ."&redirect_uri=http://www.thediscoveryapp.com/";
		$redirect = "" . $dialog_url . "";
	}   

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>The Discovery App v2.9 | Music Discovery</title>
<meta name="keywords" content="music, discovery, app, soundcloud api, matt bridgeman">
<meta name="description" content="The Discovery Application is a music discovery service based on the soundcloud API">
<meta name="author" content="Matthew Bridgeman">
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- main styling -->
<link href="css/style.css" rel="stylesheet" type="text/css">
<!-- fixes for html5 for older browsers -->
<link href="css/html5-fixes.css" rel="stylesheet" type="text/css">
<link REL="SHORTCUT ICON" HREF="css/images/favicon.ico">

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var facebookBtn = $('#facebookBtn');
	var redirect = '<?php echo $dialog_url; ?>';
	window.fbAsyncInit = function() {
	    FB.init({appId: '154550127940245', status: true, cookie: true,
	             xfbml: true});
	    FB.getLoginStatus(function(response) {
  		  if (!response.session) {
  				//user is not connected
  		  } else {
			redirect = "player/";
			<?php if (!isset($_GET['done'])) { ?>
			top.location.href=redirect;
			<?php } ?>
  		  }
  		});
	  };
	  (function() {
  	    var e = document.createElement('script'); e.async = true;
  	    e.src = document.location.protocol +
  	      '//connect.facebook.net/en_US/all.js';
  	    document.getElementById('fb-root').appendChild(e);
  	  }());
	  facebookBtn.click(function(e) {
			e.preventDefault();
			facebookCheck();
		});
		function facebookCheck () {
			top.location.href=redirect;
			<?php //echo $redirect; ?>
		}
});
</script>
<script type="text/javascript">    
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-22230288-1']);
	_gaq.push(['_trackPageview']);
	(function() {     var ga = document.createElement('script');
	ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
</head>
<body>
<div class="container">
<header id="mainHead">
	<h1>The Discovery Application</h1>
	<aside><p><a href="http://www.thediscoveryapp.com">Home</a> &#47; <a href="http://www.thediscoveryapp.com/blog">Dev Blog</a></p></aside>
</header>
<section id="mainSection">
<h1>Welcome to<br/>the Discovery App v2.9</h1>
<p>The Discovery App is a music streaming service designed to provide innovative ways to interact with new and unheard music.</p>
<p>This is Beta release version 2.9</p>
<p>It was released early to show you just what's instore and to get valuable feedback.</p>
<p>Login via Facebook below and have a go!</p>
<a href="#" id="facebookBtn" title="link-to-authorize-discovery">Login via Facebook</a>
<a href="/player?anon=1" id="anonBtn" title="link-to-authorize-discovery">Login anonymously</a>
</section>
<aside id="mainAside">

</aside>
</div>
<div id="fb-root"></div>
</body>
</html>
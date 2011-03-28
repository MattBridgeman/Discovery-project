<?php
require_once('../../includes/initialize.php');

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

require_once '../facebook-php-sdk/src/facebook.php';

$app_id = 154550127940245;
$app_secret = "08751756393722bcc0ec3ad06a20d12f";
$my_url = curPageURL(); 
$code = $_REQUEST["code"];

// Create our Application instance.
$facebook = new Facebook(array(
  'appId' => $app_id,
  'secret' => $app_secret,
  'cookie' => true,
));

if(empty($code)) {
	$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
	. $app_id . "&redirect_uri=" . urlencode($my_url."player/");
	$redirect = "" . $dialog_url . "";
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!-- meta info -->
<meta charset="UTF-8">
<meta name="keywords" content="discovery, music, streaming, player, soundcloud">
<meta name="description" content="The Discovery App Player, this is the player for ">
<meta name="author" content="Matthew Bridgeman">
<title>The Discovery App | Player</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- css -->
<!-- fixes for html5 for older browsers -->
<link href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" type="text/css">
<!-- The 1140px Grid -->
<link rel="stylesheet" href="css/1140/1140.css" type="text/css" media="screen" />

<!--[if lte IE 9]>
<link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" />
<![endif]-->

<!-- Make minor type adjustments for 1024 monitors -->
<link rel="stylesheet" href="css/1140/smallerscreen.css" media="only screen and (max-width: 1023px)" />
<!-- Resets grid for mobile -->
<link rel="stylesheet" href="css/1140/mobile.css" media="handheld, only screen and (max-width: 767px)" />
<!-- Put your layout here -->
<link rel="stylesheet" href="css/1140/layout.css" type="text/css" media="screen" />
<!-- style -->
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/sc/sc-player-minimal.css" rel="stylesheet" type="text/css">
<link REL="SHORTCUT ICON" HREF="../css/images/favicon.ico">
<!-- javascript -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<!-- further HTML5 and new technologies fixes-->
<script src="js/modernizr-1.6.min.js" type="text/javascript"></script>
<!-- jQuery UI elements -->
<script src="js/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>
<!-- soundcloud controls -->
<script type="text/javascript" src="js/soundcloud.player.api.js"></script>

<script src="js/jquery-soundcloud-controls.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	var redirect = '<?php echo $dialog_url; ?>';
	var name;
	var fb_id;
	var email;
	window.fbAsyncInit = function() {
	    FB.init({appId: '154550127940245', status: true, cookie: true,
	             xfbml: true});
	    FB.getLoginStatus(function(response) {
  		  if (!response.session) {
  			//user is not connected
  			top.location.href=redirect;
  		  } else {
			//they are logged in
			fb_id = response.session.uid;
  			FB.api('/me', function(response) {
  	  			console.log(response);
  				name = response.name;
  				email = response.email;
  			});

  			var dataString = 'fb_id=' + fb_id + '&name=' + name + '&email =' + email;
	        
		     $.ajax({
			      type: "POST",
			      url: "bin/process.php",
			      data: dataString,
			      success: function(msg) {
			    	 console.log(msg);
		          }
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
<script src="js/sc-player.js" type="text/javascript"></script>

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
<div class="row">
	<div id="player-object" class="twelvecol">
	  <!-- <object height="81" width="100%" id="discoveryPlayer" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
	    <param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fsoundcloud.com%2Fspawn%2Fstonedeep&enable_api=true&object_id=discoveryPlayer"></param>
	    <param name="allowscriptaccess" value="always"></param>
	    <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fsoundcloud.com%2Fspawn%2Fstonedeep&enable_api=true&object_id=discoveryPlayer" type="application/x-shockwave-flash" width="100%" name="discoveryPlayer"></embed>
	  </object> -->
	  
	</div>
	
	  <div class="clear"></div>
	  <!-- <div id="header-wrapper">
	  		<header id="main-header">
	  			<div class="logo-wrap" id="logo">
	  				<a class="home_link" id="logo-link" href="#">Discovery</a>
	  			</div>
	  			<div class="sixcol player-box" id="searching">
	  				<form action="#" method="post">
						<label class="" id="search-label" for="search-input">search</label>
						<input role="search" type="text" placeholder="Search" name="search" id="search-input">
						<input class="submit"  type="submit" name="submit" id="submit" value="Discover">
					<label class="error" id="search_error">Please enter a search</label>
					</form>
	  			</div>
	  			<div class="optional-functions">
	  			<div class="player-box"><a id="logout-link" href="logout.php?logout=true">logout</a></div>
	  			</div>
	  			<div class="clear"></div>
	  		</header>
	  		<div class="clear"></div>
	  </div>  -->
	  <div class="clear"></div>
	  
	  <div class="main-wrapper">
	<section id="main-menu" class="threecol">
		<div class="white-wrapper white-padding">
		<nav id="main-navigation" class="menu-inner">
			<ul>	
					<li><a class="home_link" href="#">home</a></li>
					<li><a href="#" id="profile-btn">profile</a></li>
					
					<li><span>my music</span>
						<ul>
						<li><a href="#">my tracks</a></li>
						<li><a href="#">favourites</a></li>
						<li><a href="#">discovered</a></li>
						</ul>
					</li>
					
					<li><a href="#">now playing</a></li>
					
					<li><span>searches</span>
						<ul id="ul-searches">
						<li><a href="#">dystopia</a></li>
						<li><a href="#">favourites</a></li>
						<li><a href="#">discovered</a></li>
						</ul>
					</li>
			</ul>
		</nav>
		<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</section>
	<section id="main-content" class="ninecol last menu-inner">
	<div class="white-wrapper">
	
	<div id="the-content">
<div id="content-header"><h1>Home</h1></div>
<div id="main-form" class="wrap">
<form action="#" method="post">
						<label class="" id="main-search-label" for="main-search-input">search</label>
						<input class="" type="text" placeholder="Search" name="search" id="main-search-input">
						
						<input class="submit"  type="submit" name="submit" id="main-submit" value="Discover">
					<label class="main-error" id="search_error">Please enter a search</label>
	</form>				
<h2></h2>
</div>

<ul class="new"></ul>
</div>
	</div>
	</section>
	
	<div class="clear"></div>
	<!-- close up the main wrapper -->
	</div>
	  <footer id="player-container">
	  <div id="player-wrapper">
	  <div id="play-functions">
	  <div class="box player-box">
	  	<a id="prev-btn" class="btn" href="#">prev-btn</a>
		<!-- <a class="sc-play" href="#play">New track</a> -->
		<a id="play-pause" href="http://soundcloud.com/spawn/dystopia" class="sc-player">My new dub track</a>
		<a id="next-btn" class="btn" href="#">next-btn</a>
		<div id="volumeContainer">
		<div id="volumeSliderContainer">
		<div id="volumeSlider"></div>
		</div>
		<a id="volume-btn" class="btn" href="#">volume</a>
		</div>
		</div>
		
	  </div>
	  <div class="sixcol" id="playSliderContainer"><div id="playSlider"></div></div>
	   <div class="optional-functions">
		  <div class="player-box">
		  <a id="repeat-btn" class="off" href="#">repeat</a>
		  </div>
		<div class="clear"></div>
		</div>
	  </div>
	  <div class="clear"></div>
	  </footer>
	  
</div>
</div>
<div id="fb-root"></div>
</body>
</html>
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
$anon = "";
// Create our Application instance.
$facebook = new Facebook(array(
  'appId' => $app_id,
  'secret' => $app_secret,
  'cookie' => true,
));
if(isset($_GET['anon'])) {
	$anon = $_GET['anon'];
} else {
	if(empty($code)) {
		$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
		. $app_id . "&redirect_uri=" . urlencode($my_url."player/");
		$redirect = "" . $dialog_url . "";
	}
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!-- meta info -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="keywords" content="discovery, music, streaming, player, soundcloud">
<meta name="description" content="The Discovery App Player, this is the player the helps you discovery new and unheard music">

<meta name="author" content="Matthew Bridgeman">
<title>The Discovery App | Player</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- css -->
<!-- fixes for html5 for older browsers -->
<link href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/sc/sc-player-minimal.css" rel="stylesheet" type="text/css">
<!-- 1140px Grid styles for IE -->
<!--[if lte IE 9]><link rel="stylesheet" href="css/1140_2/css/ie.css" type="text/css" media="screen" /><![endif]-->

<!-- The 1140px Grid - http://cssgrid.net/ -->
<link rel="stylesheet" href="css/1140_2/css/1140.css" type="text/css" media="screen" />

<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
<script type="text/javascript" src="css/1140_2/js/css3-mediaqueries.js"></script>

<link REL="SHORTCUT ICON" HREF="../css/images/favicon.ico">
<!-- javascript -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<!-- further HTML5 and new technologies fixes-->
<script src="js/modernizr-1.7.min.js" type="text/javascript"></script>
<!-- jQuery UI elements -->
<script src="js/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
	var redirect = '<?php echo $dialog_url; ?>';
	var name;
	var fb_id;
	var email;
	var first_name;
	var last_name;
	var user_likes;
	var dataString;
	var urlString = "";
	var info;
	var first_name = $('input#first-name-input');
	var last_name = $('input#last-name-input');
	var email = $('input#email-input');
	var nickname = $('input#nickname-input');
	var account_submit = $('input#account-submit');
	var windowBox = $('#window');
	var winHeight = $('body').height();
	 windowBox.css({
		 "height" : winHeight
   });
	var window_head = $('#window-head');
	var window_body = $('#window-body');
	var window_a = $('#window-a');
	windowBox.hide();
	var inputs = new Array();
	var inputField = function (inputBox) {
		
		this.inputBox = inputBox;
		
	}
	var likes = new Array();
	var responses = new Array();
	var recommendations = new Object();
	var recom_image = $('li.recom-image');
	var moreInfo = $('a.moreInfo-a');
	//$('ul.subFM').live().hide();
	//.live('mouseover mouseout', function(event) {
	
	$('a.moreInfo-a').live('click', function(e) {
		
		var amt = "-=159px";
		e.preventDefault();
		  $(this).parent().parent().animate({
		  "margin-top" : amt
		  }, 'fast', function() {
		    // Animation complete.
			  amt = "+=159px";
		  });
		  $(this).html("Less Info");
		  $(this).removeClass('moreInfo-a').addClass('lessInfo-a');
	});
	$('a.lessInfo-a').live('click', function(e) {
			
			var amt = "+=159px";
			e.preventDefault();
			  $(this).parent().parent().animate({
			  "margin-top" : amt
			  }, 'fast', function() {
			    // Animation complete.
			  });
			  $(this).html("More Info");
			  $(this).removeClass('lessInfo-a').addClass('moreInfo-a');
		});
	recom_image.live("myCustomEvent", function(e, myName, myValue) {
		$(this).children('ul').hide();
		$(this).hover( function () {
		      $(this).children('ul').fadeIn('fast');
		    }, 
		    function () {
		    	$(this).children('ul').fadeOut('fast');
		    });
	});
	var nameField = new inputField(nickname);
	var emailField = new inputField(email);
	var firstField = new inputField(first_name);
	var lastField = new inputField(last_name);
	inputs.push(nameField);
	inputs.push(emailField);
	inputs.push(firstField);
	inputs.push(lastField);
	<?php if ($anon == "") { ?>
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
			dataString = 'fb_id=' + fb_id;
  			FB.api('/me', function(response) {
  	  			
  				name = response.name;
  				
  				email = response.email;
  				first_name = response.first_name;
  				last_name = response.last_name;
  				dataString += '&name=' + name + '&email=' + email + '&last_name=' + last_name + '&first_name=' + first_name;
  				
				//only perform when variables have been collected
  				$.ajax({
  			      type: "POST",
  			      url: "bin/process.php",
  			      data: "?callback=?&"+dataString,
  			      success: function(msg) {
  			    	 console.log(msg);
  			    	 info = msg;
  			    	loop_info();
  		          }
  		     });
  			  
  	  		});
  			FB.api('/me/likes', function(response) {
  	  			
  				for ( keyVar in response) {	
					for ( keyVars in response[keyVar]) {
						
						if (response[keyVar][keyVars].category == "Musician/band") {
							likes.push(response[keyVar][keyVars].name);
						}
					}
  				}
				do_likes();
  			});
  			function do_likes() {
	  			dataString = "&likes=";
	  			for (var i = 0; i < likes.length; i++) {
		  			dataString += likes[i];
		  			if (0 < i < likes.length) {
						dataString += ",";
		  			}
	  			}
		  		 	
		  		 	urlString = "bin/process.php?callback=?"+dataString+"";
		  			//only perform when variables have been collected
		  			//console.log(urlString);
		  			$.getJSON(urlString,{}, function(data2) {
		  			
		  				if (data2 == "") { 
		  					//alert("no data");
		  				} else {
		  					//alert("data");
		  					$('div.ajaxLoading').hide();
		  					recommendations = data2;
		  					var col = "threecol";
		  					$('body').find('ul.recom').empty();
		  				for (var i = 0, l = data2.length; i < l; i++) {
			  				if (i < 16) {
			  					if ((i-3)%4 == 0) {
									col = "threecol last";
			  					} else {
									col = "threecol";
			  					}
		  						//console.log(data2[i]['image']);//image
			  						//data2[i].name.replace("%27", "\'");
			  						
		  							$('body').find('ul.recom').append('<ul class="'+col+'"><li id="recom-img'+i+'" class="recom-image"><div class="artist-pic"><div style="height:200px;background: url(\''+data2[i].image+'\');"></div></div><ul class="subFM"><li class="moreInfo"><a class="moreInfo-a" href="#">More Info</a></li><li class="subSearch '+i+'"><a class="artist-search" href="'+data2[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data2[i].name+'">similar artists</a></li><li class="subFav '+i+'">Favourite</li></ul></li><li class="recom-info"><a href="#">'+data2[i].name+'</a></li></ul>');
		  						
			  				}
				  		}
		  				
						//
		  				}
		  			});
	  				
  			}
  			//last.fm recommendations
  			$('a.similar-search').live('click', function(e) {
  				e.preventDefault();
	  			dataString = "&similar=";
	  			$('ul.recom').fadeOut();
	  			$('ul.others').fadeOut();
	  			$('body').find('div.ajaxLoading').show();
	  			search = $(this).attr('href');
	  			console.log(search);
	  			dataString+=search;
		  		 	urlString = "bin/process.php?callback=?"+dataString+"";
		  			//only perform when variables have been collected
		  			//console.log(urlString);
		  			$.getJSON(urlString,{}, function(data3) {
		  			
		  				if (data3 == "") {
		  				} else {
		  					var col = "threecol";
		  					$('ul#others').empty();
		  				for (var i = 0, l = data3.length; i < l; i++) {
			  				if (i < 16) {
			  					if ((i-3)%4 == 0) {
									col = "threecol last";
			  					} else {
									col = "threecol";
			  					}
		  						//console.log(data3[i]['image']);//image
			  						//data3[i].name.replace("%27", "\'");
			  						
		  							$('ul#others').append('<ul class="'+col+'"><li id="recom-img'+i+'" class="recom-image"><div class="artist-pic"><div style="height:200px;background: url(\''+data3[i].image+'\');"></div></div><ul class="subFM"><li class="moreInfo"><a class="moreInfo-a" href="#">More Info</a></li><li class="subSearch '+i+'"><a class="artist-search" href="'+data3[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data3[i].name+'">similar artists</a></li><li class="subFav '+i+'">Favourite</li></ul></li><li class="recom-info"><a href="#">'+data3[i].name+'</a></li></ul>');
			  				}
			  				
				  		}

	  					$('div.ajaxLoading').hide();
		  				}
		  			});
	  				
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
	 <?php } else {
	 	$usersIP = strval($_SERVER["REMOTE_ADDR"]);
	 ?>
	 	dataString = "&ip=<?php echo $usersIP;  ?>"+"";
	 	
	 	urlString = "bin/process.php?callback=?"+dataString+"";
		//only perform when variables have been collected
		console.log(urlString);
		$.getJSON(urlString,{}, function(data) {
		
			if (data == "") { 
				//alert("no data");
			} else {
				//alert("data");
				console.log(data);
				info = data;
				loop_info();
			}
		});
		
	 <?php } ?>
	 function loop_info() {
		 for (var i = 0, l = info.length; i < l; i++) {
				var first_n = info[i].first_name;
				var last_n = info[i].last_name;
				var nick_n = info[i].name;
				var email_n = info[i].email;
				if (first_n != "NULL" && first_n != "") {
					firstField.inputBox.val(first_n);
				}
				if (last_n != "NULL" && last_n != "") {
					lastField.inputBox.val(last_n);
				}
				if (nick_n != "NULL" && nick_n != "") {
					console.log(true);
					nameField.inputBox.val(nick_n);
				}
				if (email_n != "NULL" && email_n != "") {
					emailField.inputBox.val(email_n);
				}
			}
	 }
	 console.log("<?php echo $code; ?>");
	 account_submit.click(function(e) {
			e.preventDefault();
			account_info();
			windowBox.fadeIn();
	});
	window_a.click(function(e) {
			windowBox.fadeOut();
	});
	function account_info() {
		var stopped = false;
		if (!stopped) {
			for (var i = 0, l = inputs.length; i < l; i++) {
				if (inputs[i].inputBox.val() == "") {
					stopped = true;
					inputs[i].inputBox.focus();
					inputs[i].inputError.show();
					inputs[i].inputBox.css({'border' : '1px solid red'});
				} else {
					inputs[i].inputBox.css({'border' : '1px solid #999'});
				}
		}
		}
		if (!stopped) {
			dataString = "&nickname=" + nameField.inputBox.val() + "&email=" + emailField.inputBox.val() + "&first_name=" + firstField.inputBox.val() + "&last_name=" + lastField.inputBox.val() + "<?php echo "&login="; if ($anon != ""){ echo $usersIP; echo "&anon=1"; echo "\""; } else { echo "\""; ?>+fb_id<?php }?>;
			urlString = "bin/process.php?callback=?"+dataString;
			console.log(urlString);
			//only perform when variables have been collected
			$.getJSON(urlString,{}, function(data) {
			
				if (data == "") { 
					//alert("no data");
				} else {
					//alert("data");
					console.log(data);
				}
			});
		}
	 }
	 function ajax_query(dataString) {
		 var ret;
		 /* $.ajax({
			      type: "POST",
			      url: "bin/process.php",
			      data: "?callback=?&"+dataString,
			      success: function(msg) {
			    	 ret=msg;
			    	 console.log(msg);
		          }
		     }); */

		     urlString = "bin/process.php?callback=?"+dataString;
		     console.log(urlString);
				//only perform when variables have been collected
				$.getJSON(urlString,{}, function(data) {
				
					if (data == "") { 
						//alert("no data");
					} else {
						//alert("data");
						console.log(data);
					}
				});
	     return ret;
	 }
});
</script>
<!-- soundcloud controls -->
<script type="text/javascript" src="js/soundcloud.player.api.js"></script>
<script src="js/sc-player.js" type="text/javascript"></script>
<script src="js/jquery-soundcloud-controls.js" type="text/javascript"></script>

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
	  
	  <div class="main-wrapper">
	<section id="main-menu" class="twelvecol last">
		<div class="white-wrapper white-padding">
		<nav id="main-navigation" class="menu-inner">
			<ul id="mainMenu">
					<li class="twocol"><span><a class="single" id="home_link" href="#main-content">home</a></span></li>
					<li class="twocol"><span><a class="single" id="profile_link" href="#profile-content">profile</a></span></li>
					<li class="twocol"><span><a id="playing_link" href="#playing-content">now playing</a></span></li>
					<li class="twocol"><span><a class="single" id="searches_link" href="#searches-content">searches</a></span></li>
					<li class="twocol"><span><a class="single" id="account_link" href="#account-content">account</a></span></li>
					<li class="twocol last"><span><a class="single" id="logout_link" href="logout.php?<?php if ($anon != "") { echo "anon=1"; }?>">Logout</a></span></li>
			</ul>
			<div class="clear"></div>
		</nav>
		
		</div>
		<div class="clear"></div>
	</section>
	<section id="main-content" class="twelvecol last menu-inner">
	<div class="white-wrapper">
	<div class="the-content">
<div class="content-header"><h1 class="header">Home</h1></div>
<div id="main-form" class="wrap">
<form class="floatCenter" action="#" method="post">
	<label class="" id="main-search-label" for="main-search-input">search</label>
	<input class="" type="text" placeholder="Search" name="search" id="main-search-input">
	<div class="mobileFix" style="">
	<ul class="ul-float"><li id="type"><a id="type-a" href="#">type of search</a><ul id="subMenu"><li><a id="track-type" href="#">tracks</a></li><li><a id="artist-type" href="#">artists</a></li><li><a id="genre-type" href="#">Genre</a></li></ul></li></ul>
	<input class="submit"  type="submit" name="submit" id="main-submit" value="Discover">
	<label class="main-error" id="search_error">Please enter a search</label>
	</div>
<div class="clear"></div>
</form>
<h2></h2>
</div>
<div class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading suggestions</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
<ul class="recom"></ul>
<div class="clear"></div>
<ul class="new"></ul>
<div class="clear"></div>
<ul id="others"></ul>
<div class="clear"></div>
</div>
	</div>
	</section>
	<section id="profile-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Profile</h1>
	
	</div>
	<div class="wrap">
	
	</div>
	</div>
	</section>
	<section id="playing-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Now Playing</h1>
	</div>
	<div class="wrap">
	</div>
	</div>
	</section>
	<section id="searches-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Searches</h1>
	
	</div>
	<div class="wrap">
	</div>
	</div>
	</section>
	<section id="account-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Account Settings</h1>
	</div>
	<div class="wrap">
		<form action="#" method="post">
		<label class="account-label" id="account-name-label" for="first-name-input">First Name</label>
		<input class="textbox" type="text" value="" name="first-name" id="first-name-input">
		<label class="account-label" id="account-last-label" for="last-name-input">Last Name</label>
		<input class="textbox" type="text" value="" name="last-name" id="last-name-input">
		<label class="account-label" id="account-email-label" for="email-input">Email</label>
		<input class="textbox" type="email" value="" name="email" id="email-input">
		<label class="account-label" id="nickname-label" for="nickname-input">Nickname</label>
		<input class="textbox" type="text" value="" name="name" id="nickname-input">
		<input class="submit"  type="submit" name="account-submit" id="account-submit" value="save">
		<div class="clear"></div>
		</form>
	</div>
	</div>
	</section>
	<br style="clear:both;"/>
	<!-- close up the main wrapper -->
	</div>
	
</div>
</div>

	  <footer id="player-container">
	  <div class="container">
	  <div class="row">
	  <div id="more-options"><a href="#" id="moreBtn">more...</a></div>
	  <div id="less-options"><a href="#" id="lessBtn">less...</a></div>
	  <div id="player-wrapper">
	  <div class="threecol play-btns">
	  <div class="box player-box">
	  	<a id="prev-btn" class="btn" href="#">prev-btn</a>
		<!-- <a class="sc-play" href="#play">New track</a> -->
		<a id="play-pause" href="http://soundcloud.com/user1207673/welcome" class="sc-player">Welcome</a>
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
	  <div class="threecol last">
		  <div class="player-box">
		  <a id="repeat-btn" class="off" href="#">repeat</a>
		  <a id="discovery-btn" class="off" href="#">discover (favourite this track)</a>
		  </div>
		<div class="clear"></div>
	  </div>
	  </div>
	  <div class="clear"></div>
	  </div>
	  </div>
	  </footer>
	  
<div id="window"><div id="window-wrapper"><div id="window-head"><h1>Information</h1></div><div id="window-body"><p>Your settings have been updated</p><p><a id="window-a" href="#">Okay</a></p></div></div></div>
<div id="fb-root"></div>
</body>
</html>
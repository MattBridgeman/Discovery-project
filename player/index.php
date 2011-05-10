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
function SetCookieLive($name, $value='', $expire = 0, $path = '', $domain='', $secure=false, $httponly=false) 
	    { 
	        $_COOKIE[$name] = $value; 
	        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly); 
	    }
require_once '../facebook-php-sdk/src/facebook.php';

$app_id = 154550127940245;
$app_secret = "08751756393722bcc0ec3ad06a20d12f";
$my_url = curPageURL(); 
$code = $_REQUEST["code"];
$scope = "email,user_likes";
$anon = "";
// Create our Application instance.
$facebook = new Facebook(array(
  'appId' => $app_id,
  'secret' => $app_secret,
  'cookie' => true,
));
if(isset($_GET['anon'])) {
	$anon = $_GET['anon'];
$today = date("d.m.y");

//setcookie("discovery-ip", "");
$cookie = $_COOKIE["discovery-ip"];
if ($cookie != null && $cookie != "") {
		//they have logged in before
		/*echo $cookie;
		$sql = "SELECT * FROM users WHERE ip='$cookie'";
		$send = $database->query($sql);
		while($row = mysql_fetch_array($send)) {
			$ip = $row['ip'];
		}*/
		$ip = $cookie;
	} else {
		$sql = "SELECT * FROM users";
		$number = array();
		$send = $database->query($sql);
		$highest = 0;
		while($row = mysql_fetch_array($send)) {
			if ($row['ip'] != "") {
				array_push($number, $row['ip']);
			}
		}
		for ($i = 0; $i < count($number); $i++) {
			if ($i == 0) {
				
			} else if ($i == 1) {
				$highest = $number[1];
			} else {
				//if this number is bigger than the number before make it $highest
				if ($number[$i] > $number[$i-1]) {
					$highest = $number[$i];
					$highest;
				}
			}
		}
		$value = $highest+1;
		setcookie("discovery-ip", $value, time()+60*60*24*365, "", ".thediscoveryapp.com");
	    $ip = $value;
	}
	
} else {
	if(empty($code)) {
		$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
		. $app_id . "&scope=". $scope ."&redirect_uri=http://www.thediscoveryapp.com/";
		$redirect = "" . $dialog_url . "";
	}
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<!-- meta info -->
<meta charset="UTF-8">

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
<link rel="stylesheet" href="css/1140_2/css/styles.css" type="text/css" media="screen" />
<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
<script type="text/javascript" src="css/1140_2/js/css3-mediaqueries.js"></script>

<link REL="SHORTCUT ICON" HREF="../css/images/favicon.ico">
<!-- javascript -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<!-- further HTML5 and new technologies fixes-->
<script src="js/modernizr-1.7.min.js" type="text/javascript"></script>
<!-- iPhone -->
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=0;">
<script src="js/fixed.js" type="text/javascript" charset="utf-8"></script>
<!-- jQuery UI elements -->
<script src="js/jquery-ui-1.8.6.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/soundcloud.player.api.js"></script>
<script src="js/sc-player.js" type="text/javascript"></script>
<script src="js/hash.js" type="text/javascript"></script>
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
	var playerReady = false;
	var inputField = function (inputBox) {
		
		this.inputBox = inputBox;
		
	}
	var likes = new Array();
	var responses = new Array();
	var recommendations = new Object();
	var recom_image = $('li.recom-image');
	var moreInfo = $('a.moreInfo-a');
	//the player is a random object
	theData = new Object();
	thePlayList = new Object();
	theLoad = new Object(); //load percentage
	
	currentPlaying = 0; //a number for use within the playlist
	
	//vars
	var requestString = "users/spawn/tracks/";
	var additionalParams = "";
	//elements to control
	var errors = $(".error");
	var mainError = $('.main-error');
	//search elements
	var mainSearchInput = $('#main-search-input');
	var submit = $('.submit');
	//menu elements
	
	var homeLink = $('a#home_link');
	var homeContent = $('#main-content');
	var profileLink = $("a#profile_link");
	var profileContent = $('#profile-content');
	var nowPlayingLink = $("a#playing_link");
	var nowPlayingContent = $('#playing-content');
	var searchesLink = $("a#social_link");
	var searchesContent = $('#social-content');
	var accountLink = $("a#account_link");
	var accountContent = $('#account-content');
	var searchType = $('a#type-a');
	var searchT;
	//play elements
	var repeatBtn = $("#repeat-btn");
	var volumeBtn = $("#volume-btn");
	var volumeSlider = $("#volumeSlider");
	var volumeSliderContainer = $("#volumeSliderContainer");
	//var playSlider = $("#playSlider");
	var prevBtn = $('#prev-btn');
	var nextBtn = $('#next-btn');
	var playPause = $('a.sc-play');
	var moreBtn = $('#moreBtn');
	var lessBtn = $('#lessBtn');
	var playerContainer = $('#player-container');
	//window
	var windowBox = $('#window');
	
	var window_head = $('#window-head');
	var window_body = $('#window-body');
	var window_a = $('#window-a');
	//dynamic RHS elements
	var scTrack = $('a.track-load');
	
	//logic vars
	var prevTrack;
	var nextTrack;
	var amount = $("#amount");
	var mySlider = $("#mySlider");
	var loading;
	var playPos;
	var isLoaded = false;
	var isPlaying = false;
	var loadedAmt = 0;
	var trackDuration;
	var trackY;
	var skipTo; //skip to seconds
	var loadedAmt;
	var loadedSeconds;
	var currentTrack;
	var playList = new Array();
	var otherPlaylists = new Array();
	var selectedPlaylist;
	var fromPlayLoad = false;
	playList.push("http://api.soundcloud.com/tracks/13964562");
	var theNewPlayer = new Object();
	var firstPlay = true;
	var states = new Array();
	var types = new Array();
	var selectedType = "";
	var submenu = $('ul#subMenu');
	var genreType = $('#genre-type');
	var artistType = $('#artist-type');
	var trackType = $('#track-type');
	var fromSearch = false;
	var id = 0;
	//elements to hide
	errors.hide();
	volumeSliderContainer.hide();
	mainError.hide();
	lessBtn.hide();
	submenu.hide();
	//$('ul.subFM').live().hide();
	//.live('mouseover mouseout', function(event) {
	function isiPhone(){
    return (
        (navigator.platform.indexOf("iPhone") != -1) ||
        (navigator.platform.indexOf("iPod") != -1)
    );
	}
	if (isiPhone()) {
		$('#player-container').css({'position' : 'absolute', 'top': '368px'});
		$('#container').css({'height': '324px'});
	}
	
	
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
			
  			FB.api('/me', function(response) {
  	  			
  				name = response.name;
  				
  				email = response.email;
  				first_name = response.first_name;
  				last_name = response.last_name;
  				dataString = '&fb_id=' + fb_id;
  				dataString += '&name=' + name + '&email=' + email + '&lastname=' + last_name + '&first_name=' + first_name;
  				
				//only perform when variables have been collected
				urlString = "bin/process.php?callback=?"+dataString+"";
				////console.log(urlString);
		  			//only perform when variables have been collected
		  			//console.log(urlString);
		  			$.getJSON(urlString,{}, function(msg) {
		  				//console.log(msg);
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
			FB.api('/me/friends', function(response) {
  	  			//take entire response and place it in process.php
  	  			//console.log(response);
  	  		$.post('bin/functions.php', {'response[]': response, 'theFB': fb_id }, function(data){
  	  		   // do something with received data!
  	  		});
  			
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
		  					if (isEmpty(data2)) {
	  						$('body').find('ul.recom').empty();
	  						$('body').find('ul.recom').append('<li class="favArtLi"><span class="emptyspan">Query Empty</span></li>');
	  						$('div.ajaxLoading').hide();
							}
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
			  						
		  							$('body').find('ul.recom').append('<ul class="'+col+'"><li id="recom-img'+i+'" class="recom-image"><div class="artist-pic"><div style="height:200px;background: url(\''+data2[i].image+'\');"></div></div><ul class="subFM"><li class="moreInfo"><a class="moreInfo-a" href="#">More Info</a></li><li class="subSearch '+i+'"><a class="artist-search" href="'+data2[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data2[i].name+'">similar artists</a></li><li class="subFav '+i+'"><a class="favourite-add" href="'+data2[i].name+'">Favourite</a></li></ul></li><li class="recom-info"><a href="#">'+data2[i].name+'</a></li></ul>');
		  						
			  				}
				  		}
		  				
						//
		  				}
		  			});
	  				
  			}
  			
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
	 	$usersIP = $ip;
	 ?>
	 	dataString = "&ip=<?php echo $usersIP;  ?>"+"&anon=1";
	 	
	 	urlString = "bin/process.php?callback=?"+dataString+"";
		//only perform when variables have been collected
		//console.log(urlString);
		$.getJSON(urlString,{}, function(data) {
		
			if (data == "") { 
				//alert("no data");
			} else {
				//alert("data");
				//console.log(data);
				info = data;
				loop_info(info);
			}
		});
	 <?php } ?>
	 function isEmpty(ob){
		   for(var i in ob){ return false;}
		  return true;
		}
	//last.fm recommendations
		$('a.similar-search').live('click', function(e) {
			e.preventDefault();
			homeLink.trigger('click');
			dataString = "&similar=";
			$('body').find('ul.recom').fadeOut();
			$('body').find('ul.others').fadeOut();
			$('body').find('div.ajaxLoading').show();
			search = $(this).attr('href');
			//console.log(search);
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
		  						
	  							$('ul#others').append('<ul class="'+col+'"><li id="recom-img'+i+'" class="recom-image"><div class="artist-pic"><div style="height:200px;background: url(\''+data3[i].image+'\');"></div></div><ul class="subFM"><li class="moreInfo"><a class="moreInfo-a" href="#">More Info</a></li><li class="subSearch '+i+'"><a class="artist-search" href="'+data3[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data3[i].name+'">similar artists</a></li><li class="subFav '+i+'"><a class="favourite-add" href="'+data3[i].name+'">Favourite</a></li></ul></li><li class="recom-info"><a href="#">'+data3[i].name+'</a></li></ul>');
		  				}
		  				
			  		}

					$('div.ajaxLoading').hide();
	  				}
	  			});
			return false;
		});
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
			  return false;
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
				  return false;
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
	 $('a.favourite-add').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			name = $(this).attr('href');
			dataString = "&favourite=";
			dataString+=name;
			dataString+="&artist=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			//console.log(dataString);
	  		urlString = "bin/process.php?callback=?"+dataString+"";
	  			$.getJSON(urlString,{}, function(data) {
	  				if (data == "") {
	  				} else {
		  				//console.log(data);
	  					if (data.error == "unforeseen error") {
	  						window_head.find("h1").html("Error");
	  						window_body.find("p.window-p").empty().html("There was an issue adding your favourite");
	  						windowBox.fadeIn();
	  					} else if(data.error == "already liked that artist") {
	  						window_head.find("h1").html("Already Liked");
	  						window_body.find("p.window-p").empty().html("You have already favourited "+name);
	  						windowBox.fadeIn();
	  					} else {
	  						window_head.find("h1").html("Favourite Added");
	  						window_body.find("p.window-p").empty().html("\""+name+"\" was added to your favourites");
	  						windowBox.fadeIn();
	  					}
	  				}
	  			});
	  		return false;
		});
	 $('a.favourite-remove').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			name = $(this).attr('href');
			var id = $(this).html();
			id = id.replace("Unfavourite ", "");
			//console.log("id: "+id);
			
			dataString = "&unfavourite=";
			dataString+=name;
			dataString+="&artist=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			
	  		urlString = "bin/process.php?callback=?"+dataString+"";
	  			$.getJSON(urlString,{}, function(data) {
	  				if (data == "") {
	  				} else {
		  				//console.log(data);
	  					if (data.error == "unforeseen error") {
	  						window_head.find("h1").html("Error");
	  						window_body.find("p.window-p").empty().html("There was an issue removing your favourite");
	  						windowBox.fadeIn();
	  					} else {
	  						//id
	  						var isLi;
	  						 $("li.favArtLi").each(function (i) {
								if($(this).hasClass(id)) {
									$(this).fadeOut();
									$(this).remove();
								}
	  						}); 
	  						window_head.find("h1").html("Favourite Removed");
	  						window_body.find("p.window-p").empty().html("\""+name+"\" was removed your favourites");
	  						windowBox.fadeIn();
	  					}
	  				}
	  			});
	  		return false;
		});
		$('a.favourite-remove-genre').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			name = $(this).attr('href');
			var id = $(this).html();
			id = id.replace("Unfavourite ", "");
			
			dataString = "&genre=";
			dataString+=name;
			dataString+="&notGenre=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			
	  		urlString = "bin/lastfm.php?callback=?"+dataString+"";
	  			$.getJSON(urlString,{}, function(data6) {
	  				if (data6 == "") {
	  				} else {
		  				//console.log(data6);
	  					if (data6.error == "unforeseen error") {
	  						window_head.find("h1").html("Genre Error");
	  						window_body.find("p.window-p").empty().html("There was an issue removing your favourite genre");
	  						windowBox.fadeIn();
	  					} else {
	  						//id
	  						
	  						 $(this).closest('ul.favGenres').children('li').each(function (i) {
								if($(this).hasClass(id)) {
									$(this).fadeOut();
									$(this).remove();
								}
	  						}); 
	  						window_head.find("h1").html("Favourite Removed");
	  						window_body.find("p.window-p").empty().html("\""+name+"\" was removed your favourite genres");
	  						windowBox.fadeIn();
	  					}
	  				}
	  			});
	  		return false;
		});
		$('a.visualSearch').live('click', function(e) {
			e.preventDefault();
			title = $(this).attr('href');
			popuponclick(title);
		});
		function popuponclick(title)
		   {
		      my_window = window.open("bin/test.php?similar="+title,
		       "mywindow","status=1,width=100%,height=100%");
		      my_window.document.write('<h1>The Popup Window</h1>');
		   }
		$('a.favTrack').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			name = $(this).attr('href');
			dataString = "";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			dataString+="&track="+name;
			dataString+="&artist=true";
	  		urlString = "bin/process.php?callback=?"+dataString+"";
	  		//console.log(urlString);
	  			$.getJSON(urlString,{}, function(data7) {
	  				if (data7 == "") {
	  				} else {
	  					if (data7.error == "unforeseen error") {
	  						window_head.find("h1").html("Favourite Error");
	  						window_body.find("p.window-p").empty().html("This track has already been added to favourites");
	  						windowBox.fadeIn();
	  					} else {
		  					//console.log(data7);
	  						window_head.find("h1").html("Favourite added");
	  						window_body.find("p.window-p").empty().html("Track was added to your favourites");
	  						windowBox.fadeIn();
	  					}
	  				}
	  			});
	  		return false;
		});
		$('a.unfavTrack').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			name = $(this).attr('href');
			var id = $(this).html();
			id = id.replace("Unfavourite this Track ", "");
			dataString = "";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			dataString+="&unfavtrack="+name;
			dataString+="&artist=true";
	  		urlString = "bin/process.php?callback=?"+dataString+"";
	  		//console.log(urlString);
	  			$.getJSON(urlString,{}, function(data8) {
	  				if (data8 == "") {
	  				} else {
	  					if (data8.error == "unforeseen error") {
	  						window_head.find("h1").html("Unfavourite Error");
	  						window_body.find("p.window-p").empty().html("This track has already been removed from favourites");
	  						windowBox.fadeIn();
	  					} else {
	  						$('body').find('ul.favTracks').children('li').each(function (i) {
								if($(this).hasClass(id)) {
									$(this).fadeOut();
									$(this).remove();
								}
	  						}); 
	  						window_head.find("h1").html("Favourite removed");
	  						window_body.find("p.window-p").empty().html("Track was removed from your favourites");
	  						windowBox.fadeIn();
	  					}
	  				}
	  			});
	  		return false;
		});
		//playListDelete
		$('a.playListDelete').live('click', function(e) {
			e.preventDefault();
			//$('ul.recom').fadeOut();
			 var name = $(this).attr('href');
			var id = $(this).html();
			id = id.replace("Delete this Track ", "");
			var listname;
			listname = $(this).closest('ul.list').attr('id');
			if (listname != null) {
				listname = listname.replace("list-", "");
				for (var i in otherPlaylists[listname]) {
					otherPlaylists[listname][i] = otherPlaylists[listname][i].replace("http://api.soundcloud.com/tracks/", "");
					otherPlaylists[listname][i] = otherPlaylists[listname][i]+'';
					name = name+'';
					if (otherPlaylists[listname][i] == name) {
						otherPlaylists[listname].splice(i, 1);
					}
				}
			} else {
				for (var i in playList) {
					playList[i] = playList[i].replace("http://api.soundcloud.com/tracks/", "");
					playList[i] = playList[i]+'';
					name = name+'';
					if (playList[i] == name) {
						playList.splice(i, 1);
						//console.log("spliced "+i);
					}
				}
			}
			
			
  						$(this).closest('li.playlistLi').fadeOut().remove();
  						
  						
  						window_head.find("h1").html("Track removed from playlist");
  						window_body.find("p.window-p").empty().html("Track was removed from playlist");
  						windowBox.fadeIn();
	  					
	  		return false;
		});
		$('a.queueTrack').live('click', function(e) {
			e.preventDefault();
			track = $(this).attr('href');
			playList.push(track);
			var name = $(this).html();
			name = name.replace('queue track ', ' ');
			window_head.find("h1").html("Track Queued");
			window_body.find("p.window-p").empty().html(name+" was added to the playlist");
			windowBox.fadeIn();
			return false;
		});
	 	$('a.similar-genre').live('click', function(e) {
	 		e.preventDefault();
	 		id = $(this).attr('href');
	 		id = id.replace(' ', '_');
	 		id = id.replace(" ", "_");
				id = id.replace(" ", "_");
				id = id.replace(" ", "_");
	 		//similar_genres();
	 		//add a loading
	 		//$(this).closest('li.favGenre').after('<ul class="related-genres" id="'+id+'"></ul>');
	 		$('body').find('ul#'+id).append('<li style="width:64px; margin:0 auto;"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></li><div class="clear"></div>');
	 		
			dataString = "&getRelatedGenres=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			dataString+="&tag="+$(this).attr('href');
	  		 	urlString = "bin/lastfm.php?callback=?"+dataString+"";
	  		 	
	  			$.getJSON(urlString,{}, function(data4) {
	  			
	  				if (data4 == "") { 
	  					//alert("no data");
	  				} else {
		  				
	  					//alert("data");
	  					$('body').find('ul#'+id).empty();
	  					
	  				for (var i = 0, l = data4.length; i < l; i++) {
	  					var name = data4[i];
		  				name = name.replace(" ", "_");
		  				name = name.replace(" ", "_");
		  				name = name.replace(" ", "_");
		  				name = name.replace(" ", "_");
	  							$('body').find('ul#'+id).append('<li class="favGenre '+i+'"><span>'+data4[i]+'</span><ul class="artist-options"><li class="genreSearch '+i+'"><a class="genre-search" href="'+data4[i]+'">songs in genre</a></li><li class="genreSimilar '+i+'"><a class="similar-genre" href="'+data4[i]+'">similar genres</a></li><li class="subFav last '+i+'"><a class="favourite-remove-genre" href="'+data4[i]+'">Unfavourite '+i+'</a></li></ul></li><ul class="related-genres" id="'+name+'"></ul>');
	  						}
	  				
	  				}
	  			});
	  		return false;
	 	});
	 function loop_info() {
		 for (var i = 0, l = info.length; i < l; i++) {
				var first_n = info[i].first_name;
				var last_n = info[i].last_name;
				var nick_n = info[i].name;
				var email_n = info[i].email;
				if (first_n != null && first_n != "") {
					firstField.inputBox.val(first_n);
				}
				if (last_n != null && last_n != "") {
					lastField.inputBox.val(last_n);
				}
				if (nick_n != null && nick_n != "") {
					//console.log(true);
					nameField.inputBox.val(nick_n);
				}
				if (email_n != null && email_n != "") {
					emailField.inputBox.val(email_n);
				}
			}
	 }
	 //console.log("<?php echo $code; ?>");
	 account_submit.click(function(e) {
			e.preventDefault();
			account_info();
			windowBox.fadeIn();
		return false;
	});
	window_a.click(function(e) {
			e.preventDefault();
			windowBox.fadeOut();
		return false;
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
			dataString = "";
			dataString = "&nickname=" + nameField.inputBox.val() + "&email=" + emailField.inputBox.val() + "&first_name=" + firstField.inputBox.val() + "&last_name=" + lastField.inputBox.val() + "<?php echo "&login="; if ($anon != ""){ echo $usersIP; echo "&anon=1"; echo "\""; } else { echo "\""; ?>+fb_id<?php }?>;
			urlString = "bin/process.php?callback=?"+dataString;
			//console.log(urlString);
			//only perform when variables have been collected
			$.getJSON(urlString,{}, function(data) {
			
				if (data == "") { 
					//alert("no data");
				} else {
					//alert("data");
					//console.log(data);
				}
			});
		}
	 }
	 function ajax_query(dataString) {
		 var ret;
		     urlString = "bin/process.php?callback=?"+dataString;
		     //console.log(urlString);
				//only perform when variables have been collected
				$.getJSON(urlString,{}, function(data) {
				
					if (data == "") { 
						//alert("no data");
					} else {
						//alert("data");
						//console.log(data);
					}
				});
	     return ret;
	 }

	//generic
		
		var typeState = function (menuItem) {
			this.menuItem = menuItem;
			this.menuItem.bind('click', function(e) {
				e.preventDefault();
				selectedType = $(this).html();
				return false;
			});
		}
		var genreState = new typeState(genreType);
		types.push(genreState);
		var artistState = new typeState(artistType);
		types.push(artistState);
		var trackState = new typeState(trackType);
		types.push(trackState);
		var menuState = function (menuItem, wrapElement, children) {
			this.id = id;
			id++;
			this.menuItem = menuItem;
			this.wrapElement = wrapElement;
			this.header = wrapElement.find("h1.header");
			this.body = wrapElement.find("div.wrap");
			this.selected = false;
			if (children) {
				menuItem.children().hide();
			}
			var theWrap = this.wrapElement.attr("id");
			var stateID = this.id;
			this.menuItem.bind('click', function(e) {
				e.preventDefault();
				
				if (Modernizr.history) {
					  // history management works!
					  var check_i = 0;
				for (var i = 0; i < states.length; i++) {
					if (states[i].wrapElement.attr("id") == theWrap) {
						check_i = i;
					}
				}
					history.pushState(check_i, theWrap, "#"+theWrap);
					
					} else {
					  // no history support :(
					  // fall back to a scripted solution like History.js
						
					}
				changeState(theWrap);
				return false;
			});
		}
		
		homeState = new menuState(homeLink, homeContent, false);
		states.push(homeState);
		profileState = new menuState(profileLink, profileContent, false);
		states.push(profileState);
		nowPlayingState = new menuState(nowPlayingLink, nowPlayingContent, false);
		states.push(nowPlayingState);
		searchesState = new menuState(searchesLink, searchesContent, false);
		states.push(searchesState);
		accountState = new menuState(accountLink, accountContent, false);
		states.push(accountState);
		//console.log(states.length);
		for (var i = 0; i < states.length; i++) {
			
			if (i > 0) {
				states[i].menuItem.addClass("unselected");
				states[i].wrapElement.hide();
			} else {
				states[i].menuItem.addClass("selected");
			}
		}
		$(window).hashchange( function(){
			var hash = location.hash;
			hash = hash.replace("#", "");
			changeState(hash);
			console.log(hash);
		});
		window.onpopstate = function(event) {
			  //alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
			//console.log(event);
			var hash = location.hash;
			hash = hash.replace("#", "");
			changeState(hash);
		};
					
		function changeState(state) {
			var hashNumber = 0;
			for (var i = 0; i < states.length; i++) {
				if (states[i].wrapElement.attr("id") == state) {
					hashNumber++;
					
					//change the class to on and it's wrapElement to on, if children is equal to true turn them on
					states[i].menuItem.addClass("selected");
					states[i].selected = true;
					states[i].menuItem.removeClass("unselected");
					if (states[i].wrapElement) {
						states[i].wrapElement.show();
						if (states[i].wrapElement.attr("id") == "profile-content") {
							loadGenres();
							loadTags();
							loadTracks();
						} else if (states[i].wrapElement.attr("id") == "playing-content") {
							loadPlaylist();
							loadOtherPlaylist();
						} else if (states[i].wrapElement.attr("id") == "social-content") {
							loadFriends();
						}
					}
					
					if (states[i].children) {
						states[i].children().show();
					}
				} else {
					//change the class to off, wrapElement to off and children to off
					states[i].menuItem.addClass("unselected");
					states[i].selected = false;
					states[i].menuItem.removeClass("selected");
					if (states[i].wrapElement) {
					states[i].wrapElement.hide();
					}
					if (states[i].children) {
						states[i].children().hide();
					}
				}
				if (state == "") {
					homeState.menuItem.addClass("selected");
					homeState.selected = true;
					homeState.menuItem.removeClass("unselected");
					homeState.wrapElement.show();
				}
			}
			
		}
		function loadGenres() {
			profileState.wrapElement.find('div.ajaxLoading').show();
			dataString = "";
			dataString = "&getLikes=true&artist=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
	  		 	urlString = "bin/process.php?callback=?"+dataString+"";
	  			//only perform when variables have been collected
	  			//console.log(urlString);
	  			$.getJSON(urlString,{}, function(data4) {
	  			
	  				if (data4 == "") { 
	  					//alert("no data");
	  					if (isEmpty(data4)) {
	  						$('body').find('ul.favArtists').empty();
	  						$('body').find('ul.favArtists').append('<li class="favArtLi"><span class="emptyspan">Discovered Artists will appear here</span></li>');
								$('#ajaxArtists').hide();
							}
	  				} else {
		  				
	  					//alert("data");
	  					$('div#ajaxArtists').hide();
	  					var col = "threecol";
	  					$('body').find('ul.favArtists').empty();
	  					for (var i = 0, l = data4.length; i < l; i++) {
	  						$('body').find('ul.favArtists').append('<li class="favArtLi '+i+'"><span class="artistSpan"><div style="background: url(\''+data4[i].image+'\');height:40px;"></div></span><a class="artist-search" href="'+data4[i].name+'">'+data4[i].name+'</a><ul class="artist-options"><li class="subSearch '+i+'"><a class="artist-search" href="'+data4[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data4[i].name+'">similar artists</a></li><li class="subFav '+i+'"><a class="favourite-remove" href="'+data4[i].name+'">Unfavourite '+i+'</a></li></ul></li>');
	  					}
	  				
	  				}
	  			});
		}
		function loadTracks() {
			$('div#ajaxTracks').show();
			dataString = "";
			dataString = "&getArtists=true&artist=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
	  		 	urlString2 = "bin/process.php?callback=?"+dataString+"";
	  			//only perform when variables have been collected
	  			//console.log(urlString2);
	  			$.getJSON(urlString2,{}, function(data6) {
		  		
	  				if (data6 == "") { 
	  					//alert("no data");
	  					if (isEmpty(data6)) {
	  						$('body').find('ul.favTracks').empty();
	  						$('body').find('ul.favTracks').append('<li class="favTrackLi"><span class="emptyspan">Discovered Tracks will appear here</span></li>');
								$('#ajaxTracks').hide();
							}
	  				} else {
	  					//ids
	  					//console.log(data6);
	  					var newString = "&ids="+data6;
	  					var urlRequest = "http://api.soundcloud.com/tracks.json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
	  					//console.log(urlRequest);
	  					$.getJSON(urlRequest, function(data) {

	  						if (data == "") { 
	  							if (isEmpty(data4)) {
	  		  						$('body').find('ul.favTracks').append('<li clas="favTrackLi"><span class="emptyspan">Discovered Tracks will appear here</span></li>');
	  									$('#ajaxTracks').hide();
	  								}
	  						} else { 
	  							var count = 0;
	  							var purchase;
	  							$('body').find('ul.favTracks').empty();
	  						for ( keyVar in data) {
		  						purchase = "";
	  							   if (data[keyVar].purchase_url == null) {
										purchaseClass = "";
	  							   } else {
		  							   purchaseClass = "purchase";
										purchase = '<li class="purchaseTrack"><a target="_blank" class="purchaseTrack" href="'+ data[keyVar].purchase_url +'">Purchase Track '+ data[keyVar].title +'</a></li>';
	  							   }
	  							   //console.log(data[keyVar]);
	  							 $('body').find('ul.favTracks').append('<li class="favTrackLi '+count+'"><span class="artistSpan"><div style="background: url('+data[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="track-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="queueTrack '+count+'"><a class="queueTrack" href="'+ data[keyVar].uri +'">queue track '+ data[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data[keyVar].user_id+'">'+data[keyVar].user_id+'</a></li><li class="unfavTrack last '+count+'"><a class="unfavTrack" href="'+data[keyVar].id+'">Unfavourite this Track '+count+'</a></li></ul></li>');
								count ++;
	 	  					}
	  						}
	  						
	  			   	    });
	  					//alert("data");
	  					$('div#ajaxTracks').hide();
	  					
	  				}
	  			});
		}
		function loadPlaylist() {
			$('div#ajaxPlaylist').show();

			//sets up a string to query soundcloud
			var string;
			for (var i in playList) {
				playList[i] = playList[i].replace("http://api.soundcloud.com/tracks/", "");
				if (playList[i] == playList[0]) {
					string= playList[i]+",";
				} else if (playList[i] == playList[playList.length]) {
					string+= playList[i];
				} else {
					string+= playList[i]+",";
				}
			}
			
			if (string == 0 || string == null) {
				$('div#ajaxPlaylist').hide();
				$('body').find('ul.playlist').empty().append('<li class="playlistLi"><span>Tracks will appear hear</span></li>');
			} else {
						var table = new Array();
	  					var newString = "&ids="+string;
	  					var urlRequest = "http://api.soundcloud.com/tracks.json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
	  					$.getJSON(urlRequest, function(data) {

	  						if (data == "") { 
	  							$('div#ajaxPlaylist').hide();
	  							$('body').find('ul.playlist').empty().append('<li>Query Empty</li>');	
	  						} else { 
		  						$('div#ajaxPlaylist').hide();
	  							var count = 0;
	  							var purchase;
	  							$('body').find('ul.playlist').empty();
		  						for ( keyVar in data) {
			  						purchase = "";
		  							   if (data[keyVar].purchase_url == null) {
											purchaseClass = "";
		  							   } else {
			  							    purchaseClass = "purchase";
											purchase = '<li class="purchaseTrack"><a target="_blank" class="purchaseTrack" href="'+ data[keyVar].purchase_url +'">Purchase Track '+ data[keyVar].title +'</a></li>';
		  							   }
		  							   var number;
		  							   number = data[keyVar].uri;
		  							 var string = '<li class="playlistLi '+count+'"><span class="artistSpan"><div style="background: url('+data[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="play-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="playTrack '+count+'"><a class="play-load" href="'+ data[keyVar].uri +'">play track '+ data[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data[keyVar].user_id+'">'+data[keyVar].user_id+'</a></li><li class="playListDelete last '+count+'"><a class="playListDelete" href="'+data[keyVar].id+'">Delete this Track '+count+'</a></li></ul></li>'
		  							   table[number] = string;
		  							 $('body').find('ul.playlist').append('<li class="playlistLi '+count+'"><span class="artistSpan"><div style="background: url('+data[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="play-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="playTrack '+count+'"><a class="play-load" href="'+ data[keyVar].uri +'">play track '+ data[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data[keyVar].user_id+'">'+data[keyVar].user_id+'</a></li><li class="playListDelete last '+count+'"><a class="playListDelete" href="'+data[keyVar].id+'">Delete this Track '+count+'</a></li></ul></li>');
									count ++;
		 	  					}
		  						$('body').find('ul.playlist').append('<h3 class="search-class"><a href="#" class="save-playlist">Save Playlist</a></h3>');
	  						}
	  						
	  			   	    });
			}	
	  			
		}
		function loadOtherPlaylist() {
			$('div#ajaxOtherPlaylist').show();
			
			if (!isEmpty(otherPlaylists)) {
				 for(var i in otherPlaylists) { delete otherPlaylists.i; }
			}
			dataString="&something=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
			urlRequest = "bin/functions.php?callback=?"+dataString+"";
  			$.getJSON(urlRequest, function(data) {

  				if (data == "") { 
  					$('div#ajaxOtherPlaylist').hide();
  					$('body').find('ul.other-playlist').empty().append('<li class="playlistLi"><span class="emptyspan">Playlists will appear hear when playlists are made</span></li>');
  				
  				} else {
  					$('body').find('ul.other-playlist').empty();
  					function doJson (dataName, i) {
						var dataName = dataName;
						var i = i;
	  					$.getJSON(urlRequest, function(data2) {

	  						if (data2 == "") { 
	  						} else {
	  							var count = 0;
	  							var purchase;
		  						for ( keyVar in data2) {
			  						purchase = "";
		  							   if (data2[keyVar].purchase_url == null) {
											purchaseClass = "";
		  							   } else {
			  							    purchaseClass = "purchase";
											purchase = '<li class="purchaseTrack"><a target="_blank" class="purchaseTrack" href="'+ data2[keyVar].purchase_url +'">Purchase Track '+ data2[keyVar].title +'</a></li>';
		  							   }
		  							   var number;
		  							   number = data2[keyVar].uri;
		  							 var string = '<li class="playlistLi '+count+'"><span class="artistSpan"><div style="background: url('+data2[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="playList-load '+i+'" href="'+ data2[keyVar].uri +'" >'+ data2[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="playTrack '+count+'"><a class="playList-load '+i+'" href="'+ data2[keyVar].uri +'">play track '+ data2[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data2[keyVar].user_id+'">'+data2[keyVar].user_id+'</a></li><li class="playListDelete last '+count+'"><a class="playListDelete" href="'+data2[keyVar].id+'">Delete this Track '+count+'</a></li></ul></li>'
		  							 $('body').find('ul.list-'+dataName.name).append('<li class="playlistLi '+count+'"><span class="artistSpan"><div style="background: url('+data2[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="playList-load '+i+'" href="'+ data2[keyVar].uri +'" >'+ data2[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="playTrack '+count+'"><a class="playList-load '+i+'" href="'+ data2[keyVar].uri +'">play track '+ data2[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data2[keyVar].user_id+'">'+data2[keyVar].user_id+'</a></li><li class="playListDelete last '+count+'"><a class="playListDelete" href="'+data2[keyVar].id+'">Delete this Track '+count+'</a></li></ul></li>');
									count ++;
		 	  					}
	  						}
	  						
	  			   	    }); 
	  					}
					for(var i = 0; i < data.length; i++) {
						//foreach list make a ul or li for the list
						var dataName = new Object();
						if(i == 0) {
							dataName = data[i]['0'];
						} else {
							dataName = data[i]['0']['0'];
						}
						$('body').find('ul.other-playlist').append("<h3 class='search-class'>Playlist: "+dataName.name+"</h3>");
						$('body').find('ul.other-playlist').append("<ul id='list-"+i+"' class='list list-"+dataName.name+"'></ul>");
						otherPlaylists.push(dataName.list);
						
						var string;
						for(var ii = 0; ii < dataName.list.length; ii++) {
							var i = i;
							if (dataName.list[0] == dataName.list[ii]) {
								string= dataName.list[ii]+",";
							} else if (dataName.list[ii] == dataName.list.length) {
								string+= dataName.list[ii];
							} else {
								string+= dataName.list[ii]+",";
							}
						}
						//loop the track like you would on the playlist
						var newString = "&ids="+string;
	  					var urlRequest = "http://api.soundcloud.com/tracks.json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
	  					
	  					doJson(dataName, i);
					}
					//console.log(otherPlaylists)
					$('div#ajaxOtherPlaylist').hide();
  				}
  			});
	  			
		}
		function loadTags() {
			profileState.wrapElement.find('div.ajaxLoading').show();
			dataString = "&getGenres=true";
			<?php if ($anon != "") { ?>
			dataString+="&anon=true";
			dataString+="&ip=<?php echo $usersIP;  ?>";
			<?php } else { ?>
			dataString+="&ip="+fb_id;
			<?php } ?>
	  		 	urlString = "bin/lastfm.php?callback=?"+dataString+"";
	  		 	
	  			//only perform when variables have been collected
	  			////console.log(urlString);
	  			$.getJSON(urlString,{}, function(data4) {
	  			
	  				if (data4 == "") {
	  					//alert("no data");
	  					if (isEmpty(data4)) {
	  						$('body').find('ul.favGenres').empty();
	  						$('body').find('ul.favGenres').append('<li class="favGenre"><span class="emptyspan">Genre Tags will be generated through your artist likes</span></li>');
								$('#ajaxTags').hide();
							}
	  				} else {
		  				
	  					//alert("data");
	  					$('div#ajaxTags').hide();
	  					$('body').find('ul.favGenres').empty();
	  				for (var i = 0, l = data4.length; i < l; i++) {
	  					var id = data4[i];
		  				id = id.replace(" ", "_");
		  				id = id.replace(" ", "_");
		  				id = id.replace(" ", "_");
		  				id = id.replace(" ", "_");
	  							$('body').find('ul.favGenres').append('<li class="favGenre '+i+'"><span>'+data4[i]+'</span><ul class="artist-options"><li class="genreSearch '+i+'"><a class="genre-search" href="'+data4[i]+'">songs in genre</a></li><li class="genreSimilar '+i+'"><a class="similar-genre" href="'+data4[i]+'">similar genres</a></li><li class="subFav last '+i+'"><a class="favourite-remove-genre" href="'+data4[i]+'">Unfavourite '+i+'</a></li></ul></li><ul class="related-genres" id="'+id+'"></ul>');
	  						}
	  				
	  				}
	  			});
		}
		function loadFriends() {
			searchesState.wrapElement.find('div#ajaxSocial').show();
			dataString="&getFriends="+fb_id;
			urlString = "bin/functions.php?callback=?"+dataString+"";
			$.getJSON(urlString, function(data) {
				if (data == "") { 
  					//console.log("no data");
  				} else {
  					//console.log("data");
  					//alert("data");
  					$('div#ajaxSocial').hide();
  					
  					$('body').find('ul.friends').empty();
  					for (var i = 0, l = data.length; i < l; i++) {
  					//http://graph.facebook.com/fb_id/picture http://graph.facebook.com/'+data[i]['id']+'/picture
  							$('body').find('ul.friends').append('<li class="friend '+i+'"><span class="artistSpan"><img src="http://graph.facebook.com/'+data[i]['id']+'/picture" alt="'+data[i]['name']+'\'s profile picture"></div></span><a class="friends-profile" href="'+data[i]['id']+'">'+data[i]['name']+'</a><ul class="artist-options"><li class="friends-profile '+i+'"><a class="friends-profile" href="'+data[i]['id']+'">'+data[i]['name']+'\'s profile</a></li></ul></li><ul class="profile" id="profile-'+data[i]['id']+'"><li class="empty"><ul class="likes-'+data[i]['id']+'"></ul></li><li class="empty"><ul class="favTracks tracks-'+data[i]['id']+'"></ul></li><li class="empty"><ul class="genres-'+data[i]['id']+'"></ul></li></ul>');
  						}
  				
  				}
			});
		}
		//gets soundcloud results for this artist
		$('a.artist-search').live('click', function(e) {
			e.preventDefault();
			homeLink.trigger('click');
			homeState.wrapElement.find('div.ajaxLoading').show();
			$('ul.recom').fadeOut();
			search = $(this).attr('href');
			search.replace("#", "");
			search.replace("%27", "/'");
			//console.log(search);
			searchT = "tracks";
			from = "artist-search";
			jsonRequest(searchT, search, from);
			return false;
		});
		$('a.artistsTracks').live('click', function(e) {
			e.preventDefault();
			homeLink.trigger('click');
			homeState.wrapElement.find('div.ajaxLoading').show();
			$('ul.recom').fadeOut();
			name = $(this).attr('href');
			name.replace("#", "");
			name.replace("%27", "/'");
			searchT = "users/"+name+"/tracks";//users/{id}/tracks
			search="";
			from = "artist-search";
			jsonRequest(searchT, search, from);
			return false;
		});
		$('a.genre-search').live('click', function(e) {
			e.preventDefault();
			homeLink.trigger('click');
			homeState.wrapElement.find('div.ajaxLoading').show();
			$('ul.recom').fadeOut();
			search = "&genres=";
			search += $(this).attr('href');
			search.replace("#", "");
			search.replace("%27", "/'");
			search += "&order=hotness";
			//console.log(search);
			searchT = "tracks";
			from = "genre";
			jsonRequest(searchT, search, from);
			return false;
		});
		//profile-+id
		$('a.friends-profile').live('click', function(e) {
			e.preventDefault();
			
			var id = $(this).attr('href');
			var name = $(this).html();
			$('ul.profile-'+id).append('<div class="ajaxLoading" id="ajax'+id+'"><h3 style="text-align:center;" class="search-class2">Loading Friends</h3><div class="wheel"><img alt="loading" src="css/images/loading.gif" style="width:64px; margin:0 auto;"></div></div>');

			function loadAll(id, name) {
				var name = name;
				dataString = "";
				dataString = "&getLikes=true&artist=true";
				<?php if ($anon != "") { ?>
				dataString+="&anon=true";
				dataString+="&ip=<?php echo $usersIP;  ?>";
				<?php } else { ?>
				dataString+="&ip="+id;
				<?php } ?>
		  		 	urlString = "bin/process.php?callback=?"+dataString+"";
		  			//only perform when variables have been collected
		  			////console.log(urlString);
		  			$.getJSON(urlString,{}, function(data4) {
		  			
		  				if (data4 == "") { 
		  					//alert("no data");
		  					if (isEmpty(data4)) {
		  						$('body').find('ul.likes-'+id).empty();
								$('body').find('ul.likes-'+id).append('<li class="favArtLi"><span class="emptyspan">Query Empty</span></li>');
								$('#ajax'+id).hide();
							}
		  				} else {
			  				
		  					$('body').find('ul.likes-'+id).empty();
		  					$('body').find('ul.likes-'+id).append("<h3 class='search-class'>"+name+": Favourite Artists</h3>");
							
		  					for (var i = 0, l = data4.length; i < l; i++) {
		  						$('body').find('ul.likes-'+id).append('<li class="favArtLi '+i+'"><span class="artistSpan"><div style="background: url(\''+data4[i].image+'\');height:40px;"></div></span><a class="artist-search" href="'+data4[i].name+'">'+data4[i].name+'</a><ul class="artist-options"><li class="subSearch '+i+'"><a class="artist-search" href="'+data4[i].name+'">artist\'s songs</a></li><li class="subSimilar '+i+'"><a class="similar-search" href="'+data4[i].name+'">similar artists</a></li><li class="subFav '+i+'"><a class="favourite-add" href="'+data4[i].name+'">favourite '+i+'</a></li></ul></li>');
		  					}
		  				
		  				}
		  			});
				dataString = "";
				dataString = "&getArtists=true&artist=true";
				<?php if ($anon != "") { ?>
				dataString+="&anon=true";
				dataString+="&ip=<?php echo $usersIP;  ?>";
				<?php } else { ?>
				dataString+="&ip="+id;
				<?php } ?>
		  		 	urlString2 = "bin/process.php?callback=?"+dataString+"";
		  			//only perform when variables have been collected
		  			//console.log(urlString2);
		  			$.getJSON(urlString2,{}, function(data6) {
			  		
		  				if (data6 == "") { 
		  					//alert("no data");
		  					if (isEmpty(data6)) {
		  						$('body').find('ul.tracks-'+id).empty();
								$('body').find('ul.tracks-'+id).append('<li class="favArtLi"><span class="emptyspan">Query Empty</span></li>');
								$('#ajax'+id).hide();
							}
		  				} else {
		  					//ids
		  					//console.log(data6);
		  					var newString = "&ids="+data6;
		  					var urlRequest = "http://api.soundcloud.com/tracks.json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
		  					//console.log(urlRequest);
		  					$.getJSON(urlRequest, function(data) {

		  						if (data == "") { 
		  							
		  							if (isEmpty(data)) {
		  								$('body').find('ul.likes-'+id).empty();
										$('body').find('ul.likes-'+id).append('<li class="favArtLi"><span class="emptyspan">Query Empty</span></li>');
										$('#ajax'+id).hide();
									}	
		  						} else { 
		  							var count = 0;
		  							var purchase;
		  							$('body').find('ul.tracks-'+id).empty();
		  							$('body').find('ul.tracks-'+id).append("<h3 class='search-class'>"+name+" Favourite Tracks</h3>");
		  			  				
		  						for ( keyVar in data) {
			  						purchase = "";
		  							   if (data[keyVar].purchase_url == null) {
											purchaseClass = "";
		  							   } else {
			  							   purchaseClass = "purchase";
											purchase = '<li class="purchaseTrack"><a target="_blank" class="purchaseTrack" href="'+ data[keyVar].purchase_url +'">Purchase Track '+ data[keyVar].title +'</a></li>';
		  							   }
		  							   //console.log(data[keyVar]);
		  							 $('body').find('ul.tracks-'+id).append('<li class="favTrackLi '+count+'"><span class="artistSpan"><div style="background: url('+data[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="track-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'">'+purchase+'<li class="queueTrack '+count+'"><a class="queueTrack" href="'+ data[keyVar].uri +'">queue track '+ data[keyVar].title +'</a></li><li class="artistsTracks"><a class="artistsTracks" href="'+data[keyVar].user_id+'">'+data[keyVar].user_id+'</a></li><li class="favTrack last '+count+'"><a class="favTrack" href="'+data[keyVar].id+'">Favourite this Track '+count+'</a></li></ul></li>');
									count ++;
		 	  					}
		  						}
		  						
		  			   	    });
		  					
		  				}
		  			});
				dataString = "&getGenres=true";
				<?php if ($anon != "") { ?>
				dataString+="&anon=true";
				dataString+="&ip=<?php echo $usersIP;  ?>";
				<?php } else { ?>
				dataString+="&ip="+id;
				<?php } ?>
		  		 	urlString = "bin/lastfm.php?callback=?"+dataString+"";
		  		 	
		  			//only perform when variables have been collected
		  			////console.log(urlString);
		  			$.getJSON(urlString,{}, function(data4) {
		  			
		  				if (data4 == "") { 
		  					//alert("no data");
		  					if (isEmpty(data4)) {
		  						$('body').find('ul.genres-'+id).empty();
								$('body').find('ul.genres-'+id).append('<li class="favArtLi"><span class="emptyspan">Query Empty</span></li>');
								$('#ajax'+id).hide();
							}
		  				} else {
			  				
		  					$('body').find('ul.genres-'+id).empty();
		  					$('body').find('ul.genres-'+id).append("<h3 class='search-class'>"+name+": Genres</h3>");
  			  				
		  				for (var i = 0, l = data4.length; i < l; i++) {
		  					var id = data4[i];
			  				id = id.replace(" ", "_");
			  				id = id.replace(" ", "_");
			  				id = id.replace(" ", "_");
			  				id = id.replace(" ", "_");
		  							$('body').find('ul.genres-'+id).append('<li class="favGenre '+i+'"><span>'+data4[i]+'</span><ul class="artist-options"><li class="genreSearch '+i+'"><a class="genre-search" href="'+data4[i]+'">songs in genre</a></li><li class="genreSimilar '+i+'"><a class="similar-genre" href="'+data4[i]+'">similar genres</a></li><li class="subFav last '+i+'"><a class="favourite-remove-genre" href="'+data4[i]+'">Unfavourite '+i+'</a></li></ul></li><ul class="related-genres" id="'+id+'"></ul>');
		  						}
		  				
		  				}
		  			});
			}
			loadAll(id, name);
			$(this).closest("ul.artist-options").empty().append("<li class='up-button'><a href='"+id+"' class='up-button'>"+name+"'s profile</a></li>");

			//end
			return false;
		});
		// list
		$('a.save-playlist').live('click', function(e) {
			e.preventDefault();
			window_head.find("h1").html("Save Playlist");
			window_body.find("p.window-p").empty().html("<form action='#' method='post'><input class='textbox' type='text' placeholder='Playlist Name' name='playlistname' id='playlist-name'/></form>");
			window_body.find("p.window-p").append("<a class='playlist-save' href='#'>save</a>");
			window_a.hide();
			windowBox.fadeIn();
			
			
			return false;
		});
		$('a.playlist-save').live('click', function(e) {
			e.preventDefault();
			windowBox.fadeOut();
			window_a.show();
			var myPlaylist = new Object();
			myPlaylist = playList;
			var name = $('input#playlist-name').val();
			<?php if ($anon != "") { ?>
			$.post('bin/functions.php', {'playlist[]': myPlaylist, "name" : name, "anon" : true, 'ip': "<?php echo $usersIP;  ?>" }, function(data){
	  	  		   // do something with received data!
	  	  		   //console.log(data);
				window_head.find("h1").html("PlayList Saved");
				window_body.find("p.window-p").empty().html("Playlist was saved to your playlists");
				windowBox.fadeIn();
	  	  		});
			<?php } else { ?>
			$.post('bin/functions.php', {'playlist[]': playList, "name" : name, 'ip': fb_id }, function(data){
	  	  		   // do something with received data!
	  	  		   //console.log(data);
				window_head.find("h1").html("PlayList Saved");
				window_body.find("p.window-p").empty().html("Playlist was saved to your playlists");
				windowBox.fadeIn();
	  	  		});
			<?php } ?>
			
			return false;
		});
		$('a.up-button').live('click', function(e) {
			e.preventDefault();
			var id = $(this).attr('href');
			var name = $(this).html();
			name = name.replace("'s profile", "");
			$('body').find("ul.likes-"+id).empty();
			$('body').find("ul.tracks-"+id).empty();
			$('body').find("ul.genres-"+id).empty();
			$(this).closest("ul.artist-options").empty().append('<li class="friends-profile"><a class="friends-profile" href="'+id+'">'+name+'\'s profile</a></li>');
			
			return false;
		});
		function jsonRequest(requestString, additionalParams, from) {
			
			$('ul.recom').hide();
			$('ul.others').hide();
			$('body').find('div.ajaxLoading').show();
			additionalParams = additionalParams.replace("&genres=", "");
			additionalParams = additionalParams.replace("&order=hotness", "");
			var orig = additionalParams;
			
			var newString = "&q=" + additionalParams;
			var urlRequest = "http://api.soundcloud.com/"+ requestString + ".json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
			//console.log(urlRequest);
			$.getJSON(urlRequest, function(data) {

				if (data == "") { 
						$('.json').append('<p>Query Empty</p>');	
				} else { 
					
				var count = 0;
				if (orig == "") {
					orig = "Artist: "+data[0]['user']['username'];
				}
				homeContent.find('h2').replaceWith('<h2 class="search-class">Search Results For: "'+ orig +'"</h2>');
					 //$('#main-form > h2').replaceWith('');
				
					// $('.the-content > ul.new').replaceWith('<ul class="new"></ul>');
				homeContent.find('ul.new').empty();
				for ( keyVar in data) {
					purchase = "";
					   if (data[keyVar].purchase_url == null) {
							purchaseClass = "";
					   } else {
						   purchaseClass = "purchase";
							purchase = '<li class="purchaseTrack"><a target="_blank" class="purchaseTrack" href="'+ data[keyVar].purchase_url +'">Purchase Track '+ data[keyVar].title +'</a></li>';
					   }
					   homeContent.find('ul.new').append('<li><span class="artistSpan"><div style="background: url('+data[keyVar].artwork_url+');-o-background-size:100%; -webkit-background-size:100%; -khtml-background-size:100%;  -moz-background-sizewidth:100%;height:40px;"></div></span><a class="track-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'</a><ul class="artist-options '+purchaseClass+'"><li class="queueTrack 0"><a class="queueTrack" href="'+ data[keyVar].uri +'">queue track '+data[keyVar].title+'</a></li><li class="favTrack last 0"><a class="favTrack" href="'+data[keyVar].id+'">Favourite this Track</a></li>'+purchase+'</ul></li>');
				}
				}
				
	   	    });
			$('body').find('div.ajaxLoading').hide();
		}

		repeatBtn.click(function(e) {
			e.preventDefault();
			repeat();
			return false;
		});
		searchType.click(function(e) {
			e.preventDefault();
			submenu.toggle('fast', function() {});
			return false;
		});
		submenu.hover( function () {
		      //$(this).children().fadeIn('fast');
	    }, 
	    function () {
	    	submenu.fadeOut('fast');
	    });
		/* profileBtn.click(function(e) {
			jsonRequest("users/spawn", "");
		}); */
		
		//buttons
		volumeSlider.slider({
		      orientation: "vertical",
		      range: "min",
		      value:100,
		      min: 0,
		      max: 100,
		      step: 5,
		      slide: function(event, volumeUi) {
		          var volume = volumeUi;
		          volumeFunct(volumeUi.value);
		      }
		 });
		

		$('a.track-load').live('click',function(event){
			  event.preventDefault();
			  var name = $(this).attr('href');
			  var id = name.replace("http://api.soundcloud.com/tracks/", "");
			  //console.log("name: "+name);
				if (id != playList[playList.length]) {
					playList.push(id);
					currentPlaying = playList.length-1;
					fromPlayLoad = true;
				  	thePlayList = playList;
				  	//console.log("track pushed: "+currentPlaying);
				}
			  if (playerReady == false) {
				  playPause.trigger('click');
				  firstPlay = false;
			  } else {
				  theNewPlayer.api_load(name);
				  theNewPlayer.api_play();
			  }
		});
		$('a.play-load').live('click',function(event){
			  event.preventDefault();
			  fromPlayLoad = true;
			  var name = $(this).attr('href');
			  var id = name.replace("http://api.soundcloud.com/tracks/", "");
			  //console.log("name: "+name);
			  for (var i in playList) {
					playList[i] = playList[i].replace("http://api.soundcloud.com/tracks/", "");
					playList[i] = playList[i]+'';
					id = id+'';
					if (playList[i] == id) {
						//set currentPlaying to the 
						currentPlaying = i;
					}
				}
			  	thePlayList = playList;
			  if (playerReady == false) {
				  fromSearch = true;
				  playPause.trigger('click');
				  firstPlay = false;
			  } else {
				  //console.log("playerReady: "+playerReady);
				  theNewPlayer.api_load(name);
				  theNewPlayer.api_play();
			  }
		});
		//playList-load
		$('a.playList-load').live('click',function(event){
			event.preventDefault();
			fromPlayLoad = true;
			var id = $(this).attr('class');
			id = id.replace("playList-load ", "");
			var track = $(this).attr('href');
			var name = track.replace('http://api.soundcloud.com/tracks/', '');
			//otherPlaylists[id];
			selectedPlaylist = id;
			//change theplaylist to relevant other playlist
			
			for (var i in otherPlaylists[selectedPlaylist]) {
				otherPlaylists[selectedPlaylist][i] = otherPlaylists[selectedPlaylist][i].replace("http://api.soundcloud.com/tracks/", "");
				otherPlaylists[selectedPlaylist][i] = otherPlaylists[selectedPlaylist][i]+'';
				id = id+'';
				if (otherPlaylists[selectedPlaylist][i] == name) {
					//set currentPlaying to the 
					currentPlaying = i;
					//console.log("currentPlaying: "+currentPlaying);
				}
			}
			thePlayList = otherPlaylists[selectedPlaylist];
			  if (playerReady == false) {
				  fromSearch = true;
				  playPause.trigger('click');
				  firstPlay = false;
			  } else {
				  theNewPlayer.api_load(track);
				  theNewPlayer.api_play();
			  }
			  return false;
		});
		volumeBtn.click(function (e) {
			e.preventDefault();
		    volumeSliderContainer.toggle('fast', function() {
		        // Animation complete.
		    });
		    return false;
		  });
		
		moreBtn.click(function(e) {
			e.preventDefault();
			if (isiPhone()) {
				playerContainer.animate({
					  "top" : "-=100px" 
					  }, 'fast', function() {
					    // Animation complete.
					  });
			} else {
				playerContainer.animate({
					  "height" : "+=100px" 
					  }, 'fast', function() {
					    // Animation complete.
					  });
			}
			  
			  moreBtn.hide();
			  lessBtn.show();
			  return false;
			});
		lessBtn.click(function(e) {
			e.preventDefault();
			if (isiPhone()) {
				playerContainer.animate({
					  "top" : "+=100px" 
					  }, 'fast', function() {
					    // Animation complete.
					  });
			} else {
			  playerContainer.animate({
			  "height" : "-=100px"
			  }, 'fast', function() {
			    // Animation complete.
			  });
			}
			  lessBtn.hide();
			  moreBtn.show();
			  return false;
			});
		//buttons
		$("#playSlider").slider({
		      orientation: "horizontal",
		      range: "min",
		      min: 0,
		      max: 100,
		      step: 1,
		      value: theNewPlayer.trackPosition,
		      slide: function(event, playUi) {
		          skipFunct(playUi.value);
		      }
		 });
		
		submit.click(function() {
			
			 if ((mainSearchInput.val()) == "") {
				mainError.show();
			 } else {
				 var search = mainSearchInput.val();
				 //console.log(search);
				 searchT = "tracks";
				 if (selectedType == "artists") {
					 searchT = "users";
				 }
				 //console.log(searchT);
				 jsonRequest(searchT, search, "");
			 }
			 var justSearched = mainSearchInput.val();

			 return false;
		});
		
		prevBtn.click(function(e) {
			e.preventDefault();
			if(thePlayList) {
				
				prevTrack = currentPlaying - 1;
				if (prevTrack < 0) {
					prevTrack = 0;
				}
				theNewPlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[prevTrack]);
				currentPlaying --;
				
			}
			 return false;
		});
		nextBtn.click(function(e) {
			e.preventDefault();
			if(thePlayList) {
				
				nextTrack = currentPlaying + 1;
				if (nextTrack > thePlayList.length-1) {
					window_head.find("h1").html("Error");
					window_body.find("p.window-p").empty().html("No More Tracks in the Playlist");
					windowBox.fadeIn();
				} else {
					theNewPlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[nextTrack]);
					currentPlaying ++;
				}
			}
			 return false;
		});
		
		function volumeFunct (volume) {
			//sets
			theNewPlayer.api_setVolume(volume);
		}
		function skipFunct (skip) {
			//sets play
			trackDuration = theNewPlayer.api_getTrackDuration();
			var trackY = trackDuration / 100;
			var skipTo = skip * trackY; //skip to seconds
			var loadedAmt = theLoad;
			var loadedSeconds = loadedAmt * trackY;//amount loaded in seconds
			if (skipTo > loadedSeconds) {
				skipTo = loadedSeconds;
			}
			theNewPlayer.api_seekTo(skipTo);
		}
		
		amount.val(mySlider.slider("value"));
		//if repeat button is on, turn off
		function repeat() {
			if (repeatBtn.hasClass('on')) {
				repeatBtn.removeClass('on').addClass('off');
			} else {
				repeatBtn.removeClass('off').addClass('on');
			}
		}
		
		 
	 

	$(document).bind('onMediaTimeUpdate.scPlayer', function(event){
		var trackPosition = ((1-((event.duration - event.position)/event.duration))*100);
		$("#playSlider").slider( "option", "value", trackPosition );
	});

	$(document).bind('onPlayerInit.scPlayer', function(event){
		$('a.loading').removeClass('loading').addClass('loaded');
	});

	soundcloud.addEventListener('onMediaPlay.scPlayer', function(player, data) {
		  isPlaying = false;
	});

	soundcloud.addEventListener('onMediaSeek', function(player, data) {
		  //console.log('seeking in the track!');
	});

	//when it's buffering
	soundcloud.addEventListener('onMediaBuffering', function(player, data) {
		theLoad = data.percent;
	});

	soundcloud.addEventListener('onMediaDoneBuffering', function(player, data) {
		  isLoaded = true;
	});
	function doHighlist(name) {
		var name = name;
		$('body').find("li.playlistLi").each(function (i) {
			var theHref = $(this).child("a.play-load").attr("href");
			//console.log("name: "+name+", theHref: "+theHref);
			if(theHref == name) {
				
				$(this).css({"background" : "#cafdee"});
			} else {
				$(this).css({"background" : "url(images/ulLight.png) repeat scroll top white"});
			}
		}); 
	}
	soundcloud.addEventListener('onMediaStart', function(player, data) {
		//console.log("data"+data);
		doHighlist(data.mediaUri);
		theNewPlayer = soundcloud.getPlayer('scPlayerEngine');
	});

	
	//handles what to do when the song is ready
	soundcloud.addEventListener('onPlayerReady', function(player, data) {
		
		if (playerReady) {
			fromPlayLoad = false;
		}
		theNewPlayer = soundcloud.getPlayer('scPlayerEngine');
		//console.log(theNewPlayer);
			player.api_play();
			theData = data;
			currentTrack = data.mediaUri;
			
	});
	
	//handles the event of the song finishing
	soundcloud.addEventListener('onMediaEnd', function(player, data) {
		playerReady = true;
		//if fromPlayload, the currentPlaying is the right number
		theNewPlayer = soundcloud.getPlayer('scPlayerEngine');
		if (fromPlayLoad) {
			//console.log(thePlayList[currentPlaying]);
			var string = "http://api.soundcloud.com/tracks/"+thePlayList[currentPlaying];
			fromPlayLoad = false;
			theNewPlayer.api_load(string);
			theNewPlayer.api_play();
		} else {
			//console.log("not fromPlayLoad");
			//console.log(thePlayList[currentPlaying+1]);
			if (thePlayList.length >= thePlayList[currentPlaying+1]) {
				//console.log("next track");
				theNewPlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[currentPlaying+1]);
			}
		}
		for (var i in thePlayList) {
			playCounter++;
			//returns playlist length
			//console.log(playCounter);
		}
		//console.log(playList.length);
		if (repeatBtn.hasClass('on')) {
			player.api_load(currentTrack);
			soundcloud.addEventListener('onPlayerReady', function(player, data) {
			player.api_play();
			});
			//console.log("repeat");
		}
		
	});
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
<div id="container" class="container">
<div id="content" class="row">
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
					<li class=""><span><a class="single" id="home_link" href="#main-content">home</a></span></li>
					<li class=""><span><a class="single" id="profile_link" href="#profile-content">profile</a></span></li>
					<li class=""><span><a id="playing_link" href="#playing-content">now playing</a></span></li>
					<li class=""><span><a class="single" id="social_link" href="#social-content">social</a></span></li>
					<li class=""><span><a class="single" id="account_link" href="#account-content">account</a></span></li>
					<li class="last"><span><a class="single" id="logout_link" href="logout.php?<?php if ($anon != "") { echo "anon=1"; }?>">Logout</a></span></li>
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
	<h3 class="search-class">Discovered Tracks</h3>
	<div id="ajaxTracks" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Tracks</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="favTracks">
	</ul>
	<h3 class="search-class">Favourite Artists</h3>
	<div id="ajaxArtists" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Artists</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="favArtists">
	</ul>
	<h3 class="search-class">Favourite Genres</h3>
	<div id="ajaxTags" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Genres</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="favGenres"></ul>
	</div>
	</div>
	</section>
	<section id="playing-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Now Playing</h1>
	</div>
	<div class="wrap">
	<h3 class="search-class">Now Playing</h3>
	<div id="ajaxPlaylist" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Playlist</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="playlist">
	</ul>
	<div id="ajaxOtherPlaylist" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Playlist</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="other-playlist">
	</ul>
	</div>
	</div>
	</section>
	<section id="social-content" class="twelvecol last menu-inner">
	<div class="the-content">
	<div class="content-header">
	<h1 class="header">Social</h1>
	
	</div>
	<div class="wrap">
	<h3 class="search-class">Friends on Discovery</h3>
	<div id="ajaxSocial" class="ajaxLoading"><h3 class="search-class2" style="text-align:center;">Loading Friends</h3><div class="wheel"><img style="width:64px; margin:0 auto;" src="css/images/loading.gif" alt="loading" /></div></div>
	<ul class="friends">
	</ul>
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
		<div class="threecol">
		<label class="account-label" id="account-name-label" for="first-name-input">First Name</label>
		</div>
		<div class="ninecol last">
		<input class="textbox" type="text" value="" name="first-name" id="first-name-input">
		</div>
		<div class="threecol">
		<label class="account-label" id="account-last-label" for="last-name-input">Last Name</label>
		</div>
		<div class="ninecol last">
		<input class="textbox" type="text" value="" name="last-name" id="last-name-input">
		</div>
		<div class="threecol">
		<label class="account-label" id="account-email-label" for="email-input">Email</label>
		</div>
		<div class="ninecol last">
		<input class="textbox" type="email" value="" name="email" id="email-input">
		</div>
		<div class="threecol">
		<label class="account-label" id="nickname-label" for="nickname-input">Nickname</label>
		</div>
		<div class="ninecol last">
		<input class="textbox" type="text" value="" name="name" id="nickname-input">
		</div>
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
	  <div class="clear"></div>
	  </footer>
	  
<div id="window"><div id="window-wrapper"><div id="window-head"><h1>Information</h1></div><div id="window-body"><p class="window-p">Your settings have been updated</p><p><a id="window-a" href="#">Okay</a></p></div></div></div>
<div id="fb-root"></div>
</body>
</html>
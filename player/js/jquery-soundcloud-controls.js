/*!
 * soundcloud javascript playback functionality
 * Author: Matthew Bridgeman
 */

//current seconds / total seconds 

//when the page is ready
$(document).ready(function() {
   // put all your jQuery goodness in here.
	//$('#player-object').hide();
	
	//generic
	//the player is a random object
	thePlayer = new Object();
	theData = new Object();
	thePlayList = new Object();
	theLoad = new Object(); //load percentage
	currentPlaying = new Object;
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
	var profileBtn = $("#profile-btn");
	var homeLink = $('.home_link');
	//play elements
	var repeatBtn = $("#repeat-btn");
	var volumeSlider = $("#volumeSlider");
	var volumeSliderContainer = $("#volumeSliderContainer");
	var playSlider = $("#playSlider");
	var prevBtn = $('#prev-btn');
	//dynamic RHS elements
	var scTrack = $('a.track-load');
	
	//logic vars
	var prevTrack;
	var nextTrack;
	var amount = $("#amount");
	var mySlider = $("#mySlider");
	var loading = $('a.loading');
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
	//elements to hide
	errors.hide();
	volumeSliderContainer.hide();
	mainError.hide();
	
	function jsonRequest(requestString, additionalParams) { 
		var orig = additionalParams;
		var newString = "&q=" + additionalParams;
		var urlRequest = "http://api.soundcloud.com/"+ requestString + ".json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
		
		$.getJSON(urlRequest, function(data) {

			if (data == "") { 
					$('.json').append('<p>Query Empty</p>');	
			} else { 
			var count = 0;
				 
				 $('#main-form > h2').replaceWith('<h2 class="search-class">Search Results For: "'+ orig +'"</h2>');
				 
				 $('#the-content > ul.new').replaceWith('<ul class="new"></ul>');
				 $.each(data, function() {
					 
					 for (var i = count, l = 10; i < l; i++) {
						 //console.log(data[i].id);
						 $('#the-content ul.new').append('<li><a class="track-load" href="'+ data[i].uri +'" >'+ data[i].title +'<a/></li>');
						 count++;
					 }
					 
					});

			}
			
   	    });
			
	}

	repeatBtn.click(function(e) {
		e.preventDefault();
		repeat();
	});
	
	profileBtn.click(function(e) {
		jsonRequest("users/spawn", "");
	});
	
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
	

	scTrack.live('click',function(event){
		  event.preventDefault();
		  currentPlaying ++;
		  thePlayer.api_load(this.href);
		});
	
	
	volumeBtn.hover(function () {
	    volumeSliderContainer.fadeIn('slow');
	  },
	  function () {
		volumeSliderContainer.fadeOut('fast');
	  }
	});
	
	//buttons
	playSlider.slider({
	      orientation: "horizontal",
	      range: "min",
	      min: 0,
	      max: 100,
	      step: 1,
	      value: thePlayer.trackPosition,
	      slide: function(event, playUi) {
	          skipFunct(playUi.value);
	      }
	 });
	
	homeLink.click(function() {
		  //$('#the-content .wrap').replaceWith( 
				  
		 $.ajax({
		      type: "GET",
		      url: "main-search.php",
		      success: function(data) {
		    	  $('#the-content').replaceWith(data);
		        }
		     });
		  
		});
	submit.click(function() {
		
		 if ((mainSearchInput.val()) == "") {
			mainError.show();
		 } else {
			 var data = mainSearchInput.val();
			 jsonRequest("tracks", data );
		 }
		 var justSearched = mainSearchInput.val();

		 return false;
	});
	
	prevBtn.click(function() {
		if(thePlayList) {
			
			prevTrack = currentPlaying - 1;
			if (prevTrack < 0) {
				prevTrack = 0;
			}
			thePlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[prevTrack]);
			//var nextTrack = currentPlaying + 1;
			currentPlaying --;
			/*for( i = 0; l = thePlayList.length; i++)  {
				  var lastTrack = thePlayList[i];
				  if(playlist[i] == currentTrack) {
					  console.log("this "+playlist[i]+" is current track");
				  }
				  
			}*/
		}
		 return false;
	});
	nextBtn.click(function() {
		if(thePlayList) {
			
			nextTrack = currentPlaying + 1;
			
			thePlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[nextTrack]);
			//var nextTrack = currentPlaying + 1;
			currentPlaying ++;
			//console.log(nextTrack);
			/*for( i = 0; l = thePlayList.length; i++)  {
				  var lastTrack = thePlayList[i];
				  if(playlist[i] == currentTrack) {
					  console.log("this "+playlist[i]+" is current track");
				  }
				  
			}*/
			
		}
		 return false;
	});
	
	function volumeFunct (volume) {
		//sets
		thePlayer.api_setVolume(volume);
	}
	function skipFunct (skip) {
		//sets play
		trackDuration = thePlayer.api_getTrackDuration();
		var trackY = trackDuration / 100;
		var skipTo = skip * trackY; //skip to seconds
		var loadedAmt = theLoad;
		var loadedSeconds = loadedAmt * trackY;//amount loaded in seconds
		if (skipTo > loadedSeconds) {
			skipTo = loadedSeconds;
		}
		//var secondsBuf = ;
		
		//console.log(skipTo);
		//var trackPercentage = trackDuration / 100;
		//var trackPosition = player.api_getTrackPosition();
		//var trackPos = (trackPosition / trackDuration);
		thePlayer.api_seekTo(skipTo);
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
	
	 
 });

$(document).bind('onMediaTimeUpdate.scPlayer', function(event){
	var trackPosition = ((1-((event.duration - event.position)/event.duration))*100);
	playSlider.slider( "option", "value", trackPosition );
});

$(document).bind('onPlayerInit.scPlayer', function(event){
	loading.removeClass('loading').addClass('loaded');
});

soundcloud.addEventListener('onMediaPlay.scPlayer', function(player, data) {
	  isPlaying = false;
});

soundcloud.addEventListener('onMediaSeek', function(player, data) {
	  console.log('seeking in the track!');
});

//when it's buffering
soundcloud.addEventListener('onMediaBuffering', function(player, data) {
	//console.log(data.percent);
		//slider = 
	  //if slider is more than data.percent { playback = (overall seeking percentage)  }
	theLoad = data.percent;
	//console.log(loadedAmt);
  //console.log(trackPosition);
  //playPos = trackPos;
	//thePlayer = player;
});

soundcloud.addEventListener('onMediaDoneBuffering', function(player, data) {
	  isLoaded = true;
});

soundcloud.addEventListener('onMediaStart', function(player, data) {
	
});


//handles what to do when the song is ready
soundcloud.addEventListener('onPlayerReady', function(player, data) {

		//the player object equals whatever the player is
		thePlayer = player;
		theData = data;
		
		//once the player is ready, divide total seconds by 100
		
		
		//stopper = slider
		
		// -|-----
		currentTrack = data.mediaUri;
		//for (i = 0; l = playList.length; i++) {
		//currentPlaying ++;
		//}
		if (currentTrack != thePlayList[thePlayList.length]) {
			//console.log($(playList).last());
			playList.push(data.mediaId);
		}
		thePlayList = playList;
		console.log(currentPlaying);
		//player.api_setVolume(0);
		
});

//handles the event of the song finishing
soundcloud.addEventListener('onMediaEnd', function(player, data) {
	//console.log(data);
	
	//if repeat button is on, repeat track
	if (repeatBtn.hasClass('on')) {
		player.api_load(currentTrack);
		soundcloud.addEventListener('onPlayerReady', function(player, data) {
		player.api_play();
		});
		  console.log("repeat");
	}  
});

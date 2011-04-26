/*!
 * soundcloud javascript playback functionality
 * Author: Matthew Bridgeman
 */

//when the page is ready
$(document).ready(function() {
 
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
	
	var homeLink = $('a#home_link');
	var homeContent = $('#main-content');
	var profileLink = $("a#profile_link");
	var profileContent = $('#profile-content');
	var nowPlayingLink = $("a#playing_link");
	var nowPlayingContent = $('#playing-content');
	var searchesLink = $("a#searches_link");
	var searchesContent = $('#searches-content');
	var accountLink = $("a#account_link");
	var accountContent = $('#account-content');
	var searchType = $('a#type-a');
	var searchT;
	//play elements
	var repeatBtn = $("#repeat-btn");
	var volumeBtn = $("#volume-btn");
	var volumeSlider = $("#volumeSlider");
	var volumeSliderContainer = $("#volumeSliderContainer");
	var playSlider = $("#playSlider");
	var prevBtn = $('#prev-btn');
	var nextBtn = $('#next-btn');
	var playPause = $('a.sc-play');
	var moreBtn = $('#moreBtn');
	var lessBtn = $('#lessBtn');
	var playerContainer = $('#player-container');
	
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
	var typeState = function (menuItem) {
		this.menuItem = menuItem;
		this.menuItem.bind('click', function(e) {
			e.preventDefault();
			selectedType = $(this).html();
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
		var stateID = this.id;
		this.menuItem.bind('click', function(e) {
			e.preventDefault();
			console.log(wrapElement);
			changeState(wrapElement);
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
	
	function changeState(state) {
		for (var i = 0; i < states.length; i++) {
			if (states[i].wrapElement == state) {
				//change the class to on and it's wrapElement to on, if children is equal to true turn them on
				states[i].menuItem.addClass("selected");
				states[i].selected = true;
				states[i].menuItem.removeClass("unselected");
				if (states[i].wrapElement) {
					states[i].wrapElement.show();
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
		}
	}
	//gets soundcloud results for this artist
	$('a.artist-search').live('click', function(e) {
		e.preventDefault();
		$('ul.recom').fadeOut();
		search = $(this).attr('href');
		search.replace("#", "");
		search.replace("%27", "/'");
		console.log(search);
		searchT = "tracks";
		from = "artist-search";
		jsonRequest(searchT, search, from);
	});
	function jsonRequest(requestString, additionalParams, from) { 
		$('ul.recom').hide();
		$('ul.others').hide();
		$('body').find('div.ajaxLoading').show();
		var orig = additionalParams;
		var newString = "&q=" + additionalParams;
		var urlRequest = "http://api.soundcloud.com/"+ requestString + ".json?consumer_key=KrpXtXb1PQraKeJETJL7A"+ newString;
		
		$.getJSON(urlRequest, function(data) {

			if (data == "") { 
					$('.json').append('<p>Query Empty</p>');	
			} else { 
				
			var count = 0;
			homeContent.find('h2').replaceWith('<h2 class="search-class">Search Results For: "'+ orig +'"</h2>');
				 //$('#main-form > h2').replaceWith('');
			
				// $('.the-content > ul.new').replaceWith('<ul class="new"></ul>');
			homeContent.find('ul.new').empty();
			for ( keyVar in data) {
				   console.log(data[keyVar]);
				   homeContent.find('ul.new').append('<li><a class="track-load" href="'+ data[keyVar].uri +'" >'+ data[keyVar].title +'<a/></li>');
			}
					

			}
			
   	    });
		$('body').find('div.ajaxLoading').hide();
	}

	repeatBtn.click(function(e) {
		e.preventDefault();
		repeat();
	});
	searchType.click(function(e) {
		e.preventDefault();
		submenu.toggle('fast', function() {});
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
		  currentPlaying ++;
		  if (firstPlay) {
			  fromSearch = true;
			  playPause.trigger('click');
			  firstPlay = false;
			  
		  }
			  thePlayer.api_load(this.href);
		});
	
	
	volumeBtn.click(function () {
	    volumeSliderContainer.toggle('fast', function() {
	        // Animation complete.
	    });
	  });
	
	moreBtn.click(function(e) {
		e.preventDefault();
		  playerContainer.animate({
		  "height" : "+=100px" 
		  }, 'fast', function() {
		    // Animation complete.
		  });
		  moreBtn.hide();
		  lessBtn.show();
		});
	lessBtn.click(function(e) {
		e.preventDefault();
		  playerContainer.animate({
		  "height" : "-=100px"
		  }, 'fast', function() {
		    // Animation complete.
		  });
		  lessBtn.hide();
		  moreBtn.show();
		});
	//buttons
	$("#playSlider").slider({
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
	
	/* homeLink.click(function() {
		 $.ajax({
		      type: "GET",
		      url: "main-search.php",
		      success: function(data) {
		    	  $('#the-content').replaceWith(data);
		        }
		     });
		  
		}); */
	submit.click(function() {
		
		 if ((mainSearchInput.val()) == "") {
			mainError.show();
		 } else {
			 var search = mainSearchInput.val();
			 console.log(search);
			 searchT = "tracks";
			 if (selectedType == "artists") {
				 searchT = "users";
			 }
			 console.log(searchT);
			 jsonRequest(searchT, search, "");
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
			currentPlaying --;
			
		}
		 return false;
	});
	nextBtn.click(function() {
		if(thePlayList) {
			
			nextTrack = currentPlaying + 1;
			
			thePlayer.api_load("http://api.soundcloud.com/tracks/"+thePlayList[nextTrack]);
			//var nextTrack = currentPlaying + 1;
			currentPlaying ++;
			
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
	$('a.loading').removeClass('loading').addClass('loaded');
});

soundcloud.addEventListener('onMediaPlay.scPlayer', function(player, data) {
	  isPlaying = false;
});

soundcloud.addEventListener('onMediaSeek', function(player, data) {
	  console.log('seeking in the track!');
});

//when it's buffering
soundcloud.addEventListener('onMediaBuffering', function(player, data) {
	theLoad = data.percent;
});

soundcloud.addEventListener('onMediaDoneBuffering', function(player, data) {
	  isLoaded = true;
});

soundcloud.addEventListener('onMediaStart', function(player, data) {
});


//handles what to do when the song is ready
soundcloud.addEventListener('onPlayerReady', function(player, data) {

		player.api_play();
		//the player object equals whatever the player is
		thePlayer = player;
		theData = data;
		console.log(thePlayer);
		currentTrack = data.mediaUri;
		if (fromSearch) {
			console.log("fromSearch");
			playList.push($('a.track-load').attr('href'));
			fromSearch = false;
		}
		if (currentTrack != thePlayList[thePlayList.length]) {
			playList.push(data.mediaId);
		}
		thePlayList = playList;
		console.log(currentPlaying);
		
});

//handles the event of the song finishing
soundcloud.addEventListener('onMediaEnd', function(player, data) {
	if (repeatBtn.hasClass('on')) {
		player.api_load(currentTrack);
		soundcloud.addEventListener('onPlayerReady', function(player, data) {
		player.api_play();
		});
		console.log("repeat");
	}  
});

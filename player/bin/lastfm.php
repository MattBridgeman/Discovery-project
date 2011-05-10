<?php
// LastFM stuff update
//
// Name: Discovery
//
require_once("../../../includes/initialize.php");
require_once("lastfmapi/lastfmapi.php");
header("Content-type: application/json");
header('Access-Control: *');
header('Access-Control-Allow-Origin: *');
$today = date("d.m.y");
if(isset($_GET['getGenres'])) {
	$ip = $_COOKIE["discovery-ip"];
	$already = false;
	$array = array();
	$lastFM = array();
	$artistArr = array();
	$notArray = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['favourites']);
		if (is_array($unserialized)) {
		foreach ($unserialized as $v1) {
			array_push($array, $v1);
		}
		}
  	}
  	
  	//check genre hasn't been disguarded
  	if (isset($_GET['anon'])) {
  		$sql = "SELECT notgenres FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT notgenres FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
			$unserialized = unserialize($row['notgenres']);
			if (is_array($unserialized)) {
				foreach ($unserialized as $v1) {
					array_push($notArray, $v1);
				}
			}
	  	}
	$authVars = array(
		'apiKey' => 'fbe282844ddb2e0bfef66790e0d012f1',
		'secret' => 'ba9da9d645aace82cefc040bdb8b7ee1',
		'username' => 'spawnDnB'
	);
	$config = array(
		'enabled' => true,
		'path' => './lastfmapi/',
		'cache_length' => 1800
	);
	// Pass the array to the auth class to eturn a valid auth
	$auth = new lastfmApiAuth('setsession', $authVars);
	
	// Call for the album package class with auth data
	$apiClass = new lastfmApi();
	$artistClass = $apiClass->getPackage($auth, 'artist', $config);

	foreach($array as $item)  {
		// Setup the variables
		$methodVars = array(
			'artist' => $item,
			'page' => 1,
			'limit' => 3
		);
		if ( $tags = $artistClass->getTopTags($methodVars) ) {
			//print_r($tags);
			$extra = 0;
			for($i = 0; $i < 4+$extra; $i++) {
				$theTag = $tags[$i]['name'];
				if(!in_array($theTag, $notArray)) {
				    array_push($artistArr, $tags[$i]['name']);
				} else {
					$extra++;
				}
			}
			$extra = 0;
	}
	}
	if (isset($_GET['anon'])) {
  		$sql = "SELECT genres FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT genres FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
			$unserialized = unserialize($row['genres']);
			if (is_array($unserialized)) {
				foreach ($unserialized as $v1) {
					if (!in_array($v1, $artistArr)) {
						array_push($artistArr, $v1);
					}
				}
			}
	  	}
	$array = serialize($artistArr);
	if(isset($_GET['anon'])) {
		$sql2 = "UPDATE users SET genres = '".$array."' WHERE ip = '$ip'";
	} else {
		$sql2 = "UPDATE users SET genres = '".$array."' WHERE fb_id = '$ip'";
	}
  	$send = $database->query($sql2);
	$json = json_encode($artistArr);
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}
if(isset($_GET['notGenre'])) {
	$ip = $_GET['ip'];
	$genre = $_GET['genre'];
	$already = false;
	$array = array();
	$lastFM = array();
	$artistArr = array();
	$notArray = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT notgenres FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT notgenres FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['notgenres']);
		if(is_array($unserialized)) {
			foreach ($unserialized as $v1) {
				array_push($array, $v1);
				if ($v1 == $genre) {
					$already = true;
				}
			}
		}
  	}
  	if(!$already) {
  		
  		array_push($array, $genre);
  		$array = serialize($array);
  		if(isset($_GET['anon'])) {
			$sql2 = "UPDATE users SET notgenres = '".$array."' WHERE ip = '$ip'";
		} else {
			$sql2 = "UPDATE users SET notgenres = '".$array."' WHERE fb_id = '$ip'";
		}
  		$send = $database->query($sql2);
  		if ($send) {
  		if (isset($_GET['anon'])) {
  			$sql = "SELECT notgenres FROM users WHERE ip = '$ip'";
  		} else {
  			$sql = "SELECT notgenres FROM users WHERE fb_id = '$ip'";
  		}
  		$send = $database->query($sql);
  		//$array = array("sql" => unserialize($send));
  		while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
  			$favourites = unserialize($row['notgenres']);
  		}
		//$json = $database->while_query($send);
  		$json = json_encode($favourites);
  		}
  	} else {
  		$json = json_encode(array("error" => "unforeseen error"));
  	}
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';	
} else if(isset($_GET['getRelatedGenres'])) {
	$ip = $_GET['ip'];
	$tag = $_GET['tag'];
	$already = false;
	$array = array();
	$lastFM = array();
	$artistArr = array();
	$notArray = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT genres FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT genres FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['genres']);
		if(is_array($unserialized)) {
			foreach ($unserialized as $v1) {
				array_push($array, $v1);
			}
		}
  	}
  	
  	//check genre hasn't been disguarded
  	if (isset($_GET['anon'])) {
  		$sql = "SELECT notgenres FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT notgenres FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
			$unserialized = unserialize($row['notgenres']);
			if (is_array($unserialized)) {
				foreach ($unserialized as $v1) {
					array_push($notArray, $v1);
				}
			}
	  	}
	$authVars = array(
		'apiKey' => 'fbe282844ddb2e0bfef66790e0d012f1',
		'secret' => 'ba9da9d645aace82cefc040bdb8b7ee1',
		'username' => 'spawnDnB'
	);
	$config = array(
		'enabled' => true,
		'path' => './lastfmapi/',
		'cache_length' => 1800
	);
	// Pass the array to the auth class to eturn a valid auth
	$auth = new lastfmApiAuth('setsession', $authVars);

	$apiClass = new lastfmApi();
	$tagClass = $apiClass->getPackage($auth, 'tag', $config);
	
	// Setup the variables
	$methodVars = array(
		'tag' => $tag
	);
	
	if ($tags = $tagClass->getSimilar($methodVars)) {
		$extra = 0;
		for($i = 0; $i < 5+$extra; $i++) {
			$theTag = $tags[$i]['name'];
			if(!in_array($theTag, $notArray) && !in_array($theTag, $array)) {
			    array_push($artistArr, $tags[$i]['name']);
			} else {
				$extra++;
			}
		}
		$extra = 0;
	}
	
	$json = json_encode($artistArr);
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}

?>
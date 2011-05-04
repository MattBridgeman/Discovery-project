<?php
require_once("../../../includes/initialize.php");
require_once("lastfmapi/lastfmapi.php");
header("Content-type: application/json");
header('Access-Control: *');
header('Access-Control-Allow-Origin: *');
if(isset($_GET['getLikes'])) {
	$ip = $_GET['ip'];
	$already = false;
	$array = array();
	$lastFM = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['favourites']);
		foreach ($unserialized as $v1) {
			array_push($array, $v1);
			if ($v1 == $fav) {
				$already = true;
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
			'limit' => 1
		);
		if ($results = $artistClass->search($methodVars)) {
			//$print_r($results);
							
			foreach ($results as $k1 => $v1) {
			
				if (is_array($results[$k1])) {
				
					foreach ($results[$k1] as $k2 => $v2) {
						if (is_array($results[$k1][$k2])) {
						if ($results[$k1][$k2]["name"]) {
								//print_r($results[$k1][$k2]["name"]);
								$artistArr["name"] = $results[$k1][$k2]["name"];
							}
							foreach ($results[$k1][$k2] as $k3 => $v3) {
								if (is_array($results[$k1][$k2][$k3])) {
									foreach ($results[$k1][$k2]["image"] as $k4 => $v4) {
										$image = str_replace("/34/", "/252/", $results[$k1][$k2]["image"][$k4]);
										//echo($image."<br/>");
										if ($image != "") {
											$artistArr["image"] = $image;
										}
									}
								}
							}
							array_push($lastFM, $artistArr);
							unset($artistArr);
							$artistArr = array();
						}
						
					}
					//array_push($lastFM, $artistArr);
					
				}
				
			}
			
		}
		else {
			//$ret = $artistClass->error['code'];
			array_push($lastFM, array("error" => $artistClass->error['desc']));
		}
	}
	$json = json_encode($lastFM);
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}


if(isset($_GET['unfavourite'])) {
	$fav = trim($_GET['unfavourite']);
	$fav = str_replace("%20", " ", $fav);
	$ip = $_GET['ip'];
	$already = false;
	$array = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['favourites']);
		foreach ($unserialized as $v1) {
			
			if ($v1 == $fav) {
				$already = true;
				//don't add it back to the list
			} else {
				array_push($array, $v1);
			}
		}
  	}
  	//if send worked and it was found in the array
  	if ($send && $already) {
  		//don't need array push because we don't want it in the list
  		//array_push($array, $fav);
  		$array = serialize($array);
	  	if(isset($_GET['anon'])) {
			$sql2 = "UPDATE users SET favourites = '".$array."' WHERE ip = '$ip'";
		} else {
			$sql2 = "UPDATE users SET favourites = '".$array."' WHERE fb_id = '$ip'";
		}
  		$send = $database->query($sql2);
  		if ($send) {
  		if (isset($_GET['anon'])) {
  			$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  		} else {
  			$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  		}
  		$send = $database->query($sql);
  		//$array = array("sql" => unserialize($send));
  		while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
  			$favourites = unserialize($row['favourites']);
  		}
		//$json = $database->while_query($send);
  		$json = json_encode($favourites);
  		
  		}
  	} else {
  		$json = json_encode(array("error" => "unforeseen error"));
  	}
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}

if(isset($_GET['getRelatedGenres'])) {
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

if(isset($_GET['favourite'])) {
	$fav = trim($_GET['favourite']);
	$ip = $_GET['ip'];
	$already = false;
	$array = array();
	if (isset($_GET['anon'])) {
  		$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  	} else {
  		$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  	}
  	$send = $database->query($sql);
	while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
		$unserialized = unserialize($row['favourites']);
		foreach ($unserialized as $v1) {
			array_push($array, $v1);
			if ($v1 == $fav) {
				$already = true;
			}
		}
  	}
  	if ($send && $already != true) {
  		//$array = array("alix perez", "SP:MC");
  		array_push($array, $fav);
  		$array = serialize($array);
	  	if(isset($_GET['anon'])) {
			$sql2 = "UPDATE users SET favourites = '".$array."' WHERE ip = '$ip'";
		} else {
			$sql2 = "UPDATE users SET favourites = '".$array."' WHERE fb_id = '$ip'";
		}
  		$send = $database->query($sql2);
  		if ($send) {
  		if (isset($_GET['anon'])) {
  			$sql = "SELECT favourites FROM users WHERE ip = '$ip'";
  		} else {
  			$sql = "SELECT favourites FROM users WHERE fb_id = '$ip'";
  		}
  		$send = $database->query($sql);
  		//$array = array("sql" => unserialize($send));
  		while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
  			$favourites = unserialize($row['favourites']);
  		}
		//$json = $database->while_query($send);
  		$json = json_encode($favourites);
  		
  		}
  	} else {
  		if ($already == true) {
  			$json = json_encode(array("error" => "already liked that artist"));
  		} else {
  			$json = json_encode(array("error" => "unforeseen error"));
  		}
  	}
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}
?>
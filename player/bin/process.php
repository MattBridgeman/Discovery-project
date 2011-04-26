<?php
// Database update
//
// Name: Discovery
//
require_once("../../../includes/initialize.php");
require_once("lastfmapi/lastfmapi.php");
header("Content-type: application/json");
header('Access-Control: *');
header('Access-Control-Allow-Origin: *');
$today = date("d.m.y");
if(isset($_GET['fb_id'])) {
	$name = trim($_GET['name']);
	$fb_id = trim($_GET['fb_id']);
	$first_name = trim($_GET['first_name']);
	$last_name = trim($_GET['last_name']);
	$email = trim($_GET['email']);
	
	$sql = "SELECT * FROM users WHERE fb_id='$fb_id'";
	$send = $database->query($sql);
	if(mysql_num_rows($send) < 1) {
		$sql = "INSERT INTO users (name,fb_id,first_name,last_name,email,date_added)
		VALUES ('$name', '$fb_id', '$first_name', '$last_name', '$email', '$today')";
			$send = $database->query($sql);
			if ($send) {
				$message = "true";
			} else {
				$message = "false";
			}
		 	
  		} else {
  		$sql2 = "UPDATE users SET last_login = '$today'
WHERE fb_id = '$fb_id'";
  		$send = $database->query($sql2);
			if ($send) {
				$message = "true ip";
			} else {
				$message = "false ip";
			}
  		}
  		$json = $database->while_query($sql);
  		$json = json_encode($json);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} else if(isset($_GET['ip'])) {
	$ip = $_GET['ip'];
	
	$sql = "SELECT * FROM users WHERE ip='$ip'";
	$send = $database->query($sql);
	if(mysql_num_rows($send) < 1) {
		$sql = "INSERT INTO users (ip, date_added)
		VALUES ('$ip', '$today')";
			$send = $database->query($sql);
			if ($send) {
				$message = "true ip";
			} else {
				$message = "false ip";
			}
		 	
  		} else {
  			$sql2 = "UPDATE users SET last_login = '$today'
WHERE ip = '$ip'";
  		$send = $database->query($sql2);
			if ($send) {
				$message = "true ip";
			} else {
				$message = "false ip";
			}
  		}
  		$json = $database->while_query($sql);
  		$json = json_encode($json);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} else if(isset($_GET['last_name'])) {
	$last = trim($_GET['last_name']);
	$first = trim($_GET['first_name']);
	$email = trim($_GET['email']);
	$nickname = trim($_GET['nickname']);
	$ip = trim($_GET['login']);
	$type = "";
	if (isset($_GET['anon'])) {
		$sql2 = "UPDATE users SET last_name = '".$_GET['last_name']."',first_name = '".$_GET['first_name']."',email = '".$_GET['email']."',name = '".$_GET['nickname']."' WHERE ip = '$ip'";
	} else {
		$sql2 = "UPDATE users SET last_name = '".$_GET['last_name']."',first_name = '".$_GET['first_name']."',email = '".$_GET['email']."',name = '".$_GET['nickname']."' WHERE fb_id = '$ip'";
	}
	
  	$send = $database->query($sql2);
  	if ($send) {
  		if (isset($_GET['anon'])) {
  			$sql = "SELECT * FROM users WHERE ip = '$ip'";
  		} else {
  			$sql = "SELECT * FROM users WHERE fb_id = '$ip'";
  		}
  		$array = array("sql" => $sql2);
  		$send = $database->query($sql);
		$json = $database->while_query($sql);
  		$json = json_encode($json);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
  	} else {
  		$message = array('error' => 'error');
		$json = json_encode($message);
	  	$message = $json;
	  	echo $_GET['callback'] . ' (' . $message . ');';
		echo $message;
  	}
  	
} else if(isset($_GET['likes'])) {
$resultsArr = array();
	$artistArr = array();
	$lastFM = array();
	$likes = $_GET['likes'];
	$likes = stripslashes($likes);
	$likes = str_replace("%20", " ", $likes);
	$likes = str_replace("'", "%27", $likes);
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
	
	$explode = (explode(',', $likes));
	for($i = 0; $i < (count($explode)-1); $i++)  {
		// Setup the variables
		$methodVars = array(
			'artist' => $explode[$i],
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
		//$lastFM = json_encode(array("artist" => $likesID));
		//json_encode($lastFM);
		
	}
	$resultsArr = json_encode($lastFM);
	$print = $_GET['callback'] . ' (' . $resultsArr . ');';
	echo $print;
} else if(isset($_GET['similar'])) {
	$resultsArr = array();
	$artistArr = array();
	$lastFM = array();
	$likes = $_GET['similar'];
	$likes = stripslashes($likes);
	$likes = str_replace("%20", " ", $likes);
	$likes = str_replace("'", "%27", $likes);
	// Get the session auth data
	//$file = fopen('../auth.txt', 'r');
	// Put the auth data into an array
	$authVars = array(
		'apiKey' => 'fbe282844ddb2e0bfef66790e0d012f1',
		'secret' => 'ba9da9d645aace82cefc040bdb8b7ee1',
		'username' => 'spawnDnB',
		'sessionKey' => '',
		'subscriber' => ''
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
	
	// Setup the variables
	$methodVars = array(
		'artist' => $likes,
	);
	
	if ( $artists = $artistClass->getSimilar($methodVars) ) {
				//$print_r($results);
								
				foreach ($artists as $k1 => $v1) {
					if ($artists[$k1]["name"]) {
						//print_r($results[$k1][$k2]["name"]);
						$artistArr["name"] = $artists[$k1]["name"];
						$image = str_replace("/34/", "/252/", $artists[$k1]["image"]);
						$artistArr["image"] = $image;
					}
					array_push($lastFM, $artistArr);
					unset($artistArr);
					$artistArr = array();
				}
		
	}
	else {
		array_push($lastFM, array("error" => $artistClass->error['desc']));
	}
		
	$resultsArr = json_encode($lastFM);
	$print = $_GET['callback'] . ' (' . $resultsArr . ');';
	echo $print;
} else {
	$message = array('error with post');
	$json = json_encode($message);
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
}
?>
<?php
	
require_once("lastfmapi/lastfmapi.php");
$resultsArr = array();
$artistArr = array();
$lastFM = array();
$likes = $_GET['similar'];
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
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($lastFM);
	echo '</pre>';
	
?>
<?php

// Include the API
require '../lastfmapi/lastfmapi.php';

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
	'path' => '../lastfmapi/',
	'cache_length' => 1800
);
// Pass the array to the auth class to eturn a valid auth
$auth = new lastfmApiAuth('setsession', $authVars);

// Call for the album package class with auth data
$apiClass = new lastfmApi();
$artistClass = $apiClass->getPackage($auth, 'artist', $config);

// Setup the variables
$methodVars = array(
	'artist' => 'Chase & Status'
);

if ( $artist = $artistClass->getInfo($methodVars) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($artist);
	echo '</pre>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>
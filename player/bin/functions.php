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
$friendArr = array();
$array = array();
if(isset($_POST['theFB'])) {
	$friends = $_POST['response'];
	$true = true;
	for($i = 0; $i < count($friends); $i++) {
		foreach ($friends[$i]['data'] as $data) {
			if ($true) {
				//do somehting then equal true
				array_push($array, array("name" => $data['name']));
				$true = false;
			} else {
				//do something else 
				array_push($array, array("id" => $data['id']));
				array_push($friendArr, $array);
				unset($array);
				$array = array();
				$true = true;
			}
			
		}
		//$array = array("id" => $friends[$i]['data'], "name" => "");
			
	}
	$ip = $_POST['theFB'];
	
	$sql = "SELECT * FROM users WHERE fb_id = '$ip'";
	$send = $database->query($sql);
	if(mysql_num_rows($send) < 1) {
		//there's been an issue finding the row
  		} else {
  		$serialized = base64_encode(serialize($friendArr));
  		$sql2 = "UPDATE users SET friends = '$serialized'
WHERE fb_id = '$ip'";
  		$send = $database->query($sql2);
  		}
  		$sql = "SELECT friends FROM users WHERE fb_id = '$fb_id'";
  		$json = $database->while_query($sql);
	  	
	 	function myUnserialize( $txt ) { 
		   return unserialize(gzuncompress(base64_decode($txt))); 
		}
		$unserialized = unserialize($json['friends']);
  		$json = json_encode($unserialized);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} else if(isset($_POST['getFriends'])) {
	$ip = $_POST['getFriends'];
	$users = array();
	$friendIDs = array();
	$sql = "SELECT friends FROM users WHERE fb_id = {$ip}";
	$send = $database->query($sql);
	$while = $database->while_query($sql);
	$sql2 = "SELECT * FROM users";
	$send2 = $database->query($sql2);
	while($row = mysql_fetch_array($send2, MYSQL_ASSOC)) {
		array_push($users, array($row['fb_id'], $row['id'], $row['ip']));
  	}
	if(mysql_num_rows($send) < 1) {
		//there's been an issue finding the row
  		} else {
  		while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
			$unserialized = unserialize(base64_decode($row['friends']));
	  	}
	  	if (is_array($unserialized)) {
	  		//foreach friends as v1
  			foreach ($unserialized as $v1) {
  			//each $v1['id'] is a friend
				if ($v1[1]['id'] == $ip) {
					//do nothing as this is them
				} else {
					//foreach discovery user
					foreach ($users as $user) {
						if ($user[0] == $v1[1]['id']) {
							array_push($friendIDs, array("name" => $v1[0]['name'], "id" => $v1[1]['id']));
						}
					}
				}
			}
	  	}
  		}
  		$json = json_encode($friendIDs);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} else if(isset($_POST['playlist'])) {
	$ip = $_POST['ip'];
	$name = $_POST['name'];
	$playList = $_POST['playlist'];
	
	$users = array();
	$friendIDs = array();
	$createPlaylist = array();
	if (isset($_POST['anon'])) {
		$sql = "SELECT playlists FROM users WHERE ip = '$ip'";
	} else {
		$sql = "SELECT playlists FROM users WHERE fb_id = {$ip}";
	}
	$send = $database->query($sql);
	$while = $database->while_query($sql);
	//serialize $serialized = base64_encode(serialize($friendArr));
	//unserialize $unserialized = unserialize(base64_decode($row['friends']));
	$unserialized = unserialize(base64_decode($while[0]['playlists']));
	$array = array();
	array_push($createPlaylist, array("name" => $name, "list" => $playList));
	array_push($array, $createPlaylist);
	if(is_array($unserialized)) {
	array_push($array, $unserialized);
	}
	//serialize and update the database
	$serialized = base64_encode(serialize($array));
	if(isset($_POST['anon'])) {
			$sql2 = "UPDATE users SET playlists = '".$serialized."' WHERE ip = '$ip'";
		} else {
			$sql2 = "UPDATE users SET playlists = '".$serialized."' WHERE fb_id = '$ip'";
		}
  		$send = $database->query($sql2);
	if (isset($_POST['anon'])) {
		$sql = "SELECT playlists FROM users WHERE ip = '$ip'";
	} else {
		$sql = "SELECT playlists FROM users WHERE fb_id = {$ip}";
	}
	$send = $database->query($sql);
	$while = $database->while_query($sql);
	//serialize $serialized = base64_encode(serialize($friendArr));
	//unserialize $unserialized = unserialize(base64_decode($row['friends']));
	$unserialized = unserialize(base64_decode($while[0]['playlists']));
	
  		$json = json_encode($unserialized);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} else if(isset($_GET['ip'])) {
	$ip = $_GET['ip'];
	if (isset($_GET['anon'])) {
		$sql = "SELECT playlists FROM users WHERE ip = '$ip'";
	} else {
		$sql = "SELECT playlists FROM users WHERE fb_id = {$ip}";
	}
	$send = $database->query($sql);
	$while = $database->while_query($sql);
	//serialize $serialized = base64_encode(serialize($friendArr));
	//unserialize $unserialized = unserialize(base64_decode($row['friends']));
	$unserialized = unserialize(base64_decode($while[0]['playlists']));
	
  		$json = json_encode($unserialized);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
  		print_r($row);
} else if(isset($_GET['getFriends'])) {
	$ip = $_GET['getFriends'];
	$users = array();
	$friendIDs = array();
	$sql = "SELECT friends FROM users WHERE fb_id = {$ip}";
	$send = $database->query($sql);
	$while = $database->while_query($sql);
	$sql2 = "SELECT * FROM users";
	$send2 = $database->query($sql2);
	while($row = mysql_fetch_array($send2, MYSQL_ASSOC)) {
		array_push($users, array($row['fb_id'], $row['id'], $row['ip']));
  	}
	if(mysql_num_rows($send) < 1) {
		//there's been an issue finding the row
  		} else {
  		while($row = mysql_fetch_array($send, MYSQL_ASSOC)) {
			$unserialized = unserialize(base64_decode($row['friends']));
	  	}
	  	if (is_array($unserialized)) {
	  		//foreach friends as v1
  			foreach ($unserialized as $v1) {
  			//each $v1['id'] is a friend
				if ($v1[1]['id'] == $ip) {
					//do nothing as this is them
				} else {
					//foreach discovery user
					foreach ($users as $user) {
						if ($user[0] == $v1[1]['id']) {
							array_push($friendIDs, array("name" => $v1[0]['name'], "id" => $v1[1]['id']));
						}
					}
				}
			}
	  	}
  		}
  		$json = json_encode($friendIDs);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
} ?>
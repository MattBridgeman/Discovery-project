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
if(isset($_POST['theFB'])) {
	$friends = $_POST['response'];
	$ip = $_POST['theFB'];
	
	$sql = "SELECT * FROM users WHERE fb_id = '$ip'";
	$send = $database->query($sql);
	if(mysql_num_rows($send) < 1) {
		//there's been an issue finding the row
  		} else {
  		$serialized = serialize($friends);
  		$sql2 = "UPDATE users SET friends = '$serialized'
WHERE fb_id = '$ip'";
  		$send = $database->query($sql2);
  		}
  		$sql = "SELECT friends FROM users WHERE fb_id = '$fb_id'";
  		$json = $database->while_query($sql);
  		$json = json_encode($json);
  		$message = $json;
  		echo $_GET['callback'] . ' (' . $message . ');';
}
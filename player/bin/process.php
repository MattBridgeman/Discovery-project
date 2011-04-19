<?php
// Database update
//
// Name: Discovery
//
require_once("../../../includes/initialize.php");
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
  	
} else {
	$message = array('error with post');
	$json = json_encode($message);
  	$message = $json;
  	echo $_GET['callback'] . ' (' . $message . ');';
	echo $message;
}
?>
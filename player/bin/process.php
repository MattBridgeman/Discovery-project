<?php
// Awesome Facebook Application
//
// Name: Discovery
//
require_once("../../includes/initialize.php");

if(isset($_POST['fb_id'])) {
	$name = trim($_POST['name']);
	$fb_id = trim($_POST['fb_id']);
	$sql = "SELECT * FROM users WHERE fb_id='$fb_id'";
	$send = $database->query($sql);
	if(mysql_num_rows($send) < 1) {
		$sql = "INSERT INTO users (name,fb_id)
		VALUES ('$name', '$fb_id')";
			$send = $database->query($sql);
			if ($send) {
				$message = "true";
			} else {
				$message = "false";
			}
		 	
  		} else {
  			$message = "Entry exists";
  		}
  		echo $message;
} else {
	$message = 'error with post';
	echo $message;
}
?>
<?php
require_once("../../../includes/initialize.php");
$today = date("d.m.y");
if (isset($_COOKIE["ip"])) {
		//they have logged in before
		$cookie = $_COOKIE["ip"];
		$sql = "SELECT * FROM users WHERE ip={$cookie}";
		$send = $database->query($sql);
		while($row = mysql_fetch_array($send)) {
			if ($row['ip'] != "") {
				$number++;
			}
		}
		if(is_numeric($number)) {
		echo $number+1;
		}
		echo "count: ".$count;
		echo "ip: ".$ip;
	} else {
		$sql = "SELECT ip FROM users";
		$number = 0;
		$send = $database->query($sql);
		while($row = mysql_fetch_array($send)) {
			if ($row['ip'] != "") {
				$number++;
			}
		}
		if(is_numeric($number)) {
		echo $number;
		}
		/*
		$value = $count+1;
		setcookie("ip", $value, time()+60*60*24*365);
		$sql = "INSERT INTO users (ip,date_added)
		VALUES ('$value', '$today')";
		$send = $database->query($sql);
		if ($send) {
			echo "sent";
			
		}*/
	}
?>
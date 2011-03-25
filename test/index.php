<?php 

require_once("../../includes/initialize.php");

if (!$session->is_logged_in()) { 
	redirect_to("login/login.php"); 
} else {
	redirect_to("player/index.php");
}

?>
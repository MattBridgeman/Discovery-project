<?php
require_once('../../../includes/initialize.php');
if (!$session->is_logged_in()) { 
	redirect_to("../login/login.php"); 
} else if (isset($_GET['logout'])) {
	$session->logout();
	redirect_to("../login/login.php"); 
}

?>
<?php
//define core paths
//definedd as absolute paths, to make sure that require_once
//works
//this means forward slash
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

//if the constant: site_root isnt defined, define as the root from the HD
//if the constant LIB path isnt, define as site_root/includes
defined('SITE_ROOT') ? null :
	define('SITE_ROOT', DS.'home6'.DS.'thedisc4');

defined('LIB_PATH') ? null : 
	define('LIB_PATH', SITE_ROOT.DS.'includes');

	//config first
require_once (LIB_PATH.DS.'config.php');
	//basic functions
require_once (LIB_PATH.DS.'functions.php');
	//core objects
require_once (LIB_PATH.DS.'session.php');
require_once (LIB_PATH.DS.'database.php');

require_once (LIB_PATH.DS.'database_object.php');

	//database related classes
require_once (LIB_PATH.DS.'user.php');


?>
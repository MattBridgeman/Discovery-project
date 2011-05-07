<?php 
require_once("../../../includes/initialize.php");
require_once("lastfmapi/lastfmapi.php");
$set = false;
if (isset($_GET['similar'])) {
	$similar = $_GET['similar']; 
	$set = true;
}


?>
<?php if ($set){ ?>
<iframe style="border: 0;" src="test.php?similar=<?php echo $similar; ?>" width="100%" height="100%">
  <p>Your browser does not support iframes.</p>
</iframe>
<?php } else { ?>
	<p>An Error has occured <a href="self.close()">Close This Window</a></p>
<?php } ?>
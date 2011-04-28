<?php
	
require_once("lastfmapi/lastfmapi.php");
$likes = $_GET['similar'];
$likes = explode(",next,", $likes);
	$mainArtists = array();
	$artistArr = array();
	$lastFM = array();
	$authVars = array(
		'apiKey' => 'fbe282844ddb2e0bfef66790e0d012f1',
		'secret' => 'ba9da9d645aace82cefc040bdb8b7ee1',
		'username' => 'spawnDnB'
	);
	$config = array(
		'enabled' => true,
		'path' => './lastfmapi/',
		'cache_length' => 1800
	);
	// Pass the array to the auth class to eturn a valid auth
	$auth = new lastfmApiAuth('setsession', $authVars);
	
	// Call for the album package class with auth data
	$apiClass = new lastfmApi();
	$artistClass = $apiClass->getPackage($auth, 'artist', $config);
	
	for($i = 0; $i < count($likes); $i++)  {
		// Setup the variables
		$methodVars = array(
			'artist' => $likes[$i],
			'page' => 1,
			'limit' => 1
		);
		if ($results = $artistClass->search($methodVars)) {
			//$print_r($results);
							
			foreach ($results as $k1 => $v1) {
			
				if (is_array($results[$k1])) {
				
					foreach ($results[$k1] as $k2 => $v2) {
						if (is_array($results[$k1][$k2])) {
						if ($results[$k1][$k2]["name"]) {
								//print_r($results[$k1][$k2]["name"]);
								$artistArr["name"] = $results[$k1][$k2]["name"];
							}
							foreach ($results[$k1][$k2] as $k3 => $v3) {
								if (is_array($results[$k1][$k2][$k3])) {
									foreach ($results[$k1][$k2]["image"] as $k4 => $v4) {
										$image = str_replace("/34/", "/252/", $results[$k1][$k2]["image"][$k4]);
										//echo($image."<br/>");
										if ($image != "") {
											$artistArr["image"] = $image;
										}
									}
								}
							}
							array_push($lastFM, $artistArr);
							unset($artistArr);
							$artistArr = array();
						}
						
					}
					//array_push($lastFM, $artistArr);
					
				}
				
			}
			
		}
		else {
			//$ret = $artistClass->error['code'];
			//array_push($lastFM, array("error" => $artistClass->error['desc']));
		}
		array_push($mainArtists, $lastFM);
		unset($lastFM);
		$lastFM = array();
		
	}

$resultsArr = array();
$artistArr = array();
$lastFM = array();
$likes = $_GET['similar'];
$likes = explode(",next,", $likes);
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
	'path' => './lastfmapi/',
	'cache_length' => 1800
);
// Pass the array to the auth class to eturn a valid auth
$auth = new lastfmApiAuth('setsession', $authVars);

// Call for the album package class with auth data
$apiClass = new lastfmApi();
$artistClass = $apiClass->getPackage($auth, 'artist', $config);
foreach($likes as $i) {
// Setup the variables
$methodVars = array(
	'artist' => $i,
	'limit' => 6
);

	if ( $artists = $artistClass->getSimilar($methodVars) ) {
				//$print_r($results);
								
				foreach ($artists as $k1 => $v1) {
					if ($artists[$k1]["name"]) {
						//print_r($results[$k1][$k2]["name"]);
						$artistArr["name"] = $artists[$k1]["name"];
						$image = str_replace("/34/", "/252/", $artists[$k1]["image"]);
						$artistArr["image"] = $image;
					}
					array_push($lastFM, $artistArr);
					unset($artistArr);
					$artistArr = array();
				}
		
	}
	else {
		//array_push($lastFM, array("error" => $artistClass->error['desc']));
	}
	array_push($resultsArr, $lastFM);
	unset($lastFM);
	$lastFM = array();
}
	/* echo '<b>Data Returned</b>';
	echo '<pre>';
	
	echo '</pre>'; */
	//print_r($mainArtists);
?>
<html> 
  <head> 
    <title>Matts test</title> 
    <script type="text/javascript" src="./protovis-r3.2.js"></script> 
    <style type="text/css"> 
 
body {
  margin: 0;
}
 
    </style> 
  </head> 
  <body> 
    <script type="text/javascript+protovis"> 
   
function getAlbumSize(perc){
	
	var maxAlbumSize = 130;
	var minAlbumSize = 20;
 
	return  minAlbumSize+(perc/100)*(maxAlbumSize-minAlbumSize);
 
}
 
function updateData(){
	var theData;

		theData = {
		  nodes:[
			<?php 
			$theNum = 0;
			foreach($mainArtists as $artist) { 
				foreach ($artist as $part) {
	?>
				{id:<?php echo $theNum; ?>,songName:"<?php echo $part['name']; ?>",art:"<?php echo $part['image']; ?>",size:<?php echo 70-($theNum * 10); ?>,visible:true},
			<? $theNum++;
				}
			} ?>
 			
			<?php
			//$resultsArr[0];
			$num = $theNum-1;
			for($i = 0; $i< count($resultsArr); $i++){
				foreach($resultsArr[$i] as $k2) {
				$num++;
					?>
		    {id:<?php echo $num; ?>,songName:"<?php echo $k2['name'];?>",artistName:"<?php echo $k2['name'];?>",art:"<?php echo $k2['image'];?>",size:10,visible:true},
		    <?php } 
		    }
		    ?>
		  ],
		  links:[
			<?php for($i = 0; $i < $theNum-1; $i++) { ?>
			{source:<?php echo $i; ?>, target:<?php echo $i+1; ?>, value:20},
			<?php } ?>
			<?php
			$layer = 0;
			for($i = $theNum; $i<=$num; $i++){
			
			if($i<=7){
				$layer = 0;
			}else if($i<=14){
				$layer = 1;
			}else if($i<=21){
				$layer = 2;
			}else if($i<=28){
				$layer = 3;
			}
				?>
			 {source:<?php echo $layer; ?>, target:<?php echo $i; ?>, value:10},
			<?php } ?>
			
		  ]
		};
	
	return theData;
}
 
function setupForce(theForce){
	var theData = updateData()
	theForce.nodes(theData.nodes)
    .links(theData.links);
    theForce.reset().root;
    
    return theForce;
}
 
var w = document.body.clientWidth,
    h = document.body.clientHeight,
    colors = pv.Colors.category19();
 
var vis = new pv.Panel()
    .width(w)
    .height(h)
    .fillStyle("white")
    .event("mousedown", pv.Behavior.pan())
    .event("mousewheel", pv.Behavior.zoom());
 
var force = vis.add(pv.Layout.Force)
    .springLength(90)
    .chargeMaxDistance(1000)
    .chargeMinDistance(10)
    .chargeConstant(-1000);
   // .springDamping(0)
   // .springConstant(0);
 
setupForce(force);
force.link.add(pv.Line);
 
/*
force.node.add(pv.Dot)
    .size(function(d) (d.group*100))
    .fillStyle(function(d) d.fix ? "brown" : colors(d.group))
    .strokeStyle(function() this.fillStyle().darker())
    .lineWidth(1)
    .title(function(d) d.nodeName)
    .event("mousedown", pv.Behavior.drag())
    .event("drag", force);
    */
 
force.node.add(pv.Image)
    .url(function(d) d.art)
    .title(function(d) d.songName)
    .width(function(d) getAlbumSize(d.size))
    .height(function(d) getAlbumSize(d.size))
    .top(function(d) d.y-getAlbumSize(d.size)/2)
    .left(function(d) d.x-getAlbumSize(d.size)/2)
    .event("mousedown", function(d) {
    	//runs when click
		console.log(d.artistName);
		window.location.href= "test.php?similar="+d.artistName+",next,<?php echo $_GET['similar']; ?>";
    });
    //.visible(function(d) d.visible);
 
 
vis.render();
 
 
vis.render();
 
    </script> 
  </body> 
</html>
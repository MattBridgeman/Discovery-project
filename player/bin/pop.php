<?php ?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
$(document).ready(function() {
	var theHeight = $('body').height();
	var theWidth = $('body').width();
	$('a.visualSearch').live('click', function(e) {
		e.preventDefault();
		title = $(this).attr('href');
		popuponclick(title);
	});
	function popuponclick(title) {
	      my_window = window.open("test.php?&ip=671360506&similar="+title,
	       "mywindow","status=1,width="+theWidth+",height=1000px");
	      
	    //  my_window.document.write('<h1>The Popup Window</h1>');
	}
	function Json(title) {
		alert("the title: "+title);
	}
});
</script>
</head>
<body>
<a class="visualSearch" href="Icicle">Visual Search</a>
</body>
</html>
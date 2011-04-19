<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Discovery Dev Blog <?php wp_title(); ?></title>
    
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="keywords" content="music, discovery, app, soundcloud api, matt bridgeman">
	<meta name="description" content="The Discovery Application is a music discovery service based on the soundcloud API">
	<meta name="author" content="Matthew Bridgeman">
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" media="screen" />
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('woo_feedburner_url') <> "" ) { echo get_option('woo_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
       
	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie6.css" media="screen" />
    <![endif]-->
    
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie7.css" media="screen" />
    <![endif]-->
    <!--[if IE]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
       
    <?php if ( is_single() ) wp_enqueue_script( 'comment-reply' ); ?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        
	<script type="text/javascript">
			$(document).ready(function() {
			});
	</script>
	<script type="text/javascript">    
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-22230288-1']);
	_gaq.push(['_trackPageview']);
	(function() {     var ga = document.createElement('script');
	ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script>
</head>
<body>
<div class="container">
<header id="mainHead">
	<h1><a href="<?php bloginfo('url'); ?>">The Discovery Application Blog</a></h1>
	<aside><p><a href="http://www.thediscoveryapp.com">Home</a> &#47; <a href="<?php bloginfo('url'); ?>">Dev Blog</a></p></aside>
	<div class="clear"></div>
</header>

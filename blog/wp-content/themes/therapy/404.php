<?php get_header(); ?>
<section id="mainSection" style="height: 212px;">
<div class="container">
<div id="macs"><h1>Page not found</h1></div>
</div>
</section>
<div class="container">
<section id="mainSection">
<?php if ( function_exists('yoast_breadcrumb') && (!(is_front_page()))) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
	} ?>
<article class="blog-article" id="<?php $title = get_the_title(); echo str_replace(" ", "-", $title);  ?>">
<h2><?php _e('Nothing Found',woothemes); ?></h2>
<?php _e('Sorry, no posts matched you criteria. Please try again.',woothemes); ?>
</article>
</section>
</div>    	
<?php get_footer(); ?>
<?php get_header(); ?>
<section id="mainSection">
<?php if ( function_exists('yoast_breadcrumb') && (!(is_front_page()))) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
	} ?>
<article class="blog-article" id="<?php $title = get_the_title(); echo str_replace(" ", "-", $title);  ?>">
<h2><?php _e('Nothing Found',woothemes); ?></h2>
<?php _e('Sorry, no posts matched you criteria. Please try again.',woothemes); ?>
</article>
</section>
<aside id="mainAside">

</aside>
</div>    	
<?php get_footer(); ?>
<?php get_header(); ?>
<section id="mainSection" style="height: 212px;">
<div class="container">
<div id="macs"><h1>Post</h1></div>
</div>
</section>
<div class="container">
<section id="mainAside">
<?php if ( function_exists('yoast_breadcrumb') && (!(is_front_page()))) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
	} ?>
<?php if (have_posts()) : $count = 0; ?>
<?php while (have_posts()) : the_post(); $count++; ?>
<article class="blog-article" id="<?php $title = get_the_title(); echo str_replace(" ", "-", $title);  ?>">
<h2><a title="<?php _e('Permanent Link to',woothemes); ?> <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('j F'); ?></time>
<?php comments_popup_link(__('0 Comments',woothemes), __('1 Comment',woothemes), __('% Comments',woothemes)); ?>
<?php
	the_content();
?>
<?php comments_template(); ?>
<div class="clear"></div>
</article>
<?php endwhile; else: ?>
	<h2><?php _e('Nothing Found',woothemes); ?></h2>
	<p><?php _e('Sorry, no posts matched you criteria. Please try again.',woothemes); ?></p>
<?php endif; ?> 
<?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
	<div class="prev"><?php previous_posts_link(__('&laquo; Newer Entries ',woothemes)) ?></div>
	<div class="next"><?php next_posts_link(__(' Older Entries &raquo;',woothemes)) ?></div>
	<div class="clear"></div>
<?php } ?>

</section>
</div>    	
<?php get_footer(); ?>
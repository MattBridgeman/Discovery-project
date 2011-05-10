<?php get_header(); ?>
<section id="mainSection" style="height: 212px;">
<div class="container">
<div id="macs"><h1>Archive</h1></div>
</div>
</section>
<div class="container">
<section id="mainAside">
<?php if ( function_exists('yoast_breadcrumb') && (!(is_front_page()))) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
	} ?>
<div class="blog-article">
<?php if (is_category()) { ?>
        	
	<h3><?php _e('Archive',woothemes); ?> | <?php echo single_cat_title(); ?> <?php $cat_obj = $wp_query->get_queried_object(); $cat_id = $cat_obj->cat_ID; echo '<a href="'; get_category_rss_link(true, $cat, ''); echo '">' . __('RSS feed for this section',woothemes) . '</a>'; ?></h3>        
           	
	<?php } elseif (is_day()) { ?>
	<h3><?php _e('Archive',woothemes); ?> | <?php the_time('F jS, Y'); ?></h3>

	<?php } elseif (is_month()) { ?>
	<h3><?php _e('Archive',woothemes); ?> | <?php the_time('F, Y'); ?></h3>

	<?php } elseif (is_year()) { ?>
	<h3><?php _e('Archive',woothemes); ?> | <?php the_time('Y'); ?></h3>
	
<?php } ?>
</div>
<?php if (have_posts()) : $count = 0; ?>
<?php while (have_posts()) : the_post(); $count++; ?>
<article class="blog-article" id="<?php $title = get_the_title(); echo str_replace(" ", "-", $title);  ?>">
<h2><a title="<?php _e('Permanent Link to',woothemes); ?> <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('j F'); ?></time>
<?php comments_popup_link(__('0 Comments',woothemes), __('1 Comment',woothemes), __('% Comments',woothemes)); ?>
<?php
	if ( get_option('woo_content_home') == "true" ) 
		the_content('[...]'); 
	else 
		the_excerpt();
?>
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
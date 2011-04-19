<?php
/*
Template Name: Archives Page
*/
?>

<?php get_header(); ?>
	
	<div id="breadcrumbs">
		
		<?php if ( get_option( 'woo_breadcrumbs' ) == 'true') { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
		
	</div><!-- /#breadcrumbs -->
	
	<div id="main">
	
		<div id="content">
			
			<div class="post">
					
				<h2 class="title"><?php bloginfo('title'); ?> <?php _e('Archives',woothemes); ?></h2>	
					
				<div class="entry">
						
					<h2><?php _e('The Last 30 Posts',woothemes); ?></h2>
				
						<ul>
							<?php query_posts('showposts=30'); ?>
							<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
							<?php $wp_query->is_home = false; ?>
							<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a> &gt; <?php the_time('j F Y') ?> <span class="comments">( <?php echo $post->comment_count ?> <?php _e('comments',woothemes); ?> )</span></li>
							<?php endwhile; endif; ?>	
						</ul>
					
					<h2><?php _e('Categories',woothemes); ?></h2>
				
						<ul>
							<?php wp_list_categories('title_li=&hierarchical=0&show_count=1') ?>	
						</ul>	
				
					<h2><?php _e('Monthly Archives',woothemes); ?></h2>
				
						<ul>
							<?php wp_get_archives('type=monthly&show_post_count=1') ?>	
						</ul>			
						
				</div><!-- /.entry -->
			
			</div><!-- /.post -->
            		
		</div><!-- /#content -->
		
	<?php get_sidebar(); ?>
       		
<?php get_footer(); ?>
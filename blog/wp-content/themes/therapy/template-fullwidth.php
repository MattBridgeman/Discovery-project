<?php
/*
Template Name: Fullwidth Page
*/
?>

<?php get_header(); ?>
	
	<div id="breadcrumbs">
		
		<?php if ( get_option( 'woo_breadcrumbs' ) == 'true') { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
		
	</div><!-- /#breadcrumbs -->
	
	<div id="main">
	
		<div id="content" class="fullwidth">
			
			<?php if (have_posts()) : $count = 0; ?>
			<?php while (have_posts()) : the_post(); $count++; ?>
		
				<div class="post">
		
					<h2 class="title"><a title="<?php _e('Permanent Link to',woothemes); ?> <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>

					<div class="entry">
							
						<?php the_content(); ?>
						
					</div><!-- /.entry -->
			
				</div><!-- /.post -->
				
			<?php endwhile; else: ?>
			
				<div class="post">
						
					<h2 class="title"><?php _e('Nothing Found',woothemes); ?></h2>

					<div class="entry">
							
						<p><?php _e('Sorry, no posts matched you criteria. Please try again.',woothemes); ?></p>
						
					</div><!-- /.entry -->
			
				</div><!-- /.post -->
			
			<?php endif; ?>
            		
		</div><!-- /#content -->
       		
<?php get_footer(); ?>
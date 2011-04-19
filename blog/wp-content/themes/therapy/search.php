<?php get_header(); ?>
		
	
	
	<div id="main">
	
		<div id="content">
			<div class="post2"></div>
            <h2 class="new"><?php _e('Search Results',woothemes); ?> for <?php printf(__('\'%s\''), $s) ?></h2>
            <div class="post2"></div>
			 <br />
			<?php if (have_posts()) : $count = 0; ?>
			<?php while (have_posts()) : the_post(); $count++; ?>
		
				<div class="post">
		
					<div style="float:left;margin-top:12px;text-align:right; width:105px;">
						<span class="date"><?php the_time('j F'); ?></span><br />
						<span class="comments"><?php comments_popup_link(__('0 Comments',woothemes), __('1 Comment',woothemes), __('% Comments',woothemes)); ?></span>
					</div>
                    
						<div style="padding-left:130px;">
					<h2 class="title"><a title="<?php _e('Permanent Link to',woothemes); ?> <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					
					<?php echo woo_get_embed('embed','520','292'); ?>

					<div class="entry">
							
							<?php woo_get_image('image',get_option('woo_thumb_width'),get_option('woo_thumb_height'),'thumb alignleft'); ?>
							
							<?php
							if ( get_option('woo_content_home') == "true" ) 
								the_content('[...]'); 
							else 
								the_excerpt(); 
							?>
						
					</div><!-- /.entry -->

					<div class="clear"></div>
						
					<div class="tags">
					
						<?php the_tags(__('Tags: ',woothemes), ', ', ''); ?>
					
					</div><!-- /.tags -->
				</div><!-- post entry -->
				</div><!-- /.post -->
				
			<?php endwhile; else: ?>
			<?php endif; ?> 
		
           	<?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
               	<div class="prev"><?php previous_posts_link(__('&laquo; Newer Entries ',woothemes)) ?></div>
        		<div class="next"><?php next_posts_link(__(' Older Entries &raquo;',woothemes)) ?></div>
                <div class="clear"></div>
           	<?php } ?>
            		
		</div><!-- /#content -->
		
		<?php get_sidebar(); ?>
       		
<?php get_footer(); ?>
<?php
/*
Template Name: Sitemap
*/
?>

<?php get_header(); ?>
	
	<div id="breadcrumbs">
		
		<?php if ( get_option( 'woo_breadcrumbs' ) == 'true') { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
		
	</div><!-- /#breadcrumbs -->
	
	<div id="main">
	
		<div id="content">
			
			<div class="post">
				
				<h2 class="title"><?php bloginfo('title'); ?> <?php _e('Sitemap',woothemes); ?></h2>
					
				<div class="entry">
						
					<h2><?php _e('Pages',woothemes); ?></h2>
								
						<ul><?php wp_list_pages('sort_column=menu_order&depth=0&title_li='); ?></ul>
								
					<h2><?php _e('Blog / News Categories',woothemes); ?></h2>
								
						<ul><?php wp_list_categories('depth=0&title_li=&show_count=1'); ?></ul>
								
					<h2><?php _e('Blog / News Monthly Archives',woothemes); ?></h2>
							
						<ul><?php wp_get_archives('type=monthly&limit=12'); ?> </ul>
								
				</div><!-- /.entry -->
			
			</div><!-- /.post -->
            		
		</div><!-- /#content -->
		
		<?php get_sidebar(); ?>
       		
<?php get_footer(); ?>
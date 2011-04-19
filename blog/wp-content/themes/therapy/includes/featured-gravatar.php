		<?php if ( get_option( 'woo_profile' ) <> "" ) { ?>
		
		<div class="gravatar">
			
			<img src="<?php echo get_option( 'woo_profile' ); ?>" alt="<?php bloginfo('title'); ?>" />
			
		</div><!-- /.gravatar -->
		
		<?php } ?>
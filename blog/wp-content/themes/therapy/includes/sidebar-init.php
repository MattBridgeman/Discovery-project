<?php

// Register widgetized areas

function the_widgets_init() {
    if ( !function_exists('register_sidebars') )
        return;

    	register_sidebar(array('name' => 'Sidebar (Above Tabber)','id' => 'sidebar-before','before_widget' => '<div id="%1$s" class="block widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
    	register_sidebar(array('name' => 'Sidebar (Below Tabber)','id' => 'sidebar-after','before_widget' => '<div id="%1$s" class="block widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));    	
    	register_sidebar(array('name' => 'Social Profiles Box (Sidebar)','id' => 'social-sidebar','before_widget' => '','after_widget' => '','before_title' => '','after_title' => ''));		
    	
    	// Register only if extended footer option has been activated
    	
    	if ( get_option( 'woo_extended_footer' ) == 'true' ) {
    	
    		register_sidebar(array('name' => 'Footer 1','id' => 'footer-1','before_widget' => '<div id="%1$s" class="block widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
    		register_sidebar(array('name' => 'Footer 2','id' => 'footer-2','before_widget' => '<div id="%1$s" class="block widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
    		register_sidebar(array('name' => 'Footer 3','id' => 'footer-3','before_widget' => '<div id="%1$s" class="block widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));    	    	
    	
    	}
    	
}

add_action( 'init', 'the_widgets_init' );

?>
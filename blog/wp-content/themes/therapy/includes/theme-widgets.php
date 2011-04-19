<?php

// =============================== Social Profiles Widget ======================================

class AS_SocialWidget extends WP_Widget {

   function AS_SocialWidget() {
       parent::WP_Widget(false, $name = __('Social Profiles', woothemes));    
   }


   function widget($args, $instance) {        
       extract( $args );
       
       $preset = $instance['preset'];
       $url = $instance['url'];
       $title = $instance['title'];
       $custom_title = $instance['custom'];

       ?>   		

			<li id="social-<?php echo $preset; ?>">
				<img src="<?php bloginfo('template_directory'); ?>/images/mediaicons/<?php echo $preset; ?>.png" alt="<?php echo $preset; ?>" />
				<a href="<?php echo $url; ?>" title="<?php echo $preset; ?>">
					<span class="site"><?php if ( $custom_title <> "" ) { echo $custom_title; } else { echo ucfirst( $preset ); } ?></span>
					<span class="url"><?php echo $url; ?></span>
				</a>
			</li>       

       <?php
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {                
       $preset = esc_attr($instance['preset']);
       $url = esc_attr($instance['url']);
       $title = esc_attr($instance['title']);
       $custom_title = esc_attr($instance['custom']);
       ?>
       <p>
       <label for="<?php echo $this->get_field_id('preset'); ?>"><?php _e('Preset:'); ?></label>
       <select name="<?php echo $this->get_field_name('preset'); ?>" class="widefat" id="<?php echo $this->get_field_id('preset'); ?>">
           <option value="select">-- <?php _e('Select Preset',woothemes); ?> --</option>
           <option value="brightkite" <?php if($preset== 'brightkite'){ echo "selected='selected'";} ?>>Brightkite</option>
           <option value="delicious" <?php if($preset== 'delicious'){ echo "selected='selected'";} ?>>Delicious</option>           
           <option value="deviantart" <?php if($preset== 'deviantart'){ echo "selected='selected'";} ?>>DeviantArt</option>           
           <option value="digg" <?php if($preset== 'digg'){ echo "selected='selected'";} ?>>Digg</option>
           <option value="facebook" <?php if($preset== 'facebook'){ echo "selected='selected'";} ?>>Facebook</option>
           <option value="flickr" <?php if($preset== 'flickr'){ echo "selected='selected'";} ?>>Flickr</option>
           <option value="friendfeed" <?php if($preset== 'friendfeed'){ echo "selected='selected'";} ?>>FriendFeed</option>
           <option value="lastfm" <?php if($preset== 'lastfm'){ echo "selected='selected'";} ?>>LastFM</option>
           <option value="linkedin" <?php if($preset == 'linkedin'){ echo "selected='selected'";} ?>>LinkedIn</option>           
           <option value="posterous" <?php if($preset== 'posterous'){ echo "selected='selected'";} ?>>Posterous</option>
           <option value="stumbleupon" <?php if($preset== 'stumbleupon'){ echo "selected='selected'";} ?>>Stumbleupon</option>
           <option value="tumblr" <?php if($preset== 'tumblr'){ echo "selected='selected'";} ?>>Tumblr</option>
           <option value="twitter" <?php if($preset == 'twitter'){ echo "selected='selected'";} ?>>Twitter</option>
           <option value="vimeo" <?php if($preset== 'vimeo'){ echo "selected='selected'";} ?>>Vimeo</option>
           <option value="youtube" <?php if($preset== 'youtube'){ echo "selected='selected'";} ?>>YouTube</option>           
       </select>
       </p>
       <p style="display:none">
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if ( $preset <> "select" ) { echo $preset; } else { echo $custom_title; } ?>" />
       </label>
       </p>     
       <p>
       <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
       </label>
       </p>
       <p><strong><?php _e('OR specify a custom value',woothemes); ?></strong></p>
       <p>
       <label for="<?php echo $this->get_field_id('custom'); ?>"><?php _e('Custom Title:'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('custom'); ?>" name="<?php echo $this->get_field_name('custom'); ?>" type="text" value="<?php echo $custom_title; ?>" />
       </label>
       </p>       
       <?php 
   }

} 

register_widget('AS_SocialWidget');

// =============================== Latest Video widget ======================================
function videoWidget()
{
	$settings = get_option("widget_videowidget");

	$tag = $settings['tag'];
	if ( $tag <> "" ) { $tag = 'video'; }

?>

	<div id="latest-video" class="widget">

		<h3 class="widget_title"><?php _e('Latest Video',woothemes); ?></h3>
		
		<div class="wrap">
			
			<div class="clear"></div>
			
			<?php
				
				global $post;
 				$videos = get_posts('numberposts=1&tag=' . $tag);
				foreach($videos as $post) {
					echo woo_get_embed('embed','280','157');
					echo '<a href="' . get_permalink($post->ID) . '" title="' . get_the_title($post->ID) . '">' . get_the_title($post->ID) . '</a>';
					}
			
			?>
			
			<div class="clear"></div>
		
		</div>				
	
	</div><!--latest-video-->	

<?php
}

function videoWidgetAdmin() {

	$settings = get_option("widget_videowidget");

	// check if anything's been sent
	if (isset($_POST['update_video'])) {
		$settings['tag'] = strip_tags(stripslashes($_POST['video_tag']));

		update_option("widget_videowidget",$settings);
	}

	echo '<p>
			<label for="video_tag">Video tag:
			<input id="video_tag" name="video_tag" type="text" class="widefat" value="'.$settings['tag'].'" /></label></p>';
	echo '<input type="hidden" id="update_video" name="update_video" value="1" />';

}

register_sidebar_widget('Woo - Latest Video', 'videoWidget');
register_widget_control('Woo - Latest Video', 'videoWidgetAdmin', 400, 200);

// =============================== Flickr widget ======================================
function flickrWidget()
{
	$settings = get_option("widget_flickrwidget");

	$id = $settings['id'];
	$number = $settings['number'];

?>

<div id="flickr" class="widget">
	<h3 class="widget_title"><?php _e('Photos on',woothemes); ?> <span>flick<span>r</span></span></h3>
	<div class="wrap">
		<div class="clear"></div>
		<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $number; ?>&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $id; ?>"></script>        
		<div class="clear"></div>
	</div>
</div>

<?php
}

function flickrWidgetAdmin() {

	$settings = get_option("widget_flickrwidget");

	// check if anything's been sent
	if (isset($_POST['update_flickr'])) {
		$settings['id'] = strip_tags(stripslashes($_POST['flickr_id']));
		$settings['number'] = strip_tags(stripslashes($_POST['flickr_number']));

		update_option("widget_flickrwidget",$settings);
	}

	echo '<p>
			<label for="flickr_id">Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):
			<input id="flickr_id" name="flickr_id" type="text" class="widefat" value="'.$settings['id'].'" /></label></p>';
	echo '<p>
			<label for="flickr_number">Number of photos:
			<input id="flickr_number" name="flickr_number" type="text" class="widefat" value="'.$settings['number'].'" /></label></p>';
	echo '<input type="hidden" id="update_flickr" name="update_flickr" value="1" />';

}

register_sidebar_widget('Woo - Flickr', 'flickrWidget');
register_widget_control('Woo - Flickr', 'flickrWidgetAdmin', 400, 200);

// =============================== Ad 125x125 widget ======================================
function adsWidget()
{
$settings = get_option("widget_adswidget");
$number = $settings['number'];
if ($number == 0) $number = 1;
$img_url = array();
$dest_url = array();

$numbers = range(1,$number); 
$counter = 0;

if (get_option('woo_ads_rotate') == 'true') {
	shuffle($numbers);
}
?>
<div id="adwidget" class="widget">

<h3><?php _e('Sponsors',woothemes); ?></h3>
<div class="adwrap">
<?php
	foreach ($numbers as $number) {	
		$counter++;
		$img_url[$counter] = get_option('woo_ad_image_'.$number);
		$dest_url[$counter] = get_option('woo_ad_url_'.$number);
	
?>
        <a href="<?php echo "$dest_url[$counter]"; ?>"><img src="<?php echo "$img_url[$counter]"; ?>" alt="Ad" /></a>
<?php } ?>
</div><!-- /adwrap -->
<div class="clear"></div>

</div><!-- /#ads -->
<?php

}
register_sidebar_widget('Woo - Ads 125x125', 'adsWidget');

function adsWidgetAdmin() {

	$settings = get_option("widget_adswidget");

	// check if anything's been sent
	if (isset($_POST['update_ads'])) {
		$settings['number'] = strip_tags(stripslashes($_POST['ads_number']));

		update_option("widget_adswidget",$settings);
	}

	echo '<p>
			<label for="ads_number">Number of ads (1-4):
			<input id="ads_number" name="ads_number" type="text" class="widefat" value="'.$settings['number'].'" /></label></p>';
	echo '<input type="hidden" id="update_ads" name="update_ads" value="1" />';

}
register_widget_control('Woo - Ads 125x125', 'adsWidgetAdmin', 200, 200);

// =============================== Search widget ======================================
function searchWidget()
{
include(TEMPLATEPATH . '/search-form.php');
}
register_sidebar_widget('Woo - Search', 'SearchWidget');


/* Deregister Default Widgets */

/*
function woo_deregister_widgets(){
    unregister_widget('WP_Widget_Search');         
}
add_action('widgets_init', 'woo_deregister_widgets');  
*/

?>
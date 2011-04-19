jQuery(document).ready(function() {

// Remove borders, etc

jQuery('#social ul li:first-child').css('border-top','none');
jQuery('#social ul li:last-child').css('border-bottom','none');
jQuery('#tabber ul.list li:last-child a').css('border-bottom','none');
jQuery('.widget li:last-child a').css('border-bottom','none');
jQuery('.widget li:last-child').css('border-bottom','none');

jQuery('.widget li:last-child').css('border-bottom-right-radius','5px');
jQuery('.widget li:last-child').css('-moz-border-radius-bottomright','5px');
jQuery('.widget li:last-child').css('-webkit-border-bottom-right-radius','5px');
jQuery('.widget li:last-child').css('border-bottom-left-radius','5px');
jQuery('.widget li:last-child').css('-moz-border-radius-bottomleft','5px');
jQuery('.widget li:last-child').css('-webkit-border-bottom-left-radius','5px');

jQuery('#tabber ul.list li:last-child').css('border-bottom-right-radius','5px');
jQuery('#tabber ul.list li:last-child').css('-moz-border-radius-bottomright','5px');
jQuery('#tabber ul.list li:last-child').css('-webkit-border-bottom-right-radius','5px');
jQuery('#tabber ul.list li:last-child').css('border-bottom-left-radius','5px');
jQuery('#tabber ul.list li:last-child').css('-moz-border-radius-bottomleft','5px');
jQuery('#tabber ul.list li:last-child').css('-webkit-border-bottom-left-radius','5px');
	
	
//Equal height footer columns

var height1 = jQuery("#extended_footer .col1").height()-32;
var height2 = jQuery("#extended_footer .col2").height()-32;
var height3 = jQuery("#extended_footer .col3").height()-21;

if (( height1 > height2  ) && ( height1 > height3 ))  {
	
	jQuery("#extended_footer .col2, #extended_footer .col3").height(height1);
	
}
else if (( height2 > height1  ) && ( height2 > height3 ))  {

	jQuery("#extended_footer .col1, #extended_footer .col3").height(height2);
}
else if (( height3 > height1  ) && ( height3 > height2 ))  {

	jQuery("#extended_footer .col1, #extended_footer .col2").height(height3);
}
	
});
<div id="sidebar">
	
	<!--<div id="social">
	
		<ul>
			
			<?php dynamic_sidebar('social-sidebar'); ?>
			
		</ul>
	
	</div>--><!-- /#social -->
	
	<?php dynamic_sidebar('sidebar-before'); ?>	
	
    <div id="tabber">
           
        <ul class="idTabs">
        <li>
			 <h3 class="oi">About Me</h3>
<p>Hi, I'm Matt Bridgeman, a web developer originally from Portsmouth but currently studying for a BA in Interactive Media at Bournemouth. <br/><a href="<?php bloginfo('url'); ?>/?page_id=2">Find out more here</a></p>

<div style="border-bottom:1px dotted #999"></div>
</li>
        </ul>
        
        <div class="clear"></div>

   	    <ul class="idTabs">
        <li>
			 <h3 class="oi">Recent Work</h3>
             <div style="margin: 0 auto; width:212px;" >
   <div class="boxgrid captionfull">
   <a class="group" rel="group" href="http://www.mattbridgeman.co.uk/portfolio/new890-big.jpg"><img alt="890 website redesign" src="http://www.mattbridgeman.co.uk/portfolio/grey-new890-small.jpg" /></a>
		<div class="cover boxcaption" style="top: 160px;">890 redesign</div></div>
        </div>
        
        <br />
        <div class="clear"></div>
        
             <div style="margin: 0 auto; width:212px;" >
   <div class="boxgrid captionfull">
   <a class="group" rel="group" href="http://www.mattbridgeman.co.uk/portfolio/qbert-big.jpg"><img alt="Qbert Tribute" src="http://www.mattbridgeman.co.uk/portfolio/grey-qbert-small.jpg" /></a>
		<div class="cover boxcaption" style="top: 160px;">Qbert Tribute</div></div>
        </div>
        
        <p><a href="http://www.mattbridgeman.co.uk/portfolio">Check out more here</a></p>
        </li>
        </ul>
        
			
    </div><!-- /tabber -->

	<!-- Widgetized Sidebar -->	
	<?php dynamic_sidebar('sidebar-after'); ?>		           
	
</div><!-- /#sidebar -->
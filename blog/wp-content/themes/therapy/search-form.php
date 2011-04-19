<div id="search_main" class="widget">

	<h3><?php _e('Search',woothemes); ?></h3>

    <form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
        <div>
        <input type="text" class="field" name="s" id="s"  value="Search..." onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}" />
        <input type="submit" class="submit" name="submit" value="Search" />
        </div>
    </form>
</div>

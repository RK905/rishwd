<?php
/**
 * Filetrip Uploader preview page
 *
 * @package   Filetrip_Post_Type
 * @author    Abdulrhman Elbuni
 * @license   GPL-2.0+
 * @copyright 2013-2014
 **/

 get_header(); ?>
<div class="wrap-content filetrip-single">
<?php  while ( have_posts() ) : the_post();
		$id = get_the_ID();
        echo do_shortcode('[filetrip id="'.$id.'"]');
  
 endwhile; ?>
</div>
<?php get_footer(); ?>

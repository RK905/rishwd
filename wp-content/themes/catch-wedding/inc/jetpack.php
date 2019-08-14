<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.me/
 *
 * @package Catch_Wedding
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.me/support/infinite-scroll/
 * See: https://jetpack.me/support/responsive-videos/
 */
function catch_wedding_jetpack_setup() {
	/**
	 * Setup Infinite Scroll using JetPack if navigation type is set
	 */
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'wrapper'        => false,
		'render'         => 'catch_wedding_infinite_scroll_render',
		'footer'         => false,
		'footer_widgets' => array( 'sidebar-2', 'sidebar-3', 'sidebar-4', 'sidebar-5' ),
	) );
}
add_action( 'after_setup_theme', 'catch_wedding_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function catch_wedding_infinite_scroll_render() {
while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
			get_template_part( 'template-parts/content/content', 'search' );
		else :
			get_template_part( 'template-parts/content/content', get_post_format() );
		endif;
	}
}

/**
 * Support JetPack featured content
 */
function catch_wedding_get_featured_posts() {

	$number = get_theme_mod( 'catch_wedding_featured_content_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
		$args['post_type'] = 'featured-content';

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			$post_id = get_theme_mod( 'catch_wedding_featured_content_cpt_' . $i );

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';

	$featured_posts = get_posts( $args );

	return $featured_posts;
}

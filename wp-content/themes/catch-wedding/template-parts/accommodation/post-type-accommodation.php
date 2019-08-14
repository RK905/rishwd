<?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package Catch_Wedding
 */

$number = get_theme_mod( 'catch_wedding_accommodation_number', 6 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$args = array(
	'orderby'             => 'post__in',
	'ignore_sticky_posts' => 1 // ignore sticky posts
);

$post_list  = array();// list of valid post/page ids

$no_of_post = 0; // for number of posts

	$args['post_type'] = 'page';

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = '';

			$post_id = get_theme_mod( 'catch_wedding_accommodation_page_' . $i );
	
		if ( $post_id && '' !== $post_id ) {
			// Polylang Support.
			if ( class_exists( 'Polylang' ) ) {
				$post_id = pll_get_post( $post_id, pll_current_language() );
			}

			$post_list = array_merge( $post_list, array( $post_id ) );

			$no_of_post++;
		}
	}

	$args['post__in'] = $post_list;


if ( 0 === $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;
$loop                   = new WP_Query( $args );

if ( $loop -> have_posts() ) :
	?>
<div class="entry-content">
	<ul class="accommodation-details">
	<?php
	while ( $loop -> have_posts() ) :
		$loop -> the_post();
		?>
		<li>
			<span>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'catch-wedding-accommodation' ); ?>
					<span><?php the_title(); ?></span>
				</a>
			</span>
		</li><!-- #accommodation-item -->
	<?php
	endwhile;

	?>
	</ul>
</div><!-- .entry-content -->
	<?php
	wp_reset_postdata();
endif;
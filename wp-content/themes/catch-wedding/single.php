<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package Catch_Wedding
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="singular-content-wrap">
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();

				// Include the single post content template.
				get_template_part( 'template-parts/content/content', 'single' );

				// Comments Templates
				get_template_part( 'template-parts/content/content', 'comment' );

				if ( is_singular( 'attachment' ) ) {
					// Parent post navigation.
					the_post_navigation( array(
						'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'catch-wedding' ),
					) );
				} elseif ( is_singular( 'post' ) ) {
					// Previous/next post navigation.
					the_post_navigation( array(
						'prev_text' => '<span class="nav-subtitle-icon-wrapper">' . catch_wedding_get_svg( array( 'icon' => 'long-arrow-left' ) ) . '<span aria-hidden="true" class="nav-subtitle">'. __( 'Previous Post', 'catch-wedding' ) . '</span></span> <span class="nav-title">%title</span><span class="screen-reader-text">' . __( 'Previous Post', 'catch-wedding' ) . '</span>' ,
						'next_text' => '<span class="nav-subtitle-icon-wrapper">' . catch_wedding_get_svg( array( 'icon' => 'long-arrow-right' ) ) . '<span aria-hidden="true" class="nav-subtitle">'. __( 'Next Post', 'catch-wedding' ) . '</span></span> <span class="nav-title">%title</span><span class="screen-reader-text">' . __( 'Next Post', 'catch-wedding' ) . '</span>' ,
					) );
				}

				// End of the loop.
			endwhile;
			?>
		</div><!-- .singular-content-wrap -->
	</main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

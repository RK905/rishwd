<?php
/**
 * Template part for displaying Recent Posts in the front page template
 *
 * @package Catch_Wheels
 */
?>
<div class="recent-blog-section section">
	<div class="wrapper">
		<?php
        $title     = esc_html__( 'Recent Posts', 'catch-wedding' ); ?>

		<div class="section-heading-wrapper">
			<?php if ( $title ) : ?>
				<div class="section-title-wrapper">
					<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
				</div><!-- .section-title-wrapper -->
			<?php endif; ?>
		</div><!-- .section-heading-wrapper -->

		<div class="recent-blog-content-wrapper section-content-wrapper ">
			<?php
			$recent_posts = new WP_Query( array(
				'ignore_sticky_posts' => true,
			) );

			if ( $recent_posts->have_posts() ) :

				/* Start the Loop */
				while ( $recent_posts->have_posts() ) :
					$recent_posts->the_post();
					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content/content', get_post_format() );
				endwhile;

			else :

				get_template_part( 'template-parts/content/content', 'none' );

			endif;
			wp_reset_postdata();
			?>

		</div><!-- .section-content-wrap -->
		<p class="view-more"><a class="more-recent-posts button" href="<?php the_permalink( get_option( 'page_for_posts' ) ); ?>"><?php esc_html_e( 'More Posts', 'catch-wedding' ); ?></a></p>
	</div> <!-- .wrapper -->
</div> <!-- .recent-blog-content-wrapper -->

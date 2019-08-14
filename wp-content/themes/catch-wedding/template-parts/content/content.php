<?php
/**
 * The template part for displaying content
 *
 * @package Catch_Wedding
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('grid-item'); ?>>
	<div class="post-wrapper">
		<?php
			if ( is_sticky() ) {
				catch_wedding_post_thumbnail( 'catch-wedding-sticky' );
			} else {
				catch_wedding_post_thumbnail();
			}
		?>

		<div class="entry-container">
			<header class="entry-header">
				<div class="entry-meta">
					<?php echo catch_wedding_entry_category(); ?>
				</div><!-- .entry-meta -->
				<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
					<span class="sticky-post"><?php esc_html_e( 'Featured', 'catch-wedding' ); ?></span>
				<?php endif; ?>

				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

				<?php echo catch_wedding_recent_post_entry_header(); ?>
			</header><!-- .entry-header -->
				<div class="entry-summary">
					<?php the_excerpt(); ?>

					<?php
						catch_wedding_edit_link();
					?>
				</div><!-- .entry-summary -->
			<?php
			//echo catch_wedding_entry_footer(); ?>
		</div><!-- .entry-container -->
	</div><!-- .hentry-inner -->
</article><!-- #post-## -->

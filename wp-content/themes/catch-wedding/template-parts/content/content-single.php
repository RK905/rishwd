<?php
/**
 * The template part for displaying single posts
 *
 * @package Catch_Wedding
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php if ( 'page' !== get_post_type() ) :
			catch_wedding_entry_header();

		endif; ?>
	</header><!-- .entry-header -->

	<?php
	$single_layout = get_theme_mod( 'catch_wedding_single_layout', 'disabled' );

	if ( 'disabled' !== $single_layout ) {
		catch_wedding_post_thumbnail( $single_layout );
	}
	?>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'catch-wedding' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'catch-wedding' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php 	catch_wedding_entry_footer(); ?>

</article><!-- #post-## -->

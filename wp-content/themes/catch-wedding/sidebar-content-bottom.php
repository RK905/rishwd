<?php
/**
 * The template for the content bottom widget areas on posts and pages
 *
 * @package Catch_Wedding
 */
?>

<?php
if ( ! is_active_sidebar( 'sidebar-6' ) && ! is_active_sidebar( 'sidebar-7' ) ) {
	return;
}

// If we get this far, we have widgets. Let's do this.
?>
<aside id="content-bottom-widgets" class="content-bottom-widgets" role="complementary">
	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : ?>
		<div class="widget-area">
			<?php dynamic_sidebar( 'sidebar-6' ); ?>
		</div><!-- .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>
		<div class="widget-area">
			<?php dynamic_sidebar( 'sidebar-7' ); ?>
		</div><!-- .widget-area -->
	<?php endif; ?>
</aside><!-- .content-bottom-widgets -->

<?php
/**
 * Display Header Media Text
 *
 * @package Catch_Wedding
 */
?>
<?php
$header_media_title = get_theme_mod( 'catch_wedding_header_media_title', esc_html__( 'Diane and William', 'catch-wedding' ) );

$header_media_text = get_theme_mod( 'catch_wedding_header_media_text', esc_html__( 'Make things as simple as possible but no simpler.', 'catch-wedding' ) );

if ( '' !== $header_media_title || '' !== $header_media_text ) : ?>
<div class="custom-header-content sections header-media-section">
	<?php if ( '' !== $header_media_title ) : ?>
	<h2 class="entry-title"><?php echo wp_kses_post( $header_media_title ); ?></h2>
	<?php endif; ?>

	<?php
	$button_url  = get_theme_mod( 'catch_wedding_header_media_url', '#' );
	$button_text = get_theme_mod( 'catch_wedding_header_media_url_text', esc_html__( 'Continue reading', 'catch-wedding' ) );
	?>

	<p class="site-header-text"><?php echo wp_kses_post( $header_media_text ); ?>
	<?php if ( $button_url || $button_text ) : ?><a class="more-link"  href="<?php echo esc_url( $button_url ); ?>" target="<?php echo get_theme_mod( 'catch_wedding_header_url_target' ) ? '_blank' : '_self'; ?>"  > <span class="more-button"><?php echo esc_html( $button_text ); ?><span class="screen-reader-text"><?php echo wp_kses_post( $header_media_title ); ?><?php endif; ?></span></span></a></p>
</div>
<?php endif; ?>

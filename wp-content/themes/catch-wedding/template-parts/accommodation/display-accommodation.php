<?php
/**
 * The template for displaying accommodation
 *
 * @package Catch_Wedding
 */
?>

<?php
$enable = get_theme_mod( 'catch_wedding_accommodation_option', 'disabled' );

if ( ! catch_wedding_check_section( $enable ) ) {
	// Bail if featured content is disabled.
	return;
}

$title          = get_theme_mod( 'catch_wedding_accommodation_title', esc_html__( 'Accommodations', 'catch-wedding' ) );
$description    = get_theme_mod( 'catch_wedding_accommodation_description', wp_kses_data( __( 'We\'ve reserved rooms at a Holiday Inn close to downtown Asheville.
Complimentary shuttle service to the downtown area and transportation
to and from the wedding ceremony/reception included. Also
walking-distance from a Cracker Barrel.', 'catch-wedding' ) ) );
$highlight_text = get_theme_mod( 'catch_wedding_accommodation_highlight_text', wp_kses_post( __( 'To book a room, call <span>+123456789</span>', 'catch-wedding' ) ) );


?>

<div id="accommodation-section" class="accommodation-section section">
	<div class="wrapper">
		<?php
			$map         = get_theme_mod( 'catch_wedding_accommodation_map', trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/contact-map.jpg' );

			$link_target = get_theme_mod( 'catch_wedding_accommodation_link_target' ) ? '_blank' : '_self';

			$class = 'layout-one';

			if ( $map ) {
				$class = 'layout-two';
			}

			$map_link   = get_theme_mod( 'catch_wedding_accommodation_map_link', esc_html__( '#', 'catch-wedding' ) );
			$map_target = get_theme_mod( 'catch_wedding_accommodation_map_target' ) ? '_blank' : '_self';
		?>

		<div class="section-content-wrap">
			<article class="hentry">
				<div class="hentry-inner">

				<?php if ( $map ) : ?>
						<div class="post-thumbnail accommodation-map" style="background-image: url( '<?php echo esc_url( $map ); ?>' )">
							<a href= "<?php echo esc_url( $map_link ); ?>" target="<?php echo $map_target; ?>"></a>
						</div><!-- .accommodation-map -->
				<div class="entry-container">

				<?php else : ?>
				<div class="entry-container full-width">
				<?php endif; ?>

					<?php if ( $title || $description || $highlight_text ) : ?>
						<header class="entry-header">
							<?php if (  $title ) : ?>
									<h2 class="entry-title section-title"><?php echo wp_kses_data( $title ); ?></h2>
							<?php endif; ?>

							<?php if ( $description ) : ?>
								<p class="accommodation-description"> <?php echo wp_kses_post( $description ); ?> </p>
							<?php endif; ?>

							<?php if ( $highlight_text ) : ?>
								<p class="accommodation-highlight-text"> <?php echo wp_kses_post( $highlight_text ); ?> </p>
							<?php endif; ?>
						</header>
					<?php endif; ?>

					<?php
						get_template_part( 'template-parts/accommodation/post-type', 'accommodation' );
					?>
				</div><!-- .entry-container -->
			</div>
			</article> <!-- article -->
		</div><!-- .section-content-wrap -->
	</div><!-- .wrapper -->
</div> <!-- #accommodation-section -->

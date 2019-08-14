<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Catch_Wedding
 */

if ( ! function_exists( 'catch_wedding_featured_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own catch_wedding_featured_image(), and that function will be used instead.
	 *
	 * @since Catch Base 1.0
	 */
	function catch_wedding_featured_image() {
		$header_media_title = get_theme_mod( 'catch_wedding_header_media_title', esc_html__( 'Diane and William', 'catch-wedding' ) );
		$header_media_text = get_theme_mod( 'catch_wedding_header_media_text', esc_html__( 'Make things as simple as possible but no simpler.', 'catch-wedding' ) );
		if ( is_post_type_archive( 'jetpack-testimonial' ) ) :
			$jetpack_options = get_theme_mod( 'jetpack_testimonials' );
			if ( isset( $jetpack_options['featured-image'] ) && '' !== $jetpack_options['featured-image'] ) : ?>
				<div class="header-media">
					<div class="wrapper">
						<div class="post-thumbnail archive-header-image">
							<?php echo wp_get_attachment_image( (int) $jetpack_options['featured-image'], 'catch-wedding-header-image' ); ?>
						</div>
					</div><!-- .wrapper -->
				</div><!-- .header-media -->
			<?php endif;

		elseif ( is_post_type_archive( 'jetpack-portfolio' ) || is_post_type_archive( 'featured-content' ) || is_post_type_archive( 'ect-service' ) ) :
			$option = '';

			if ( is_post_type_archive( 'jetpack-portfolio' ) ) {
				$option = 'jetpack_portfolio_featured_image';
			} elseif ( is_post_type_archive( 'featured-content' ) ) {
				$option = 'featured_content_featured_image';
			} elseif ( is_post_type_archive( 'ect-service' ) ) {
				$option = 'ect_service_featured_image';
			}

			$featured_image = get_option( $option );

			if ( '' !== $featured_image ) : ?>
				<div class="header-media">
					<div class="wrapper">
						<div class="post-thumbnail archive-header-image">
							<?php echo wp_get_attachment_image( (int) $featured_image, 'catch-wedding-header-image' ); ?>
						</div>
					</div><!-- .wrapper -->
				</div><!-- .header-media -->

			<?php endif;

		elseif ( has_custom_header() || '' !== $header_media_title || '' !== $header_media_text ) : ?>
			<div class="header-media">
				<div class="wrapper">
					<?php if ( has_custom_header() ) :  ?>
					<div class="custom-header-media">
						<?php the_custom_header_markup(); ?>
					</div>
					<?php endif; ?>

					<?php get_template_part( 'template-parts/header/header-media', 'text' ); ?>
				</div><!-- .wrapper -->

				<?php if ( ! get_theme_mod( 'catch_wedding_header_disable_scroll_down' ) ) : ?>
					<div class="scroll-down">
						<span><?php esc_html_e( 'Scroll', 'catch-wedding' ); ?></span>
						<span class="fa fa-angle-down" aria-hidden="true"> <?php echo catch_wedding_get_svg( array( 'icon' => 'angle-down' ) ); ?> </span>
					</div><!-- .scroll-down -->
				<?php endif; ?>

			</div><!-- .header-media -->
		<?php
		endif;
	} // catch_wedding_featured_image
endif;

if ( ! function_exists( 'catch_wedding_featured_page_post_image' ) ) :
	/**
	 * Template for Featured Header Image from Post and Page
	 *
	 * To override this in a child theme
	 * simply create your own catch_wedding_featured_imaage_pagepost(), and that function will be used instead.
	 *
	 * @since Catch Wedding 1.0
	 */
	function catch_wedding_featured_page_post_image() {
		if ( ! has_post_thumbnail() ) {
			catch_wedding_featured_image();
			return;
		}
		?>
		<div class="header-media">
			<div class="wrapper">
				<div class="post-thumbnail singular-header-image">
					<?php
					if ( is_home() && $blog_id = get_option( 'page_for_posts' ) ) {
					    echo get_the_post_thumbnail( $blog_id, 'catch-wedding-header-image' );
					} else {
						the_post_thumbnail( 'catch-wedding-header-image' );
					}
					?>
				</div><!-- .post-thumbnail -->
			</div><!-- .wrapper -->
		</div><!-- .header-media -->
		<?php
	} // catch_wedding_featured_page_post_image
endif;


if ( ! function_exists( 'catch_wedding_featured_overall_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own catch_wedding_featured_pagepost_image(), and that function will be used instead.
	 *
	 * @since Catch Wedding 1.0
	 */
	function catch_wedding_featured_overall_image() {
		global $post, $wp_query;
		$enable = get_theme_mod( 'catch_wedding_header_media_option', 'homepage' );

		// Get Page ID outside Loop
		$page_id = absint( $wp_query->get_queried_object_id() );

		$page_for_posts = absint( get_option( 'page_for_posts' ) );

		// Check Enable/Disable header image in Page/Post Meta box
		if ( is_page() || is_single() ) {
			//Individual Page/Post Image Setting
			$individual_featured_image = get_post_meta( $post->ID, 'catch-wedding-header-image', true );

			if ( 'disable' === $individual_featured_image || ( 'default' === $individual_featured_image && 'disable' === $enable ) ) {
				echo '<!-- Page/Post Disable Header Image -->';
				return;
			}
			elseif ( 'enable' == $individual_featured_image && 'disable' === $enable ) {
				catch_wedding_featured_page_post_image();
			}
		}

		// Check Homepage
		if ( 'homepage' === $enable ) {
			if ( is_front_page() || ( is_home() && intval( $page_for_posts ) !== intval( $page_id ) ) ) {
				catch_wedding_featured_image();
			}
		}
		// Check Excluding Homepage
		if ( 'exclude-home' === $enable ) {
			if ( is_front_page() || ( is_home() && intval( $page_for_posts ) !== intval( $page_id ) ) ) {
				return false;
			}
			else {
				catch_wedding_featured_image();
			}
		}
		elseif ( 'exclude-home-page-post' === $enable ) {
			if ( is_front_page() || ( is_home() && intval( $page_for_posts ) !== intval( $page_id ) ) ) {
				return false;
			}
			elseif ( is_page() || is_single() ) {
				catch_wedding_featured_page_post_image();
			}
			else {
				catch_wedding_featured_image();
			}
		}
		// Check Entire Site
		elseif ( 'entire-site' === $enable ) {
			catch_wedding_featured_image();
		}
		// Check Entire Site (Post/Page)
		elseif ( 'entire-site-page-post' === $enable ) {
			if ( is_page() || is_single() || ! is_front_page() ) {
				catch_wedding_featured_page_post_image();
			}
			else {
				catch_wedding_featured_image();
			}
		}
		// Check Page/Post
		elseif ( 'pages-posts' === $enable ) {
			if ( is_page() || is_single() ) {
				catch_wedding_featured_page_post_image();
			}
		}
	} // catch_wedding_featured_overall_image
endif;

<?php
/**
 * Hero Content Options
 *
 * @package Catch_Wedding
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_wedding_hero_content_options( $wp_customize ) {
	$wp_customize->add_section( 'catch_wedding_hero_content_options', array(
			'title' => esc_html__( 'Hero Content Options', 'catch-wedding' ),
			'panel' => 'catch_wedding_theme_options',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_hero_content_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'choices'           => catch_wedding_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'catch-wedding' ),
			'section'           => 'catch_wedding_hero_content_options',
			'type'              => 'select',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
            'name'              => 'catch_wedding_hero_main_image',
            'default'           => trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/hero-content-1920x925.jpg',
            'sanitize_callback' => 'catch_wedding_sanitize_image',
            'active_callback'   => 'catch_wedding_is_hero_content_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Section Background Image', 'catch-wedding' ),
            'section'           => 'catch_wedding_hero_content_options',
            'mime_type'         => 'image',
        )
    );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_hero_content',
			'default'           => '0',
			'sanitize_callback' => 'catch_wedding_sanitize_post',
			'active_callback'   => 'catch_wedding_is_hero_content_active',
			'label'             => esc_html__( 'Page', 'catch-wedding' ),
			'section'           => 'catch_wedding_hero_content_options',
			'type'              => 'dropdown-pages',
		)
	);
}
add_action( 'customize_register', 'catch_wedding_hero_content_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'catch_wedding_is_hero_content_active' ) ) :
	/**
	* Return true if hero content is active
	*
	* @since Catch Wedding Pro 1.0
	*/
	function catch_wedding_is_hero_content_active( $control ) {
		global $wp_query;

		$page_id = $wp_query->get_queried_object_id();

		// Front page display in Reading Settings
		$page_for_posts = get_option( 'page_for_posts' );

		$enable = $control->manager->get_setting( 'catch_wedding_hero_content_visibility' )->value();

		//return true only if previwed page on customizer matches the type of content option selected
		return ( 'entire-site' == $enable  || ( ( is_front_page() || ( is_home() && $page_for_posts != $page_id ) ) &&	 'homepage' == $enable )
			);
	}
endif;
<?php
/**
 * Accommodation Info options
 *
 * @package Catch_Wedding
 */

/**
 * Add accommodation options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_wedding_accommodation_options( $wp_customize ) {
    $wp_customize->add_section( 'catch_wedding_accommodation', array(
			'title' => esc_html__( 'Accommodations', 'catch-wedding' ),
			'panel' => 'catch_wedding_theme_options',
		)
	);

	// Add color scheme setting and control.
	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_accommodation_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'choices'           => catch_wedding_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'select',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_accommodation_title',
			'default'           => esc_html__( 'Accommodations', 'catch-wedding' ),
			'sanitize_callback' => 'wp_kses_data',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Title', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'text',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_accommodation_description',
			'default'           => wp_kses_data( __( 'We\'ve reserved rooms at a Holiday Inn close to downtown Asheville.
Complimentary shuttle service to the downtown area and transportation
to and from the wedding ceremony/reception included. Also
walking-distance from a Cracker Barrel.', 'catch-wedding' ) ),
			'sanitize_callback' => 'wp_kses_data',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Description', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'textarea',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_accommodation_highlight_text',
			'default'           => wp_kses_post( __( 'To book a room, call <span>+123456789</span>', 'catch-wedding' ) ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Highlight Text', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'textarea',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_accommodation_number',
			'default'           => 6,
			'sanitize_callback' => 'catch_wedding_sanitize_number_range',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'description'       => esc_html__( 'Save and refresh the page if No. is changed (Max no of Content is 20)', 'catch-wedding' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Links', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'number',
		)
	);

	$number = get_theme_mod( 'catch_wedding_accommodation_number', 6 );

	//loop for page content
	for ( $i = 1; $i <= $number ; $i++ ) {
		catch_wedding_register_option( $wp_customize, array(
				'name'              => 'catch_wedding_accommodation_page_' . $i,
				'sanitize_callback' => 'catch_wedding_sanitize_post',
				'active_callback'   => 'catch_wedding_is_accommodation_active',
				'label'             => esc_html__( 'Page', 'catch-wedding' ) . ' ' . $i ,
				'section'           => 'catch_wedding_accommodation',
				'type'              => 'dropdown-pages',
			)
		);
	} // End for().

	catch_wedding_register_option( $wp_customize, array(
			'name'              =>'catch_wedding_accommodation_map',
			'default'           => trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/contact-map.jpg',
			'sanitize_callback' => 'catch_wedding_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Map Image', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              =>'catch_wedding_accommodation_map_link',
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Link', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              =>'catch_wedding_accommodation_map_target',
			'sanitize_callback' => 'catch_wedding_sanitize_checkbox',
			'active_callback'   => 'catch_wedding_is_accommodation_active',
			'label'             => esc_html__( 'Check to Open Link in New Window/Tab', 'catch-wedding' ),
			'section'           => 'catch_wedding_accommodation',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'catch_wedding_accommodation_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'catch_wedding_is_accommodation_active' ) ) :
	/**
	* Return true if accommodation is active
	*
	* @since Catch Wedding Pro 1.0
	*/
	function catch_wedding_is_accommodation_active( $control ) {
		$enable = $control->manager->get_setting( 'catch_wedding_accommodation_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( catch_wedding_check_section( $enable ) );
	}
endif;
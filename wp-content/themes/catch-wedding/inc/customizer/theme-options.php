<?php
/**
 * Theme Options
 *
 * @package Catch_Wedding
 */

/**
 * Add theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_wedding_theme_options( $wp_customize ) {
	$wp_customize->add_panel( 'catch_wedding_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'catch-wedding' ),
		'priority' => 130,
	) );

	// Layout Options
	$wp_customize->add_section( 'catch_wedding_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'catch-wedding' ),
		'panel' => 'catch_wedding_theme_options',
		)
	);

	/* Default Layout */
	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'catch-wedding' ),
			'section'           => 'catch_wedding_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'catch-wedding' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'catch-wedding' ),
			),
		)
	);

	/* Homepage/Archive Layout */
	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_homepage_archive_layout',
			'default'           => 'no-sidebar-full-width',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'label'             => esc_html__( 'Homepage/Archive Layout', 'catch-wedding' ),
			'section'           => 'catch_wedding_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'catch-wedding' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'catch-wedding' ),
			),
		)
	);

	/* Single Page/Post Image Layout */
	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_single_layout',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'label'             => esc_html__( 'Single Page/Post Image Layout', 'catch-wedding' ),
			'section'           => 'catch_wedding_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'disabled'       => esc_html__( 'Disabled', 'catch-wedding' ),
				'post-thumbnail' => esc_html__( 'Post Thumbnail', 'catch-wedding' ),
			),
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'catch_wedding_excerpt_options', array(
		'panel'     => 'catch_wedding_theme_options',
		'title'     => esc_html__( 'Excerpt Options', 'catch-wedding' ),
	) );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_excerpt_length',
			'default'           => '20',
			'sanitize_callback' => 'absint',
			'description' => esc_html__( 'Excerpt length. Default is 20 words', 'catch-wedding' ),
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'catch-wedding' ),
			'section'  => 'catch_wedding_excerpt_options',
			'type'     => 'number',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_excerpt_more_text',
			'default'           => esc_html__( 'Continue Reading', 'catch-wedding' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Continue Reading Text', 'catch-wedding' ),
			'section'           => 'catch_wedding_excerpt_options',
			'type'              => 'text',
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'catch_wedding_search_options', array(
		'panel'     => 'catch_wedding_theme_options',
		'title'     => esc_html__( 'Search Options', 'catch-wedding' ),
	) );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_search_text',
			'default'           => esc_html__( 'Search', 'catch-wedding' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Search Text', 'catch-wedding' ),
			'section'           => 'catch_wedding_search_options',
			'type'              => 'text',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'catch_wedding_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'catch-wedding' ),
		'panel'       => 'catch_wedding_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'catch-wedding' ),
	) );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_front_page_category',
			'sanitize_callback' => 'catch_wedding_sanitize_category_list',
			'custom_control'    => 'Catch_Wedding_Multi_Categories_Control',
			'label'             => esc_html__( 'Categories', 'catch-wedding' ),
			'section'           => 'catch_wedding_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	// Pagination Options.
	$pagination_type = get_theme_mod( 'catch_wedding_pagination_type', 'default' );

	$nav_desc = '';

	/**
	* Check if navigation type is Jetpack Infinite Scroll and if it is enabled
	*/
	$nav_desc = sprintf(
		wp_kses(
			__( 'For infinite scrolling, use %1$sCatch Infinite Scroll Plugin%2$s with Infinite Scroll module Enabled.', 'catch-wedding' ),
			array(
				'a' => array(
					'href' => array(),
					'target' => array(),
				),
				'br'=> array()
			)
		),
		'<a target="_blank" href="https://wordpress.org/plugins/catch-infinite-scroll/">',
		'</a>'
	);

	$wp_customize->add_section( 'catch_wedding_pagination_options', array(
		'description' => $nav_desc,
		'panel'       => 'catch_wedding_theme_options',
		'title'       => esc_html__( 'Pagination Options', 'catch-wedding' ),
	) );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'choices'           => catch_wedding_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'catch-wedding' ),
			'section'           => 'catch_wedding_pagination_options',
			'type'              => 'select',
		)
	);

	/* Scrollup Options */
	$wp_customize->add_section( 'catch_wedding_scrollup', array(
		'panel'    => 'catch_wedding_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'catch-wedding' ),
	) );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_disable_scrollup',
			'sanitize_callback' => 'catch_wedding_sanitize_checkbox',
			'label'             => esc_html__( 'Disable Scroll Up', 'catch-wedding' ),
			'section'           => 'catch_wedding_scrollup',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'catch_wedding_theme_options' );

/** Active Callback Functions */

if( ! function_exists( 'catch_wedding_is_header_right_menu_social_enabled' ) ) :
	/**
	* Return true if header right or social menu is enabled
	*
	* @since Catch Wedding Pro 1.0
	*/
	function catch_wedding_is_header_right_menu_social_enabled( $control ) {
		return ( has_nav_menu( 'social-header' ) );
	}
endif;

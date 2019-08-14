<?php
/**
 * Header Media Options
 *
 * @package Catch_Wedding
 */

function catch_wedding_header_media_options( $wp_customize ) {
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'If you add video, it will only show up on Homepage/FrontPage. Other Pages will use Header/Post/Page Image depending on your selection of option. Header Image will be used as a fallback while the video loads ', 'catch-wedding' );

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_media_option',
			'default'           => 'homepage',
			'sanitize_callback' => 'catch_wedding_sanitize_select',
			'choices'           => array(
				'homepage'               => esc_html__( 'Homepage / Frontpage', 'catch-wedding' ),
				'exclude-home'           => esc_html__( 'Excluding Homepage', 'catch-wedding' ),
				'exclude-home-page-post' => esc_html__( 'Excluding Homepage, Page/Post Featured Image', 'catch-wedding' ),
				'entire-site'            => esc_html__( 'Entire Site', 'catch-wedding' ),
				'entire-site-page-post'  => esc_html__( 'Entire Site, Page/Post Featured Image', 'catch-wedding' ),
				'pages-posts'            => esc_html__( 'Pages and Posts', 'catch-wedding' ),
				'disable'                => esc_html__( 'Disabled', 'catch-wedding' ),
			),
			'label'             => esc_html__( 'Enable on ', 'catch-wedding' ),
			'section'           => 'header_image',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_media_title',
			'default'           => esc_html__( 'Diane and William', 'catch-wedding' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Title', 'catch-wedding' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

    catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_media_text',
			'default'           => esc_html__( 'Make things as simple as possible but no simpler.', 'catch-wedding' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Text', 'catch-wedding' ),
			'section'           => 'header_image',
			'type'              => 'textarea',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_media_url',
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'label'             => esc_html__( 'Header Media Url', 'catch-wedding' ),
			'section'           => 'header_image',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_media_url_text',
			'default'           => esc_html__( 'Continue Reading', 'catch-wedding' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Header Media Url Text', 'catch-wedding' ),
			'section'           => 'header_image',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_url_target',
			'sanitize_callback' => 'catch_wedding_sanitize_checkbox',
			'label'             => esc_html__( 'Check to Open Link in New Window/Tab', 'catch-wedding' ),
			'section'           => 'header_image',
			'type'              => 'checkbox',
		)
	);

	catch_wedding_register_option( $wp_customize, array(
			'name'              => 'catch_wedding_header_disable_scroll_down',
			'sanitize_callback' => 'catch_wedding_sanitize_checkbox',
			'label'             => esc_html__( 'Check to disable Scroll Link', 'catch-wedding' ),
			'section'           => 'header_image',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'catch_wedding_header_media_options' );


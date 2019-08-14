<?php

/**
 * Function to register control and setting
 */
function catch_wedding_register_option( $wp_customize, $option ) {

	// Initialize Setting.
	$wp_customize->add_setting( $option['name'], array(
		'sanitize_callback'    => $option['sanitize_callback'],
		'default'              => isset( $option['default'] ) ? $option['default'] : '',
		'transport'            => isset( $option['transport'] ) ? $option['transport'] : 'refresh',
		'theme_supports'       => isset( $option['theme_supports'] ) ? $option['theme_supports'] : '',
	) );

	$control = array(
		'label'    => $option['label'],
		'section'  => $option['section'],
		'settings' => $option['name'],
	);

	if ( isset( $option['active_callback'] ) ) {
		$control['active_callback'] = $option['active_callback'];
	}

	if ( isset( $option['priority'] ) ) {
		$control['priority'] = $option['priority'];
	}

	if ( isset( $option['choices'] ) ) {
		$control['choices'] = $option['choices'];
	}

	if ( isset( $option['type'] ) ) {
		$control['type'] = $option['type'];
	}

	if ( isset( $option['input_attrs'] ) ) {
		$control['input_attrs'] = $option['input_attrs'];
	}

	if ( isset( $option['description'] ) ) {
		$control['description'] = $option['description'];
	}

	if ( isset( $option['mime_type'] ) ) {
		$control['mime_type'] = $option['mime_type'];
	}

	if ( isset( $option['custom_control'] ) ) {
		$wp_customize->add_control( new $option['custom_control']( $wp_customize, $option['name'], $control ) );
	} else {
		$wp_customize->add_control( $option['name'], $control );
	}
}

/**
 * Function to reset date with respect to condition
 */
function catch_wedding_reset_data() {
	if ( get_theme_mod( 'catch_wedding_reset_all_settings' ) ) {
		remove_theme_mods();

		return;
	}
}
add_action( 'customize_save_after', 'catch_wedding_reset_data' );

/**
 * Alphabetically sort theme options sections
 *
 * @param  wp_customize object $wp_customize wp_customize object.
 */
function catch_wedding_sort_sections_list( $wp_customize ) {
	foreach ( $wp_customize->sections() as $section_key => $section_object ) {
		if ( false !== strpos( $section_key, 'catch_wedding_' ) && 'catch_wedding_reset_all' !== $section_key && 'catch_wedding_important_links' !== $section_key ) {
			$options[] = $section_key;
		}
	}

	sort( $options );

	$priority = 1;
	foreach ( $options as  $option ) {
		$wp_customize->get_section( $option )->priority = $priority++;
	}
}
add_action( 'customize_register', 'catch_wedding_sort_sections_list', 999 );

/**
 * Returns an array of visibility options for featured sections
 *
 * @since Catch Wedding Pro 1.0
 */
function catch_wedding_section_visibility_options() {
	$options = array(
		'homepage'    => esc_html__( 'Homepage / Frontpage', 'catch-wedding' ),
		'entire-site' => esc_html__( 'Entire Site', 'catch-wedding' ),
		'disabled'    => esc_html__( 'Disabled', 'catch-wedding' ),
	);

	return apply_filters( 'catch_wedding_section_visibility_options', $options );
}

/**
 * Returns an array of featured content options
 *
 * @since Catch Wedding Pro 1.0
 */
function catch_wedding_sections_layout_options() {
	$options = array(
		'layout-one'   => esc_html__( '1 column', 'catch-wedding' ),
		'layout-two'   => esc_html__( '2 columns', 'catch-wedding' ),
		'layout-three' => esc_html__( '3 columns', 'catch-wedding' ),
		'layout-four'  => esc_html__( '4 columns', 'catch-wedding' ),
	);

	return apply_filters( 'catch_wedding_sections_layout_options', $options );
}

/**
 * Returns an array of section types
 *
 * @since Catch Wedding Pro 1.0
 */
function catch_wedding_section_type_options() {
	$options = array(
		'demo'     => esc_html__( 'Demo', 'catch-wedding' ),
		'post'     => esc_html__( 'Post', 'catch-wedding' ),
		'page'     => esc_html__( 'Page', 'catch-wedding' ),
		'category' => esc_html__( 'Category', 'catch-wedding' ),
		'image'    => esc_html__( 'Custom', 'catch-wedding' ),
	);

	return apply_filters( 'catch_wedding_section_type_options', $options );
}

/**
 * Returns an array of color schemes registered for catchresponsive.
 *
 * @since Catch Wedding Pro 1.0
 */
function catch_wedding_get_pagination_types() {
	$pagination_types = array(
		'default'         => esc_html__( 'Default(Older Posts/Newer Posts)', 'catch-wedding' ),
		'numeric'         => esc_html__( 'Numeric', 'catch-wedding' ),
	);

	return apply_filters( 'catch_wedding_get_pagination_types', $pagination_types );
}

/**
 * Generate a list of all available post array
 *
 * @param  string $post_type post type.
 * @return post_array
 */
function catch_wedding_generate_post_array( $post_type = 'post' ) {
	$output = array();
	$posts = get_posts( array(
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'suppress_filters' => false,
		'posts_per_page'   => -1,
		)
	);

	$output['0']= esc_html__( '-- Select --', 'catch-wedding' );

	foreach ( $posts as $post ) {
		/* translators: 1: post id. */
		$output[ $post->ID ] = ! empty( $post->post_title ) ? $post->post_title : sprintf( __( '#%d (no title)', 'catch-wedding' ), $post->ID );
	}

	return $output;
}
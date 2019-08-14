<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package Catch_Wedding
 */
?>

<?php
$catch_wedding_layout = catch_wedding_get_theme_layout();

$sidebar = catch_wedding_get_sidebar_id();

if ( '' === $sidebar ) {
    return;
}
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">
	<?php dynamic_sidebar( $sidebar ); ?>
</aside><!-- .sidebar .widget-area -->

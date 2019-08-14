<?php
/**
 * Displays Header Right Navigation
 *
 * @package Catch_Wedding
 */
?>

<?php if ( has_nav_menu( 'social-header' ) ): ?>
	<div id="site-header-menu" class="site-secondary-menu">
			<nav id="social-secondary-navigation-top" class="social-navigation displaynone" role="navigation" aria-label="<?php esc_attr_e( 'Header Right Social Links Menu', 'catch-wedding' ); ?>">
				<?php
					wp_nav_menu( array(
						'theme_location' => 'social-header',
						'menu_class'     => 'social-links-menu',
						'depth'          => 1,
						'link_before'    => '<span class="screen-reader-text">',
						'link_after'     => '</span>' . catch_wedding_get_svg( array( 'icon' => 'chain' ) ),
					) );
				?>
			</nav><!-- #social-secondary-navigation -->
	</div><!-- #site-header-menu -->
<?php endif;
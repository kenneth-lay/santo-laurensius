<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
		// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>
<div id = "main-left" class = "span-6 last">
				<?php dynamic_sidebar( 'primary-widget-area' ); ?>
</div>
		<?php endif; // end primary widget area ?>
<?php
	// A second sidebar for widgets, just because.
?>
<div id = "main-right" class = "span-6 last">
<?php
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>

				<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
</div>
<?php endif; ?>

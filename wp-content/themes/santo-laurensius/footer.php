<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>
<!-- MEASUREMENT PURPOSES
<div class = "container">
	<div class = "fill-blue span-24 last">
		&nbsp;
	</div>
</div>
-->

<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar('footer');
?>


<!--
			<a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<a href="http://wordpress.org/" title="Semantic Personal Publishing Platform" rel="generator">Proudly powered by WordPress </a>
-->
<div id="copyright">
	<div class="container">
		<div class="span-24 last">
			<strong>Hak cipta (c) 2011-<?php echo date('Y'); ?> Seksi Komunikasi Sosial Gereja Katolik Santo Laurensius, Alam Sutera, Serpong</strong><br />
			Diijinkan mengambil konten (tanpa diberi perubahan) dengan mencantumkan tautan (<em>link</em>) ke <strong><a href="http://www.santo-laurensius.org" class="link">http://www.santo-laurensius.org/</a></strong>
		</div>
	</div>
</div>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
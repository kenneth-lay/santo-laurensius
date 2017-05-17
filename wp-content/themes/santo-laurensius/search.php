<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
	<?php 
		global $search_error;
		$search_error = TRUE;
	?>
<?php get_template_part( 'snippet', 'archive-head' ); ?>
<?php
	if ($_GET['post_type'] == 'kalender-acara')
	{
?>
		<?php printf( __( 'Hasil pencarian acara untuk "%s"', 'twentyten' ), '' . get_search_query() . '' ); ?>
<?php
	}
	else if ($_GET['post_type'] == 'dokumen-gereja')
	{
		
		echo "Hasil pencarian " . get_the_title(id_dokumen_gereja()) . " untuk \"" . urldecode($_GET['s']) . "\"";
	}
	else
	{
?>
		<?php printf( __( 'Hasil pencarian untuk "%s"', 'twentyten' ), '' . get_search_query() . '' ); ?>
<?php
	}
?>
<?php get_template_part('snippet', 'archive-foot'); ?>


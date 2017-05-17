<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<?php 
	get_template_part( 'snippet', 'archive-head' );
	/*
	$tdok_a = explode("/dokumen-gereja/", get_my_url());
	$tdok_a2 = explode("/", $tdok_a[1]);
	$tdok = $tdok_a2[0];
	$tda = explode("-", $tdok);
	foreach ($tda as $td)
	{
		if ($td != 'dan' AND $td != 'serta' AND $td != 'atau')
		{
			echo ucfirst($td) . " ";
		}
		else
		{
			echo $td;
		}
	}
	*/
	echo get_the_title(id_dokumen_gereja());
?>
<?php get_template_part('snippet', 'archive-foot'); ?>
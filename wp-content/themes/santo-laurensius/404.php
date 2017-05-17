<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

<?php 
	get_template_part( 'snippet', 'archive-head' );
?>
	<div class = "span-17" id = "empat-nol-empat" class = "recent-articles">
		<?php custom_404_error(1); ?>
<?php get_template_part('snippet', 'archive-foot'); ?>
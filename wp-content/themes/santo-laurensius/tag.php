<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<?php 
		get_template_part( 'snippet', 'archive-head' ); 
?>
				<?php
					printf( __( 'Arsip artikel dengan kata kunci "%s"', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				?>
		<?php //rewind_posts(); ?>
<?php get_template_part('snippet', 'archive-foot'); ?>
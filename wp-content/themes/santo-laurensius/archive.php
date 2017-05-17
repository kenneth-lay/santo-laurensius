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
?>
					<?php if ( is_day() ) : ?>
						<?php printf( __( 'Arsip tanggal: %s', 'twentyten' ), get_the_date() ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Arsip bulan: %s', 'twentyten' ), get_the_date('F Y') ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Arsip tahun: %s', 'twentyten' ), get_the_date('Y') ); ?>
					<?php else : ?>
						<?php _e( 'Arsip situs web', 'twentyten' ); ?>
					<?php endif; ?>
					<?php //rewind_posts(); ?>

<?php get_template_part('snippet', 'archive-foot'); ?>
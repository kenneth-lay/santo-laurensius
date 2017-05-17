<?php
/**
 * The template for displaying Author Archive pages.
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
					$author_id = get_the_author_meta( 'ID' );
					$first_name = get_the_author_meta('first_name', $author_id);
					if (get_the_author_meta('last_name', $author_id))
					{
						$last_name = ' ' . get_the_author_meta('last_name', $author_id);
					}
					else
					{
						$last_name = '';
					}	
					printf( __( 'Semua artikel oleh %s', 'twentyten' ), "<a class='url fn n' href='" . get_author_posts_url( $author_id ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . $first_name . $last_name . "</a>" ); 
					//rewind_posts();
				?>
<?php get_template_part('snippet', 'archive-foot'); ?>
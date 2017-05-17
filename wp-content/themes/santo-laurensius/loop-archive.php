<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<!--
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<?php next_posts_link( __( '&larr; Older posts', 'twentyten' ) ); ?>
		<?php previous_posts_link( __( 'Newer posts &rarr;', 'twentyten' ) ); ?>
<?php endif; ?>
-->
<?php
	global $post;
?>
<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>

<?php else : ?>
<?php
	$iter = 1; 
?>
<div class = "span-9" style = "width: 335px;">
	<?php myloop(1, $iter); ?>
</div>
<?php
	$iter = 1; 
?>
<div class = "span-9 last" style = "width: 335px;">
	<?php myloop(0, $iter); ?>
</div>

<?php endif; ?>

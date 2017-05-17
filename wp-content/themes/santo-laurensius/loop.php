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
		<h1><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
		<?php get_search_form(); ?>

<?php endif; ?>

<?php
	/* Start the Loop.
	 *
	 * In Twenty Ten we use the same loop in multiple contexts.
	 * It is broken into three main parts: when we're displaying
	 * posts that are in the gallery category, when we're displaying
	 * posts in the asides category, and finally all other posts.
	 *
	 * Additionally, we sometimes check for whether we are on an
	 * archive page, a search page, etc., allowing for small differences
	 * in the loop on each template without actually duplicating
	 * the rest of the loop that is shared.
	 *
	 * Without further ado, the loop:
	 */ ?>
<?php $iter = 1; ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php $ptags = wp_get_post_tags($post->ID); ?>
<?php /* How to display posts in the Gallery category. */ ?>

	<?php if ( in_category( _x('gallery', 'gallery category slug', 'twentyten') ) ) : ?>
			<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<?php twentyten_posted_on(); ?>

<?php if ( post_password_required() ) : ?>
				<?php the_content(); ?>
<?php else : ?>
<?php
	$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
	$total_images = count( $images );
	$image = array_shift( $images );
	$image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
?>
					<a href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>

				<p><?php printf( __( 'This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten' ),
						'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
						$total_images
					); ?></p>

				<?php the_excerpt(); ?>
<?php endif; ?>

				<a href="<?php echo get_term_link( _x('gallery', 'gallery category slug', 'twentyten'), 'category' ); ?>" title="<?php esc_attr_e( 'View posts in the Gallery category', 'twentyten' ); ?>"><?php _e( 'More Galleries', 'twentyten' ); ?></a>
				|
				<?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '|', '' ); ?>

<?php /* How to display posts in the asides category */ ?>

	<?php elseif ( in_category( _x('asides', 'asides category slug', 'twentyten') ) ) : ?>

		<?php if ( is_archive() || is_search() ) : // Display excerpts for archives and search. ?>
			<?php the_excerpt(); ?>
		<?php else : ?>
			<?php the_content( __( 'Continue reading &rarr;', 'twentyten' ) ); ?>
		<?php endif; ?>

				<?php twentyten_posted_on(); ?>
				|
				<?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '| ', '' ); ?>

<?php /* How to display all other posts. */ ?>

	<?php else : ?>
		<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
			<div class = "span-8">
		<?php endif; ?>
			<div class = "excerpt-instance hover-cream fill-white">
				<a class = "excerpt-title" href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				<br />
					<?php if ( count( get_the_category() ) ) : ?>
						<?php printf( __( '<strong>Kategori: <span style = "color: #f86017;">%2$s</span></strong>', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', print_category_hierarchy() ); ?>
					<?php endif; ?>
					<br />
				<span class = "fore-reddish-orange"><strong><?php twentyten_posted_on(); ?></strong></span>

		<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
				<br />
				<?php the_excerpt(); ?>
		<?php else : ?>
				<?php //the_content('Selengkapnya &rarr;'); ?>
					<br />
				<?php the_excerpt(); ?>
				<!--
				<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) ); ?>
				-->
					<div style = "background-color: #FFCC00; margin-top: 5px; padding: 2px 5px 2px 5px;">
					<?php
						$link_komentar = '';
						if (get_comments_number($post->ID) == 0)
						{
							if (is_user_logged_in())
							{
								$link_komentar = '#form-komentar';
							}
							else
							{
								$link_komentar = '#komentar-login';
							}
						}
						else
						{
							$link_komentar = '#comments';
						}
						//echo $link_komentar;
					?>
						<a class = "link-extra-maroon" href = "<?php echo get_permalink() . $link_komentar; ?>"><?php comments_number('0 komentar', '1 komentar', '% komentar' ); ?></a>
						<?php
							if (count($ptags))
							{
						?>	
								&nbsp; | &nbsp;Kata kunci:&nbsp;
						<?php
								$k = 0;
								foreach ($ptags as $pt)
								{
									if ($k < count($ptags) - 1)
									{
						?>	
										<a class = "link-extra-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>,
						<?php
									}	
									else
									{
						?>		
										<a class = "link-extra-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>
						<?php
									}
									$k = $k + 1;
								}
							}
						?>
					</div>
		<?php endif; ?>
				</div>
		<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
			</div>
		<?php endif; ?>
					<?php
						$tags_list = get_the_tag_list( '', ', ' );
						if ( $tags_list ):
					?>
						<?php //printf( __( '<em>Tags</em> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					
					<?php endif; ?>
					<!--
					<?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% komentar', 'twentyten' ) ); ?>
					<?php edit_post_link( __( 'Edit', 'twentyten' ), '| ', '' ); ?>
					-->

			<?php comments_template( '', true ); ?>

		<?php endif; // This was the if statement that broke the loop into three parts based on categories. ?>
<?php endwhile; // End the loop. Whew. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<!--
				<?php next_posts_link( __( '&larr; Older posts', 'twentyten' ) ); ?>
				<?php previous_posts_link( __( 'Newer posts &rarr;', 'twentyten' ) ); ?>
				-->
				<?php 
					$archives = explode("</li>", wp_get_archives('type=monthly&echo=0'));
					$archives2 = $archives[0];
					$archives3 = str_ireplace("<li>", "", $archives2);
					$archives3 = str_ireplace("<a href='", "", $archives3);
					$archives4 = explode("'", $archives3);
				?>
				<a href = "<?php echo $archives4[0]; ?>" class = "link-lihat-arsip">Lihat arsip artikel yang ada dalam sistem -></a>
<?php endif; ?>
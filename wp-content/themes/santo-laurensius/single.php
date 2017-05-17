<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<br />
<div class = "container main-content">
	<div class = "span-6 main-left">
		<?php get_sidebar(); ?>
	</div>
	<div class="span-17 last span-18_second-column">
		<div class="coltitle">
			<?php
				echo print_category_hierarchy();
			?>
		</div>
		<br />
		<?php
			if ( has_post_thumbnail() ) 
			{
				the_post_thumbnail(array(695,695));
				echo "<br /><br />";
			} 
		?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<div class = "prepend-small">
			<h2 class = "art-title"><?php the_title(); ?></h2>
			<div class = "prepend-top-negative-medium">&nbsp;</div>
			<h4 class = "box-small-4 art-subtitle">
				<?php twentyten_posted_on(); ?>
				&nbsp; | &nbsp;
			</h4>
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
			?>
			<h4 class = "box-small-4 art-subtitle">
				<a class = "link-maroon" href = "<?php global $post; echo get_permalink($post->ID) . '/' . $link_komentar; ?>"><?php comments_number('0 komentar', '1 komentar', '% komentar' ); ?></a>
			</h4>
			<br />
			<div class = "prepend-top-negative-large">&nbsp;</div>
			<?php
				global $post;
				$ptags = wp_get_post_tags($post->ID);
				//print_r($ptags);
				if (count($ptags))
				{
			?>
			<div class = "container" style = "width: 670px; padding-left: 0px; margin-left: 0px;">
				<div class = "span-17" style = "padding-left: 0px; margin-left: 0px;">
					<h4 class = "span-1 art-subtitle box-small-4" style = "width: 80px;">
						Kata kunci:
					</h4>
					<h4 class = "span-14 art-subtitle box-small-4" style = "padding: 5px 0px 5px 0px;">
						<?php
							$k = 0;
							foreach ($ptags as $pt)
							{
								//print_r($pt);
								if ($k < count($ptags) - 1)
								{
						?>	
						<a class = "link-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>,
						<?php
								}	
								else
								{
						?>
						<a class = "link-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>
						<?php
								}
									$k = $k + 1;
								}
							?>
					</h4>
				</div>
			</div>
			<?php
				}
				else
				{
			?>
			<br />
			<?php
				}
						if (get_post_meta($post->ID, 'bool-paragraf-pengantar', true) == 'Ya')
						{
					?>
			<div class = "full-content paragraf-pengantar">
					<?php
						}
						else
						{
					?>
			<div class = "full-content">
					<?php
						}
					?>
						<?php the_content(); ?>
			</div>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) ); ?>
<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>

			<div class = "span-17 last" id = "author-description">
				<div class = "span-2">
									<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
				</div>
				<div class = "span-14 last">
					<span class = "art-title size-medium" style = "display: block; margin-bottom: -15px;">
						<?php
							if (get_the_author_meta('user_lastname') != '')
							{
								$spasi = ' ';
							}
							else
							{
								$spasi = '';
							}
							if (get_the_author_meta('user_firstname') != '')
							{
						?>
							<?php printf( esc_attr__( 'Tentang %s%s', 'twentyten' ), get_the_author_meta('user_firstname'), $spasi . get_the_author_meta('user_lastname') ); ?>
						<?php 
							}
							else
							{
						?>
							<?php printf( esc_attr__( 'Tentang %s:', 'twentyten' ), get_the_author()); ?>
						<?php
							}
						?>
					</span>
					<br />
					<p class = "align-justify">
						<?php the_author_meta( 'description' ); ?>
					</p>
					<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
						<?php printf( __( 'Lihat semua artikel oleh %s%s &rarr;', 'twentyten' ), get_the_author_meta('user_firstname'), $spasi . get_the_author_meta('user_lastname') ); ?>
					</a>
				</div>
			</div>
<?php endif; ?>
		<br />
		<br />
		<div id = "previous-next" class="span-17 last span-18_second-column" style = "width: 690px;">
			<?php
				$nepo = get_next_post(); 
				$nepoid = $nepo->ID;
				$next_post_url = get_permalink($nepoid);
				$prpo = get_previous_post(); 
				$prpoid = $prpo->ID;
				$prev_post_url = get_permalink($prpoid);
				$prpolink = "window.location.href='" . $prev_post_url . "'";
				$nepolink = "window.location.href='" . $next_post_url . "'";
			?>
			<div class = "span-8" style = "width: 325px; padding-right: 10px;">
				<?php previous_post_link( '%link', '' . _x( '&larr;', 'Previous post link', 'twentyten' ) . ' %title' ); ?>
				<?php 
					if ($prpo->post_title == '')
					{
						echo '&nbsp;';
					}
				?>
			</div>
			<div class = "span-9" style = "text-align: right; width: 325px; padding-right: 10px;">
				<?php next_post_link( '%link', '%title ' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '' ); ?>
				<?php 
					if ($nepo->post_title == '')
					{
						echo '&nbsp;';
					}
				?>
			</div>
		</div>
		<?php
			if (get_the_author_meta('description'))
			{
		?>
		<div class = "span-18 last" style="height: 30px;">&nbsp;</div>
		<?php
			}
		?>
		<br />
		<br />
		<br />
		<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>
		</div>
	</div>
</div>


<?php get_footer(); ?>

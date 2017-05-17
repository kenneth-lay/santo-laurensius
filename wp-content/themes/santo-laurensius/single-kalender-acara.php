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
		<div class="coltitle">Acara dan Rencana</div>
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
				<?php
					$nomen = '';
					if (get_the_author_meta( 'first_name' ) != '')
					{
						$nomen = get_the_author_meta( 'first_name' );
					}
					else
					{
						$nomen = get_the_author_meta( 'display_name' );
					}
					if (get_the_author_meta( 'last_name' ) != '')
					{
						$nomen = $nomen . ' ' . get_the_author_meta( 'last_name' );
					}
					printf( __( '<span class="%1$s">Disusun oleh:</span> %2$s', 'twentyten' ),
						'meta-prep meta-prep-author',
							sprintf ( '<span class="author vcard"><a class="url fn n link-maroon" href="%1$s" title="%2$s">%3$s</a></span>',
								home_url('/kalender-acara/penyusun/') . urlencode(get_the_author_meta('login')),
								sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), $nomen ), 
								$nomen
							)
						);
				?>
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
			<div id = "meta-acara">
				<br />
			<?php
				global $bulan_indonesia;
				$tanggal_mulai = explode('-', get_post_meta($post->ID, 'tanggal_mulai', true));
				$tanggal_selesai = explode('-', get_post_meta($post->ID, 'tanggal_selesai', true));
				echo '<div class = "span-2"><strong>Mulai:</strong></div>' ?><a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . $tanggal_mulai[2] . '/'; ?>"><?php echo $tanggal_mulai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . '">' . $bulan_indonesia[$tanggal_mulai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . '">' . $tanggal_mulai[0] . '</a>';
				if (get_post_meta($post->ID, 'jam_mulai', true) != '')
				{
					echo ', pukul ' . get_post_meta($post->ID, 'jam_mulai', true);
				}
				echo '<br />';
				echo '<div class = "span-2"><strong>Selesai:</strong></div>' ?><a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . $tanggal_selesai[2] . '/'; ?>"><?php echo $tanggal_selesai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . '">' . $bulan_indonesia[$tanggal_selesai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . '">' . $tanggal_selesai[0] . '</a>';
				if (get_post_meta($post->ID, 'jam_selesai', true) != '')
				{
					echo ', pukul ' . get_post_meta($post->ID, 'jam_selesai', true);
				}
				echo '<br />';
				echo '<div class = "span-2"><strong>Tempat:</strong></div>'; ?> <a href = "<?php echo home_url('/kalender-acara/tempat/') . urlencode(get_post_meta($post->ID, 'tempat', true)); ?>" class = "link-maroon"> <?php echo get_post_meta($post->ID, 'tempat', true) . '</a>';
				echo '<br />';
				echo '<br />';
			?>
			</div>
			<div class = "single-column">
				<?php
					echo the_field('deskripsi_acara');
				?>
			</div>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) ); ?>
		<br />
		<br />
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
			<?php
				if ($prpo->post_title != '' OR $nepo->post_title != '')
				{
			?>
		<div id = "previous-next" class="span-17 last span-18_second-column" style = "width: 690px;">
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

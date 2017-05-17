<?php
/**
 * Template Name: You Catholic?
 */
 	session_start();
 	$okei = FALSE;
 	global $current_user;
	global $wpdb;
    get_currentuserinfo();
    $uid = $current_user->ID;
	$verif = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"data-umat\" AND post_status = \"publish\" AND post_author = $uid");
	foreach ($verif as $v)
	{
		$okei = TRUE;
	}
if (is_user_logged_in() AND !current_user_can('edit_pages') AND !$okei AND get_user_meta($current_user->ID, 'katolik', true) == 'bukan')
{
get_header(); 
global $post;
?>

<?php
	function get_post_parents($prnt, &$halamans, &$printout, &$counter)
	{
		foreach ($halamans as $halaman) 
		{
  			if ($prnt == $halaman->ID)
  			{
  				$counter = $counter + 1;
  				$printout = '<a href = "' . get_page_link($halaman->ID) . '" class = "span-5 last breadcrumb-instance" style = "replaceable">' . $halaman->post_title . '</a><div class = "span-1 last panah">></div>' . $printout;
  				get_post_parents($halaman->post_parent, $halamans, $printout, $counter);
  			}
  		}
  	}
?>
<br />
<div class = "container main-content">
	<div class="span-17 span-17-680 main-left">
		<div class = "prepend-small">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div class = "fill-cream border-bottom-top" style = "margin-bottom: 20px; height: auto;">
				<div class = "container">
					<?php
						global $post;
						$printout = '';
//						print_r($halamans);
						$counter = 1;
						$width = 190;
						get_post_parents($post->post_parent, $halamans, $printout, $counter);
						$printout = $printout . '<a class = "span-5 last breadcrumb-location" style = "replaceable">' . $post->post_title . '</a>';
						if ($counter > 2)
						{
							$width = 660 - ($counter - 1) * 20 - $counter * 10;
							$width = ceil($width / $counter);
							echo str_ireplace("replaceable", "width: " . $width . "px;", $printout);
							//echo '<br /><br />' . $width;
						}
						else
						{
							echo str_ireplace("replacable", "", $printout);
						}
						$counter = 0;
					?>
				</div>
			</div>
			<h2 class = "art-title">
				<?php if ( is_front_page() ) { ?>
				<?php the_title(); ?>
				<?php } else { ?>	
					<?php the_title(); ?>
				<?php } ?>	
			</h2>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			<div class = "single-column">			
				<div style = "font-size: 13px;">
					<?php the_content(); ?>
				</div>
				<?php
					require_once(ABSPATH . WPINC . '/registration.php');  
				?>
				<form action = "<?php echo site_url() . '/kirim-anda-katolik/' ?>" method="post">
					<?php pernyataan_iman(1); ?>
					<div id = "vfiller" class = "span-17">&nbsp;</div>
					<div class = "registration">
						<input type="submit" id="submitbtn" name="submit" value="Ya, Saya Katolik" />
					</div>
				</form>
			</div>
		</div>
		<br />
		<br />
	</div>
	<div class = "span-6 main-right last">
		<?php get_sidebar(); ?>
	</div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
<?php
	}
	else
	{
		wp_redirect(site_url());
		exit();
	}
?>

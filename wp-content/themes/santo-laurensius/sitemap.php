<?php
	/*
		Template Name: Site Map
	*/
?>

<?php
/**
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

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
			<?php
				if ( has_post_thumbnail() ) 
				{
					the_post_thumbnail(array(655,655));
					echo "<br /><br />";
				} 
			?>
			<h2 class = "art-title">
				<?php if ( is_front_page() ) { ?>
				<?php the_title(); ?>
				<?php } else { ?>	
					<?php the_title(); ?>
				<?php } ?>	
			</h2>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			<?php
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
				<?php sitemap_instance(); ?>
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
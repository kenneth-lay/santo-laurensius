<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<?php
	session_start();
	$sduri = get_stylesheet_directory_uri() . '/';
	$slideruri = $sduri . 'jcobb-bjqs/';
?>
<script type = "text/javascript">
	$(document).ready(function(){
		$("#slider").bjqs({
			'height' : 310,
			'width' : 430,
			'animationDuration' : 200,
			'showMarkers' : false,
			'centerControls' :true,
			'hoverPause': true,
			'nextText': '>',
			'prevText': '<',
			'useCaptions' : true,
			'keyboardNav' : true,
			'animation' : 'slide',
			'rotationSpeed': 5000
		});
});
</script>
<div class = "container main-content" >
	<br />
			<?php
				if (isset($_SESSION['berhasil']) AND $_SESSION['berhasil'] != '')
				{
			?>
			<div class = "success-message sm-24">
				<?php echo $_SESSION['berhasil']; ?>
			</div>
			<div class = "prepend-top-negative">&nbsp;</div>	
				<?php
						unset($_SESSION['berhasil']);
					}
				?>
			
	<div id = "main-left" class = "span-11">
			<div class="coltitle">Artikel-artikel Terkini</div>
			<div class="prepend-top-negative-small">&nbsp;</div>
			<div id = "slider" style = "width: 430px; height: 310px; position: relative;">
				<ul class="bjqs" style = "width: 430px; height: 310px; display: block;">
					<?php
						$args = array(
    						'meta_query' => array(
        						array(
            						'key' => '_thumbnail_id',
        					)
    					),
    					'posts_per_page' => 5,
    					
 						);
 						// Create a new filtering function that will add our where clause to the query
						function filter_where( $where = '' ) {
							$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-183 days')) . "'" . " AND post_date <= '" . date('Y-m-d') . "'";
							return $where;
						}
						add_filter( 'posts_where', 'filter_where' );
						$my_query = new WP_Query( $args );
						remove_filter( 'posts_where', 'filter_where' );
					?>
					<?php
						$itung = 0;
					?>
					<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
						<?php global $post; ?>
						<?php if ( has_post_thumbnail()) : ?>
							<?php $thumb_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
							<?php
								list($width, $height) = getimagesize($thumb_url);
								if ($height >= 310)
								{
									$itung = $itung + 1;
							?>
									<li style = "height: 310px; width: 430px;"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
							<?php
									if ($width > $height)
									{
										if ($width / $height >= 430.00 / 310.00)
										{
							?>
											<img title = "<?php echo get_the_title($post->ID); ?>" style = "height: 310px; width: auto;" src = "<?php echo $thumb_url; ?>" />
							<?php
										}
										else
										{
							?>
											<img title = "<?php echo get_the_title($post->ID); ?>" style = "width: 430px; height: auto;" src = "<?php echo $thumb_url; ?>" />
							<?php
										}
									}
									else
									{
							?>
										<img title = "<?php echo get_the_title($post->ID); ?>" style = "width: 430px; height: auto;" src = "<?php echo $thumb_url; ?>" />
							<?php
									}
							?>
									</a></li>
							<?php
								}
							?>
						<?php endif; ?>
					<?php endwhile; ?>
					<?php
						if ($itung == 0)
						{
					?>	
							<li style = "height: 310px; width: 430px;">
							<a>
								<img src = "<?php echo get_stylesheet_directory_uri() . '/images/gereja2.jpeg'; ?>" />
							</a>
							</li>
					<?
						}
					?>
					
					<!--
					<li style = "height: 310px; width: 430px;"><a href = "###"><img title = "Kursi Tua" src = "<?php echo $sduri; ?>images/timthumb.jpeg" /></a></li>
					<li style = "height: 310px; width: 430px;"><a href = "###"><img title = "Spion Mobil" src = "<?php echo $sduri; ?>images/mirror.jpeg" /></a></li>
					<li style = "height: 310px; width: 430px;"><a href = "###"><img title = "Rumput" src = "<?php echo $sduri; ?>images/springwheat_cropped.png" /></a></li>
					-->
				</ul>
			</div>
			<div class="prepend-top-negative-large">&nbsp;</div>
			<div class = "span-11 last" id = "list-excerpts" >
				<div class="prepend-top-negative-small">&nbsp;</div>
				<?php
				/* Run the loop to output the posts.
				 * If you want to overload this in a child theme then include a file
				 * called loop-index.php and that will be used instead.
				 */
				 get_template_part( 'loop', 'archives' );
			?>
			</div>
	</div>
		<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
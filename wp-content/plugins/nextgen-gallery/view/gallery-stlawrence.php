<?php 
/**
Template Page for the gallery overview

Follow variables are useable :

	$gallery     : Contain all about the gallery
	$images      : Contain all images, path, title
	$pagination  : Contain the pagination content

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
**/
?>
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if (!empty ($gallery)) : ?>
<?php 
	$galleries = array();
	$query = "SELECT * FROM wp_ngg_gallery WHERE gid != $gallery->ID";
    $result = mysql_query($query) or die(mysql_error());
	global $post;
	$nggpage  = get_query_var('nggpage');
	if (!$nggpage)
	{
		$nggpage = 1;
	}
	$ngg_options = nggGallery::get_option('ngg_options');
    $maxElement  = $ngg_options['galImages'];
    $gal_offset = ($nggpage - 1) * $maxElement;
    //echo $maxElement;
    $iter = 0;
    $gal_count = 0;
	while($row = mysql_fetch_array($result))
	{
		$page = get_page($row['pageid']);
		if ($page->post_parent == $post->ID)
		{
				$gal_count = $gal_count + 1;
		}
	}
	//print_r($galleries);
	$need_close = FALSE;
	$k = 0;
?>
<p><?php echo $gallery->description; ?></p>
<div class="ngg-galleryoverview" id="<?php echo $gallery->anchor ?>" style = "margin-top: -10px;">
	<?php
 		$i = 0;
 		global $post;
 		global $halamans;
 		//echo $gal_offset;
 		//echo $maxElement;
 		$big = FALSE;
 		foreach ($halamans as $halaman)
 		{
 			if ($post->ID == $halaman->post_parent)
 			{
 				if ($iter < $gal_offset)
				{
					$iter = $iter + 1;
				}
				else if ($i < $maxElement)
				{	
					if ($k == 0)
					{
						echo '<div style = "height: 173px;">';
						$need_close = TRUE;
						$big = TRUE;
					}
					$k = $k + 1;
					echo '<a href = "' . get_permalink($halaman->ID) . '">';
 					if ($i == 0)
 					{
 	?>	
						<div class = "span-3 album-thumbnail album-thumbnail-first ngg-gallery-thumbnail-box" >
			<?php 
					}
					else
					{
			?>
						<div class = "span-3 album-thumbnail ngg-gallery-thumbnail-box">
			<?php
					}	
			?>
							<img src = "<?php echo get_template_directory_uri() . '/images/etc/galleryicon.png'; ?>" />

			<?php
						echo '<strong><a class = "link-maroon" href = "' . get_permalink($halaman->ID) . '">' . $halaman->post_title . '</a></strong>';

			?>
							
						</div>

 	<?php
 					echo '</a>';
 					if ($k == 4)
 					{
 						$k = 0;
 						echo '</div>';
 						$big = FALSE;
 					}
 					$i = $i + 1;
 				}
 			}
 		}
 	?> 	
	<!-- Thumbnails -->
	<?php $i = 0; ?>
	<?php foreach ( $images as $image ) : ?>
	<?php
		if ($k == 0)
		{
			echo '<div style = "height: 100px;">';
			$need_close = TRUE;
			$big = FALSE;
		}
		$k = $k + 1;
	?>
	<div id="ngg-image-<?php echo $image->pid ?>" class="ngg-gallery-thumbnail-box" <?php echo $image->style ?> >
		<div class="ngg-gallery-thumbnail" style = "margin-top: 6px;" >
			<a href="<?php echo $image->imageURL ?>" title="<?php echo $image->alttext . '&||&' . $image->description ?>" <?php echo $image->thumbcode ?> >
				<?php if ( !$image->hidden ) { ?>
				<img title="<?php echo $image->alttext ?>" alt="<?php echo $image->alttext ?>" src="<?php echo $image->thumbnailURL ?>" <?php echo $image->size ?> />
				<?php } ?>
			</a>
		</div>
	</div>
	
	<?php if ( $image->hidden ) continue; ?>
	<?php
		if ($k == 4 && $big)
 		{
 			$k = 0;
 			echo '</div>';
 			$big = FALSE;
 			$need_close = FALSE;
 		}
 		else if ($k == 5 && !$big)
 		{
 			$k = 0;
 			echo '</div>';
 			$need_close = FALSE;
 		}
	?>
	<?php $i = $i + 1; ?>

 	<?php endforeach; ?>
 	<?php
 		if ($need_close)
 		{
 			$need_close = FALSE;
 			$big = FALSE;
 			echo '</div>';
 		}
 	?>
	<!-- Pagination -->
 	<?php //echo $pagination; ?>
</div>

<?php endif; ?>
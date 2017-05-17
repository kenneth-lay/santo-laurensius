<?php
/**
 * The template for displaying attachments.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<script type="text/javascript">
	function redirector($uri)
	{
   	 	window.location = $uri;
	}
</script>

<?php 
	global $attachment_page;
	$attachment_page = TRUE;
	global $mymeta;
	global $chek;
	$mymeta = wp_get_attachment_metadata();
	$path = wp_get_attachment_url();
	$path_array = explode(".", $path);
	$ekstensi = $path_array[count($path_array) - 1];
	if ( have_posts() )
	{
		the_post();
		if (!wp_attachment_is_image())
		{
			echo '<script type = "text/javascript">redirector(\'' . $path . '\')</script>';
		}
		else
		{
			get_header(); 
			get_template_part( 'snippet', 'archive-head' );
?>
			Nama berkas: "<?php the_title(); ?><?php echo "." . $ekstensi . "\"";?>
					
<?php 
			get_template_part( 'snippet', 'archive-foot' );
		}
	}
?>
<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<?php 
	global $category_error;
	$category_error = TRUE;
?>
<?php 
	get_template_part( 'snippet', 'archive-head' ); 
?>
				<?php
				
					$pageURL = get_my_url();
 					$pageURL_array = explode("category/", $pageURL);
 					$cat_slug_array = explode("/", $pageURL_array[1]);
 					$cat_slug = '';
 					$temp_categories = array();
 					$i = 0;
 					//print_r($cat_slug_array);
 					foreach ($cat_slug_array as $csa)
 					{
 						if ($i < count($cat_slug_array) - 1)
						{
							$catg = get_category_by_slug($csa);
							array_push($temp_categories, $catg);
							$i = $i + 1;
						}
						else
						{
							break;
						}
 					}
 					//echo $cat_slug;
					$categories = $temp_categories;
					$output = '';
					//print_r($categories);
					if($categories)
					{
						foreach($categories as $category) 
						{
							if ($category->parent == 0)
							{
								$output .= '<a href="' . get_category_link($category->term_id ).'" title="' . esc_attr( sprintf( __( "Lihat semua artikel di kategori %s" ), $category->name ) ) . '">'.$category->cat_name.'</a>';
								print_category_children($categories, $output, $category->term_id);
							}
						}
					}
					printf( __( 'Semua artikel berkategori: %s', 'twentyten' ), '' . $output . '' );
					//rewind_posts();
				?>
<?php get_template_part('snippet', 'archive-foot'); ?>
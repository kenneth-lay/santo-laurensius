			<?php global $chek; ?>
			<?php global $search_error; ?>
			<?php global $category_error; ?>
			<?php global $attachment_page; ?>
			<?php if ($chek OR $search_error OR $category_error OR $attachment_page) : ?>
				</h2>
			</div>
			<div class="prepend-top-negative-medium">&nbsp;</div>
			<?php endif; ?>

			<?php if ($search_error): ?>
				<?php $search_error = FALSE; ?>
				<div class="prepend-top-negative">&nbsp;</div>
				<div class = "search-error-message">
					Maaf, kami tidak berhasil menemukan artikel yang anda cari.
				</div>
				<div class = "span-17" id = "search-fails" class = "recent-articles">
				<?php custom_404_error(0); ?>
			<?php endif; ?>
			
			<?php if ($attachment_page) : ?>
				<?php
					global $mymeta;
				?>

				<?php if ( wp_attachment_is_image() ) :
					echo '<br />';
					$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
					foreach ( $attachments as $k => $attachment ) 
					{
						if ( $attachment->ID == $post->ID )
						break;
					}
					$k++;
					// If there is more than 1 image attachment in a gallery
					if ( count( $attachments ) > 1 ) 
					{
						if ( isset( $attachments[ $k ] ) ) // get the URL of the next image attachment
						$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
					else
						$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
				} 
				else 
				{
					// or, if there's only 1 image attachment, get the URL of the image
					$next_attachment_url = wp_get_attachment_url();
				}
				?>
				
				<p class = "autorsize">
					<?php
						$attachment_size = apply_filters( 'twentyten_attachment_size', 9999 );
						echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
					?>
				</p>
				<?php
					if ( wp_attachment_is_image() ) 
					{
				?>
						<div class = "span-17 last" id = "picture-description">
							<div class = "span-3" style = "margin-right: 15px;">
								<img src = "<?php echo get_template_directory_uri() . "/images/etc/imageicon.png"; ?>" />
							</div>
							<div class = "span-13 last">
								<span class = "art-title size-medium" style = "display: block; margin-bottom: -15px;">
									Tentang gambar ini:
								</span>
								<br />
								<p class = "align-justify">
									<?php
										printf( __( 'Ukuran gambar ini adalah <strong style = "color: #CC3300;">%s pixels</strong>. ', 'twentyten'),
											sprintf( '%1$s&times;%2$s',
												$mymeta['width'],
												$mymeta['height']
											)
										);
										echo 'Akibat penyesuaian dengan tampilan situs,
										ukuran gambar yang anda lihat sekarang mungkin tidak sesuai dengan deskripsi di atas.
										Namun sistem telah menampilkan berkas gambar diatas dengan resolusi penuh.';
										echo '<br /><br />';
										printf( __( 'Unduh gambar ini: <a href = "%s">Klik disini</a>', 'twentyten'),
											wp_get_attachment_url()
										);
									?>
								</p>
							</div>
						</div>
					<?php
					}
				?>
				<div class = "span-17 last span-18_second-column" style = "width: 670px;">&nbsp;</div>
				<div class="span-17 last span-18_second-column previous-next" style = "width: 680px;">
					<div class = "span-8" style = "width: 315px; padding-right: 10px; float: left;">
						<?php previous_image_link( false, '&larr; Gambar yang lebih lama' ); ?>
					</div>
					<div class = "span-8" style = "text-align: right; width: 315px; padding-right: 10px; margin-right: 20px; float: right;">
						<?php next_image_link( false, 'Gambar yang lebih baru &rarr;' ); ?>
					</div>
				</div>
				<?php else : ?>
					<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
				<?php endif; ?>	
				<?php $mymeta = array(); ?>
			<?php endif; ?>
			
			
			<?php if ($chek) : ?>
		<?php
			// If a user has filled out their description, show a bio on their entries.
			$pageURL = get_my_url();
			if ( get_author_posts_url(get_the_author_meta( 'ID' )) == $pageURL AND get_the_author_meta( 'description' ) ) : ?>
				<!--
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
					<h2><?php printf( __( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
					<?php the_author_meta( 'description' ); ?>
				-->	
				<div class = "span-17" id = "author-description-2" style = "width: 650px;">
					<div class = "span-3">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 96 ) ); ?>
					</div>
					<div class = "span-13 last" style = "width: 525px;">
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
					</div>
				</div>
		<?php endif; ?>	
		<?php
 			$pageURL_array = explode("category/", $pageURL);
 			$cat_slug_array = explode("/", $pageURL_array[1]);
			$catg = get_category_by_slug($cat_slug_array[count($cat_slug_array) - 2]);
		?>
		<?php
			if ($catg->description != '')
			{
		?>
				<div class = "span-17" id = "category-description" style = "width: 650px;">
					<?php echo $catg->description; ?>
				</div>
		<?php
			}
		?>
		<?php endif; ?>
			<?php if ($category_error): ?>
				<?php $category_error = FALSE; ?>
				<div class="prepend-top-negative">&nbsp;</div>
				<div class = "search-error-message">
					Maaf, belum ada artikel pada kategori ini.
				</div>
				<div class = "span-17" id = "search-fails" class = "recent-articles">
				<?php custom_404_error(0); ?>
			<?php endif; ?>


<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	 			
	if ($chek)
	{
		rewind_posts();
	}
	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-archives.php and that will be used instead.
	 */
	 if (!$attachment_page AND substr_count(get_my_url(), "dokumen-gereja") == 0)
	 {
	 	get_template_part( 'loop', 'archive' );
	 }
	 else
	 {
	 	$attachment_page = FALSE;
	 	if (substr_count(get_my_url(), "/dokumen-gereja/") OR substr_count(get_my_url(), 'post_type=dokumen-gereja'))
	 	{
	 		get_template_part( 'loop', 'dokumen-gereja' );
	 	}
	 }
?>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div class = "span-17 span-17-680" style = "width: 680px;">&nbsp;</div>
				<div class = "span-17 span-17-680 fill-fransiscan" style = "width: 680px;">
					<?php
					if (!substr_count(get_my_url(), "/dokumen-gereja/") AND !substr_count(get_my_url(), 'post_type=dokumen-gereja'))
					{
					?>
						<div class = "span-8 last" id = "older-posts-link">
							<?php
								if (get_next_posts_link() != '')
								{
									if (substr_count(get_my_url(), "/kalender-acara/") OR substr_count(get_my_url(), 'post_type=kalender-acara'))
									{
										next_posts_link( __( '&larr; Entri selanjutnya', 'twentyten' ) ); 
									}
									else
									{
										next_posts_link( __( '&larr; Entri yang lebih lama', 'twentyten' ) ); 
									}
								}
								else
								{
									echo '&nbsp;';
								}
							?>
						</div>
						<div class = "span-9 last" id = "newer-posts-link">
						<?php
							if (substr_count(get_my_url(), "/kalender-acara/") OR substr_count(get_my_url(), 'post_type=kalender-acara'))
							{
								previous_posts_link( __( 'Entri sebelumnya &rarr;', 'twentyten' ) );
							}
							else
							{
								previous_posts_link( __( 'Entri yang lebih baru &rarr;', 'twentyten' ) );
							}
						?>
						</div>
					<?php
					}
					else
					{
					?>
						<div class = "span-8 last" id = "older-posts-link">
							<?php previous_posts_link( __( '&larr; Sebelumnya', 'twentyten' ) ); ?>
						</div>
						<div class = "span-9 last" id = "newer-posts-link" style = "float: right; margin-right: 20px;">
							<?php
								if (get_next_posts_link() != '')
								{
									next_posts_link( __( 'Selanjutnya &rarr;', 'twentyten' ) ); 
								}
								else
								{
									echo '&nbsp;';
								}
							?>
						</div>
					<?php
					}
					?>
				</div>
<?php endif; ?>
	</div>
	<div class = "span-6 main-right last">
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
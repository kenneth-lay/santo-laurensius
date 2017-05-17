<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	global $query_string;
	$additional_string = '&posts_per_page=10';
	global $chek;
	global $search_error;
	global $category_error;
	global $attachment_page;
	if (!$attachment_page AND have_posts() )
	{
		$chek = 1;
		the_post();
		$search_error = FALSE;
		$category_error = FALSE;
	}
	else if (!$attachment_page)
	{
		$chek = FALSE;
	}
	global $sttg, $tgl, $bln, $thn, $penyusun, $tpt;
	if ($thn == '')
	{
		$thn = '9999';
	}
	if ($bln == '')
	{
		$bln = '01';
	}
	if ($tgl == '')
	{
		$tgl = '01';
	}		
	if ($sttg == 'mulai')
	{
		$args = array(
    	  'meta_key' => 'tanggal_mulai',
    	  'meta_compare' => '>=',
    	  'meta_value' => $thn . '-' . $bln . '-' . $tgl,
    	  'post_type' => 'kalender-acara',
    	  'paged' => get_query_var('paged'),
    	  'posts_per_page' => 10,
    	  'orderby' => 'meta_value',
    	  'order' => 'ASC'
		);
		query_posts( $args );
	}
	else if ($sttg == 'selesai')
	{
		$args = array(
    	  'meta_key' => 'tanggal_selesai',
    	  'meta_compare' => '<=',
    	  'meta_value' => $thn . '-' . $bln . '-' . $tgl,
    	  'post_type' => 'kalender-acara',
    	  'paged' => get_query_var('paged'),
    	  'posts_per_page' => 10,
    	  'orderby' => 'meta_value',
    	  'order' => 'ASC'
		);
		query_posts( $args );
	}
	else if ($penyusun != '')
	{
		$userobj = get_user_by('login', $penyusun);
		query_posts( $query_string . $additional_string . '&author=' . $userobj->ID . '&orderby=meta_value&meta_key=tanggal_mulai&order=DESC&posts_per_page = 10');
	}
	else if ($tpt != '')
	{
		$args = array(
    	  'meta_key' => 'tempat',
    	  'meta_compare' => '=',
    	  'meta_value' => $tpt,
    	  'post_type' => 'kalender-acara',
    	  'paged' => get_query_var('paged'),
    	  'posts_per_page' => 10,
    	  'orderby' => 'tanggal_mulai',
    	  'order' => 'ASC'
		);
		query_posts( $args );
	}
	else if (substr_count(get_my_url(), "kalender-acara"))
	{	
		query_posts( $query_string . $additional_string . '&orderby=meta_value&meta_key=tanggal_mulai&order=ASC');
	}
	else if (substr_count(get_my_url(), "/dokumen-gereja/"))
	{
		global $wpdb;
		$tdok_a = explode("/dokumen-gereja/", get_my_url());
		$tdok_a2 = explode("/", $tdok_a[1]);
		$tdok = $tdok_a2[0];
		$id = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\" AND post_name = \"$tdok\"");
		$postid = -1;
		foreach ($id as $idi)
		{
			$postid = $idi->ID;
		}
		$args = array(
    	  'meta_key' => 'tipe_dokumen',
    	  'meta_compare' => '=',
    	  'meta_value' => $postid,
    	  'post_type' => 'dokumen-gereja',
    	  'paged' => get_query_var('paged'),
    	  'posts_per_page' => 10,
    	  'orderby' => 'title',
    	  'order' => 'ASC'
		);
		query_posts( $args );
		//query_posts( $query_string . $additional_string);
	}
	else if (isset($_GET['kode_dokumen_gereja']) AND $_GET['kode_dokumen_gereja'] != '')
	{
		if ((!isset($_GET['nomor_pasal']) OR $_GET['nomor_pasal'] == '') AND (!isset($_GET['nomor_pasal_awal']) OR $_GET['nomor_pasal_awal'] == '' OR !isset($_GET['nomor_pasal_akhir']) OR $_GET['nomor_pasal_akhir'] == ''))
		{
			$args = array(
    	  	'meta_key' => 'tipe_dokumen',
    	  	'meta_compare' => '=',
    	  	'meta_value' => $_GET['kode_dokumen_gereja'],
    	  	's' => $_GET['s'],
    	  	'post_type' => 'dokumen-gereja',
    	  	'paged' => get_query_var('paged'),
    	  	'posts_per_page' => 10,
    	  	'orderby' => 'title',
    	  	'order' => 'ASC'
			);
			query_posts( $args );
		}
		else if (isset($_GET['nomor_pasal']) AND $_GET['nomor_pasal'] != '')
		{
			$args = array(
    	  	'meta_query' => array(
				array(
					'key' => 'tipe_dokumen',
					'value' => $_GET['kode_dokumen_gereja'],
					'compare' => '='
				),
				array(
					'key' => 'nomor_pasal',
					'value' => $_GET['nomor_pasal'],
					'compare' => '='
				)
			),
    	  	'post_type' => 'dokumen-gereja',
    	  	'paged' => get_query_var('paged'),
    	  	'posts_per_page' => 10,
    	  	'orderby' => 'title',
    	  	'order' => 'ASC'
			);
			query_posts( $args );
		}
		else if (isset($_GET['nomor_pasal_awal']) AND $_GET['nomor_pasal_awal'] != '' AND isset($_GET['nomor_pasal_akhir']) AND $_GET['nomor_pasal_akhir'] != '')
		{
			$args = array(
    	  	'meta_query' => array(
				array(
					'key' => 'tipe_dokumen',
					'value' => $_GET['kode_dokumen_gereja'],
					'compare' => '='
				),
				array(
					'key' => 'nomor_pasal',
					'value' => array($_GET['nomor_pasal_awal'], $_GET['nomor_pasal_akhir']),
					'compare' => 'BETWEEN'
				)
			),
    	  	'post_type' => 'dokumen-gereja',
    	  	'paged' => get_query_var('paged'),
    	  	'posts_per_page' => 10,
    	  	'orderby' => 'title',
    	  	'order' => 'ASC'
			);
			query_posts( $args );
		}
	}	
	else
	{
		query_posts( $query_string . $additional_string);
	}
	
?>

<div class = "container main-content">
	<div class = "prepend-small">&nbsp;</div>
	<div class = "span-17 span-17-680 main-left">
			<?php if ($chek OR $search_error OR $category_error OR $attachment_page) :?>
				<?php
					if (substr_count(get_my_url(), "kalender-acara") == 0 AND substr_count(get_my_url(), "dokumen-gereja") == 0)
					{
				?>
				<div class = "span-17 panel-daftar-tags">
				<h2 class = "panel-title">Daftar Semua Kata Kunci:</h2>
				<?php 
					$args = array
					(
    					'smallest'                  => 10, 
    					'largest'                   => 10,
    					'unit'                      => 'pt', 
    					'number'                    => 45,  
    					'format'                    => 'flat',
    					'separator'                 => " &nbsp;",
    					'orderby'                   => 'name', 
    					'order'                     => 'ASC',
    					'exclude'                   => null, 
    					'include'                   => null, 
    					'topic_count_text_callback' => default_topic_count_text,
    					'link'                      => 'view', 
    					'taxonomy'                  => 'post_tag', 
    					'echo'                      => true );
					wp_tag_cloud( $args );
				?>
				</div>
				<div class = "prepend-top-negative">&nbsp;</div>
				<?php
					}
					else if (substr_count(get_my_url(), "kalender-acara"))
					{
				?>
					<div class = "span-17 panel-daftar-tags">
						<h2 class = "panel-title">Daftar Semua Tempat Acara:</h2>
						<?php
							global $wpdb;
							$entri_acara = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"kalender-acara\" AND post_status = \"publish\"");
		 					$arsip = array();
		 					foreach ($entri_acara as $ea)
		 					{
		 						$tempat = get_post_meta($ea->ID, 'tempat', true);
		 						if(!isset($arsip[$tempat]) OR $arsip[$tempat] != 1)
		 						{
		 							$arsip[$tempat] = 1;
		 						}
		 					}
		 					$key_array = array();
		 					foreach ($arsip as $key=>$value)
		 					{
		 						$key_array[] = $key;
		 					}
		 					sort($key_array);
		 					//print_r ($key_array);
		 					$itung = 0;
		 					foreach ($key_array as $key_inst)
		 					{
						?>
								<a href = "<?php echo home_url('/kalender-acara/tempat/') . urlencode($key_inst); ?>" style = "font-size: 10px;"><?php echo $key_inst; ?></a>
						<?php
								$itung = $itung + 1;
								if ($itung == 30)
								{
									$itung = 0;
									break;
								}
		 					}
						?>
					</div>
					<div class = "prepend-top-negative">&nbsp;</div>
				<?php
					}
				?>
			<div>
				<h2 class = "coltitle art-title">
			<?php endif; ?>
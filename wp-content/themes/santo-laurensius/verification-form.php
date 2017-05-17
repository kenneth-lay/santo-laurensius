<?php
/**
 * Template Name: Verification Form
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
if (is_user_logged_in() AND !$okei AND !current_user_can('edit_published_pages') AND !get_user_meta($current_user->ID, 'katolik', true))
{
	get_header(); 
	global $post;
?>

<script type = "text/javascript">
	$(document).ready(function(){
		if ($("#show_kk1").attr("checked") == "checked" || $("#show_kk2").attr("checked") == "checked" || $("#show_kk3").attr("checked") == "checked" || $("#show_kk4").attr("checked") == "checked")
    	{
    		$("#nama_kk").show();
    	}
    	if ($("#umat_ya").attr("checked") == "checked")
    	{
    		$("#nomor_kk").show();
    		$("#nomor_surat_baptis").css('border-left', '7px orange solid');
    	}
    	else
    	{
    		$("#nomor_kk").hide();
    		$("#nomor_surat_baptis").css('border-left', '7px #CC3300 solid');
    	}
  		$("#umat_ya").click(function(){
    		$("#nomor_surat_baptis").show();
    		$("#nomor_surat_baptis").css('border-left', '7px orange solid');
    		$("#nomor_kk").show();
    		if ($("#show_kk1").attr("checked") == "checked" || $("#show_kk2").attr("checked") == "checked" || $("#show_kk3").attr("checked") == "checked" || $("#show_kk4").attr("checked") == "checked")
    		{
    			$("#nama_kk").show();
    		}
    		else
    		{
    			$("#nama_kk").hide();
    			$("#nama_kk_text").val('');
    		}
  		});
  		$("#umat_bukan").click(function(){
    		$("#nomor_surat_baptis").show();
    		$("#nomor_surat_baptis").css('border-left', '7px #CC3300 solid');
    		$("#nomor_kk").hide();
    		if ($("#show_kk1").attr("checked") == "checked" || $("#show_kk2").attr("checked") == "checked" || $("#show_kk3").attr("checked") == "checked" || $("#show_kk4").attr("checked") == "checked")
    		{
    			$("#nama_kk").show();
    		}
    		else
    		{
    			$("#nama_kk").hide();
    			$("#nama_kk_text").val('');
    		}
  		});
  		$("#show_kk1").click(function(){
    		$("#nama_kk").show();
  		});
  		$("#show_kk2").click(function(){
    		$("#nama_kk").show();
  		});
  		$("#show_kk3").click(function(){
    		$("#nama_kk").show();
  		});
  		$("#show_kk4").click(function(){
    		$("#nama_kk").show();
  		});
  		$("#hide_kk").click(function(){
    		$("#nama_kk").hide();
    		$("#nama_kk_text").val('');
  		});
	});
</script>

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
				if (isset($_SESSION['berhasil']) AND $_SESSION['berhasil'] != '')
				{
			?>
			<div class = "success-message span-17">
				<?php echo $_SESSION['berhasil']; ?>
			</div>
			<div class = "prepend-top-negative">&nbsp;</div>	
			<?php
					unset($_SESSION['berhasil']);
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
			
			<div class = "registration-form single-column">			
				<div style = "font-size: 13px;">
					<?php the_content(); ?>
				</div>
				Bagian dengan&nbsp; <div class = "sample-bordir">bordir merah</div> di sebelah kiri kotak pengisian menandakan bahwa kotak tersebut wajib diisi/dijawab oleh setiap calon anggota.
				Bagian dengan&nbsp; <div class = "sample-bordir-2">bordir jingga</div> di sebelah kiri kotak pengisian menandakan bahwa anda boleh memilih salah satu kotak yang akan diisi.
				<div class = "prepend-top-negative">&nbsp;</div>
				<?php
					require_once(ABSPATH . WPINC . '/registration.php'); 
					global $current_user;
    				get_currentuserinfo();
					$nama_depan = $current_user->first_name;
					$nama_belakang = $current_user->last_name;
				?>
				<?php
					if (isset($_SESSION['previous_nama_depan']) AND $_SESSION['previous_nama_depan'] != '')
					{
						$previous_nama_depan = $_SESSION['previous_nama_depan'];
						unset($_SESSION['previous_nama_depan']);
					}
					else
					{
						$previous_nama_depan = $nama_depan;
					}
					if (isset($_SESSION['previous_nama_belakang']) AND $_SESSION['previous_nama_belakang'] != '')
					{
						$previous_nama_belakang = $_SESSION['previous_nama_belakang'];
						unset($_SESSION['previous_nama_belakang']);
					}
					else
					{
						$previous_nama_belakang = $nama_belakang;
					}
					$cek1 = '';
					$cek2 = '';
					if (isset($_SESSION['previous_jenis_kelamin']) AND $_SESSION['previous_jenis_kelamin'] != '')
					{
						$previous_jenis_kelamin = $_SESSION['previous_jenis_kelamin'];
						unset($_SESSION['previous_jenis_kelamin']);
						if ($previous_jenis_kelamin == 'pria')
						{
							$cek1 = "checked = \"checked\"";
						}
						else
						{
							$cek2 = "checked = \"checked\"";
						}
					}
					if (isset($_SESSION['previous_tanggal_lahir']) AND $_SESSION['previous_tanggal_lahir'] != '')
					{
						$previous_tanggal_lahir = $_SESSION['previous_tanggal_lahir'];
						unset($_SESSION['previous_tanggal_lahir']);
					}
					$cek3 = '';
					$cek4 = '';
					$cek5 = '';
					$cek6 = '';
					$cek7 = '';
					$cek8 = '';
					$cek9 = '';
					if (isset($_SESSION['previous_sakramen_yang_pernah_diterima']) AND $_SESSION['previous_sakramen_yang_pernah_diterima'] != '')
					{
						$previous_sakramen_yang_pernah_diterima = $_SESSION['previous_sakramen_yang_pernah_diterima'];
						unset($_SESSION['previous_sakramen_yang_pernah_diterima']);
						if (in_array('baptis', $previous_sakramen_yang_pernah_diterima))
						{
							$cek3 = "checked = \"checked\"";
						}
						if (in_array('ekaristi', $previous_sakramen_yang_pernah_diterima))
						{
							$cek4 = "checked = \"checked\"";
						}
						if (in_array('krisma', $previous_sakramen_yang_pernah_diterima))
						{
							$cek5 = "checked = \"checked\"";
						}
						if (in_array('tobat', $previous_sakramen_yang_pernah_diterima))
						{
							$cek6 = "checked = \"checked\"";
						}
						if (in_array('imamat', $previous_sakramen_yang_pernah_diterima))
						{
							$cek7 = "checked = \"checked\"";
						}
						if (in_array('perkawinan', $previous_sakramen_yang_pernah_diterima))
						{
							$cek8 = "checked = \"checked\"";
						}
						if (in_array('pengurapan', $previous_sakramen_yang_pernah_diterima))
						{
							$cek9 = "checked = \"checked\"";
						}
					}
					$cek10 = '';
					$cek11 = '';
					if (isset($_SESSION['previous_umat_santo_laurensius']) AND $_SESSION['previous_umat_santo_laurensius'] != '')
					{
						$previous_umat_santo_laurensius = $_SESSION['previous_umat_santo_laurensius'];
						unset($_SESSION['previous_umat_santo_laurensius']);
						if ($previous_umat_santo_laurensius == 'ya')
						{
							$cek10 = "checked = \"checked\"";
						}
						else
						{
							$cek11 = "checked = \"checked\"";
						}
					}
					if (isset($_SESSION['previous_alamat_lengkap']) AND $_SESSION['previous_alamat_lengkap'] != '')
					{
						$previous_alamat_lengkap = $_SESSION['previous_alamat_lengkap'];
						unset($_SESSION['previous_alamat_lengkap']);
					}
					if (isset($_SESSION['previous_lingkungan']) AND $_SESSION['previous_lingkungan'] != '')
					{
						$previous_lingkungan = $_SESSION['previous_lingkungan'];
						unset($_SESSION['previous_lingkungan']);
					}
					if (isset($_SESSION['previous_wilayah']) AND $_SESSION['previous_wilayah'] != '')
					{
						$previous_wilayah = $_SESSION['previous_wilayah'];
						unset($_SESSION['previous_wilayah']);
					}
					if (isset($_SESSION['previous_kota_kelahiran']) AND $_SESSION['previous_kota_kelahiran'] != '')
					{
						$previous_kota_kelahiran = $_SESSION['previous_kota_kelahiran'];
						unset($_SESSION['previous_kota_kelahiran']);
					}
					if (isset($_SESSION['previous_negara_kelahiran']) AND $_SESSION['previous_negara_kelahiran'] != '')
					{
						$previous_negara_kelahiran = $_SESSION['previous_negara_kelahiran'];
						unset($_SESSION['previous_negara_kelahiran']);
					}
					if (isset($_SESSION['previous_nomor_telepon']) AND $_SESSION['previous_nomor_telepon'] != '')
					{
						$previous_nomor_telepon = $_SESSION['previous_nomor_telepon'];
						unset($_SESSION['previous_nomor_telepon']);
					}
					$cek12 = '';
					$cek13 = '';
					$cek14 = '';
					$cek15 = '';
					$cek16 = '';
					if (isset($_SESSION['previous_posisi_dalam_keluarga']) AND $_SESSION['previous_posisi_dalam_keluarga'] != '')
					{
						$previous_posisi_dalam_keluarga = $_SESSION['previous_posisi_dalam_keluarga'];
						//echo $previous_posisi_dalam_keluarga;
						unset($_SESSION['previous_posisi_dalam_keluarga']);
						if ($previous_posisi_dalam_keluarga == 'anak')
						{
							$cek12 = "checked = \"checked\"";
						}
						else if ($previous_posisi_dalam_keluarga == 'kk')
						{
							$cek13 = "checked = \"checked\"";
						}
						else if ($previous_posisi_dalam_keluarga == 'istri')
						{
							$cek14 = "checked = \"checked\"";
						}
						else if ($previous_posisi_dalam_keluarga == 'saudara')
						{
							$cek15 = "checked = \"checked\"";
						}
						else
						{
							$cek16 = "checked = \"checked\"";
						}
					}
					if (isset($_SESSION['previous_nama_kepala_keluarga']) AND $_SESSION['previous_nama_kepala_keluarga'] != '')
					{
						$previous_nama_kepala_keluarga = $_SESSION['previous_nama_kepala_keluarga'];
						unset($_SESSION['previous_nama_kepala_keluarga']);
					}
					if (isset($_SESSION['previous_nomor_surat_baptis']) AND $_SESSION['previous_nomor_surat_baptis'] != '')
					{
						$previous_nomor_surat_baptis = $_SESSION['previous_nomor_surat_baptis'];
						unset($_SESSION['previous_nomor_surat_baptis']);
					}
					if (isset($_SESSION['previous_nomor_kartu_keluarga_katolik']) AND $_SESSION['previous_nomor_kartu_keluarga_katolik'] != '')
					{
						$previous_nomor_kartu_keluarga_katolik = $_SESSION['previous_nomor_kartu_keluarga_katolik'];
						unset($_SESSION['previous_nomor_kartu_keluarga_katolik']);
					}
					$cek17 = '';
					if (isset($_SESSION['previous_setuju']) AND $_SESSION['previous_setuju'] != '')
					{
						$previous_setuju = $_SESSION['previous_setuju'];
						unset($_SESSION['previous_setuju']);
						if ($previous_setuju == 'setuju')
						{
							$cek17 = "checked = checked";
						}
					}
				?>
				<form action="<?php echo site_url() . '/kirim-verifikasi/' ?>" method="post">
					<div class = "registration-form-field">
						<label>Nama Depan</label>
						<input type="text" name = "nama_depan" value = "<?php echo $previous_nama_depan; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nama_depan_empty']) AND $_SESSION['nama_depan_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_depan_empty'] . '<br />';
								unset($_SESSION['nama_depan_empty']);
							}
							if (isset($_SESSION['nama_depan_format']) AND $_SESSION['nama_depan_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_depan_format'] . '<br />';
								unset($_SESSION['nama_depan_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field not-required-field">
						<label>Nama Belakang</label>
						<input type="text" name = "nama_belakang" value = "<?php echo $previous_nama_belakang; ?>" /><br />
						<span class = "error-msg">
							<?php
								if (isset($_SESSION['nama_belakang_format']) AND $_SESSION['nama_belakang_format'] != '')
								{
									echo '&rarr; ' . $_SESSION['nama_belakang_format'] . '<br />';
									unset($_SESSION['nama_belakang_format']);
								}
							?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Jenis Kelamin</label>
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<input type="radio" name="jenis_kelamin" value="pria" <?php echo $cek1; ?> /> <span>Pria</span><br />
						<input type="radio" name="jenis_kelamin" value="wanita" <?php echo $cek2; ?> /> <span>Wanita</span><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['jenis_kelamin_empty']) AND $_SESSION['jenis_kelamin_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['jenis_kelamin_empty'] . '<br />';
								unset($_SESSION['jenis_kelamin_empty']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Tanggal Lahir</label>
						Tanggal lahir harap ditulis dengan format: <strong>tttt-bb-hh</strong> (Contoh: 1945-08-17 = 17 Agustus 1945).
						<input type="text" name = "tanggal_lahir" value = "<?php echo $previous_tanggal_lahir; ?>" /><br />
							<span class = "error-msg">
							<?php
								if (isset($_SESSION['tanggal_lahir_empty']) AND $_SESSION['tanggal_lahir_empty'] != '')
								{
									echo '&rarr; ' . $_SESSION['tanggal_lahir_empty'] . '<br />';
									unset($_SESSION['tanggal_lahir_empty']);
								}
								if (isset($_SESSION['tanggal_lahir_format']) AND $_SESSION['tanggal_lahir_format'] != '')
								{
									echo '&rarr; ' . $_SESSION['tanggal_lahir_format'] . '<br />';
									unset($_SESSION['tanggal_lahir_format']);
								}
							?>
							</span>
					</div>
					<div class = "registration-form-field not-required-field">
						<label>Sakramen(-sakramen) yang Pernah Diterima</label>
						Jika anda pernah menerima (setidaknya sekali untuk sakramen yang dapat diterima lebih dari satu kali) sakramen-sakramen di bawah ini,
						 silahkan memberi tanda centang pada setiap sakramen yang pernah anda terima.
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="baptis" <?php echo $cek3; ?> /> <span>Baptis</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="ekaristi" <?php echo $cek4; ?> /> <span>Ekaristi</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="krisma" <?php echo $cek5; ?> /> <span>Krisma</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="tobat" <?php echo $cek6; ?> /> <span>Tobat</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="imamat" <?php echo $cek7; ?> /> <span>Imamat</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="perkawinan" <?php echo $cek8; ?> /> <span>Perkawinan</span><br />
						<input type="checkbox" name="sakramen_yang_pernah_diterima[]" value="pengurapan" <?php echo $cek9; ?> /> <span>Pengurapan Orang Sakit</span><br />
					</div>
					<div class = "registration-form-field">
						<label>Umat Santo Laurensius?</label>
						Apakah anda termasuk salah satu umat Paroki Santo Laurensius?
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<input id = "umat_ya" type="radio" name="umat_santo_laurensius" value="ya" <?php echo $cek10; ?> /> <span>Ya</span><br />
						<input id = "umat_bukan" type="radio" name="umat_santo_laurensius" value="bukan" <?php echo $cek11; ?> /> <span>Bukan</span><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['umat_santo_laurensius_empty']) AND $_SESSION['umat_santo_laurensius_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['umat_santo_laurensius_empty'] . '<br />';
								unset($_SESSION['umat_santo_laurensius_empty']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Alamat Lengkap</label>
						<textarea name = "alamat_lengkap" style = "width: 100%; height: 100px;"><?php echo $previous_alamat_lengkap; ?></textarea><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['alamat_lengkap_empty']) AND $_SESSION['alamat_lengkap_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['alamat_lengkap_empty'] . '<br />';
								unset($_SESSION['alamat_lengkap_empty']);
							}
							if (isset($_SESSION['alamat_lengkap_format']) AND $_SESSION['alamat_lengkap_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['alamat_lengkap_format'] . '<br />';
								unset($_SESSION['alamat_lengkap_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field not-required-field">
						<label>Nama Lingkungan</label>
						<input type="text" name = "lingkungan" value = "<?php echo $previous_lingkungan; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['lingkungan_format']) AND $_SESSION['lingkungan_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['lingkungan_format'] . '<br />';
								unset($_SESSION['lingkungan_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field not-required-field">
						<label>Nama Wilayah</label>
						<input type="text" name = "wilayah" value = "<?php echo $previous_wilayah; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['wilayah_format']) AND $_SESSION['wilayah_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['wilayah_format'] . '<br />';
								unset($_SESSION['wilayah_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Kota Kelahiran</label>
						<input type="text" name = "kota_kelahiran" value = "<?php echo $previous_kota_kelahiran; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['kota_kelahiran_empty']) AND $_SESSION['kota_kelahiran_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['kota_kelahiran_empty'] . '<br />';
								unset($_SESSION['kota_kelahiran_empty']);
							}
							if (isset($_SESSION['kota_kelahiran_format']) AND $_SESSION['kota_kelahiran_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['kota_kelahiran_format'] . '<br />';
								unset($_SESSION['kota_kelahiran_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Negara Kelahiran</label>
						<input type="text" name = "negara_kelahiran" value = "<?php echo $previous_negara_kelahiran; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['negara_kelahiran_empty']) AND $_SESSION['negara_kelahiran_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['negara_kelahiran_empty'] . '<br />';
								unset($_SESSION['negara_kelahiran_empty']);
							}
							if (isset($_SESSION['negara_kelahiran_format']) AND $_SESSION['negara_kelahiran_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['negara_kelahiran_format'] . '<br />';
								unset($_SESSION['negara_kelahiran_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Nomor Telepon</label>
						Anda boleh menggunakan kode wilayah dan kode area. Separator kode dan nomor adalah tanda <strong>strip</strong> / <em>dash</em> [ - ].
						Anda <strong>diperbolehkan</strong> menggunakan <strong>tanda kurung</strong>, namun <strong>tidak</strong> diperkenankan menggunakan spasi.  
						<input type="text" name = "nomor_telepon" value = "<?php echo $previous_nomor_telepon; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nomor_telepon_empty']) AND $_SESSION['nomor_telepon_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['nomor_telepon_empty'] . '<br />';
								unset($_SESSION['nomor_telepon_empty']);
							}
							if (isset($_SESSION['nomor_telepon_format']) AND $_SESSION['nomor_telepon_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nomor_telepon_format'] . '<br />';
								unset($_SESSION['nomor_telepon_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Posisi Dalam Keluarga</label>
						"Posisi" yang dimaksud adalah status anda dalam keluarga anda saat ini (dalam lingkup yang paling kecil).
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<input id = "show_kk1" type="radio" name="posisi_dalam_keluarga" value="anak" <?php echo $cek12; ?> /> <span>Anak</span><br />
						<input id = "hide_kk" type="radio" name="posisi_dalam_keluarga" value="kk" <?php echo $cek13; ?> /> <span>Kepala Keluarga</span><br />
						<input id = "show_kk2" type="radio" name="posisi_dalam_keluarga" value="istri" <?php echo $cek14; ?> /> <span>Istri</span><br />
						<input id = "show_kk3" type="radio" name="posisi_dalam_keluarga" value="saudara" <?php echo $cek15; ?> /> <span>Saudara</span><br />
						<input id = "show_kk4" type="radio" name="posisi_dalam_keluarga" value="lainlain" <?php echo $cek16; ?> /> <span>Lain-lain</span><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['posisi_dalam_keluarga_empty']) AND $_SESSION['posisi_dalam_keluarga_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['posisi_dalam_keluarga_empty'] . '<br />';
								unset($_SESSION['posisi_dalam_keluarga_empty']);
							}
						?>
						</span>
					</div>
					<div id = "nama_kk" class = "registration-form-field" style = "display: none;">
						<label>Nama Kepala Keluarga</label>
						<input id = "nama_kk_text" type="text" name = "nama_kepala_keluarga" value = "<?php echo $previous_nama_kepala_keluarga; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nama_kepala_keluarga_empty']) AND $_SESSION['nama_kepala_keluarga_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_kepala_keluarga_empty'] . '<br />';
								unset($_SESSION['nama_kepala_keluarga_empty']);
							}
							if (isset($_SESSION['nama_kepala_keluarga_format']) AND $_SESSION['nama_kepala_keluarga_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_kepala_keluarga_format'] . '<br />';
								unset($_SESSION['nama_kepala_keluarga_format']);
							}
						?>
						</span>
					</div>
					<div id = "nomor_surat_baptis" class = "registration-form-field">
						<label>Nomor Surat Baptis</label>
						<input type="text" name = "nomor_surat_baptis" value = "<?php echo $previous_nomor_surat_baptis; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nomor_surat_baptis_empty']) AND $_SESSION['nomor_surat_baptis_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['nomor_surat_baptis_empty'] . '<br />';
								unset($_SESSION['nomor_surat_baptis_empty']);
							}
							if (isset($_SESSION['nomor_surat_baptis_format']) AND $_SESSION['nomor_surat_baptis_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nomor_surat_baptis_format'] . '<br />';
								unset($_SESSION['nomor_surat_baptis_format']);
							}
						?>
						</span>
					</div>
					<div id = "nomor_kk" class = "optional-field">
						<label>Nomor Kartu Keluarga Katolik</label>
						Khusus bagi umat Paroki Santo Laurensius yang sudah terdaftar sebelumnya.
						<input type="text" name = "nomor_kartu_keluarga_katolik" value = "<?php echo $previous_nomor_kartu_keluarga_katolik; ?>" /><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nomor_kartu_keluarga_katolik_empty']) AND $_SESSION['nomor_kartu_keluarga_katolik_empty'] != '')
							{
								echo $_SESSION['nomor_kartu_keluarga_katolik_empty'] . '<br />';
								unset($_SESSION['nomor_kartu_keluarga_katolik_empty']);
							}
							if (isset($_SESSION['nomor_kartu_keluarga_katolik_format']) AND $_SESSION['nomor_kartu_keluarga_katolik_format'] != '')
							{
								echo $_SESSION['nomor_kartu_keluarga_katolik_format'] . '<br />';
								unset($_SESSION['nomor_kartu_keluarga_katolik_format']);
							}
						?>
						</span>
					</div>
					<div class = "prepend-top-negative">&nbsp;</div>
					<div id = "syarat-ketentuan" class = "fill-cream span-17" style = "display: block;">
						<h4>Syarat dan Ketentuan</h4>
						<p>
							Saya menyatakan bahwa semua data yang saya berikan di atas adalah benar adanya. Saya bersedia
							di-blokir dari keanggotaan situs web Paroki Santo Laurensius apabila saya terbukti dengan sengaja memasukkan 
							data yang salah. Administrator situs sepenuhnya berhak, tanpa pemberitahuan terlebih dahulu, untuk menghapus 
							data verifikasi yang telah saya buat, meminta verifikasi ulang akun saya, menghapus akun, atau memblokir akun milik saya.
						</p>
						<div id = "amin">
							<input type="checkbox" name="setuju" value="setuju" <?php echo $cek17; ?> /> Setuju<br />
						</div>
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['setuju_empty']) AND $_SESSION['setuju_empty'] != '')
							{
								echo $_SESSION['setuju_empty'] . '<br />';
								unset($_SESSION['setuju_empty']);
							}
						?>
						</span>
					</div>
					<div class = "prepend-top-negative">&nbsp;</div>
					<div id = "vfiller" class = "span-17" style = "display: none;">&nbsp;</div>
					<div class = "registration">
						<input type="submit" id="submitbtn" name="submit" value="Verifikasi" />
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
		$okei = FALSE;
		wp_redirect(site_url());
		exit();
	}
?>
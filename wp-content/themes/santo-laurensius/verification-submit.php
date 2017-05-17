<?php
/**
 * Template Name: Verification Submit
 */
 	session_start();
	$got_error = FALSE;
	if($_POST)
	{
			global $current_user, $wpdb;
      		get_currentuserinfo();
  			
  			$nama_depan = $wpdb->escape($_POST['nama_depan']);
			if(empty($nama_depan)) 
			{
				$got_error = TRUE;
				$_SESSION['nama_depan_empty'] = "Nama depan tidak boleh dikosongkan.";
			}
			else if (!preg_match("/^[a-zA-Z ]{3,32}$/", $nama_depan))
			{
				$got_error = TRUE;
				$_SESSION['nama_depan_format'] = "Nama depan tidak boleh melebihi 32 karakter dan minimal 3 karakter.";
			}
			$nama_belakang = $wpdb->escape($_POST['nama_belakang']);
			if(!empty($nama_belakang) AND !preg_match("/^[a-zA-Z ]{3,32}$/", $nama_belakang)) 
			{
				$got_error = TRUE;
				$_SESSION['nama_belakang_format'] = "Jika Anda ingin mengisi nama belakang, entri Anda tidak boleh melebihi 32 karakter, serta minimal panjang entri 3 karakter.";
			}
			$jenis_kelamin = $_POST['jenis_kelamin'];
			if(empty($jenis_kelamin)) 
			{
				$got_error = TRUE;
				$_SESSION['jenis_kelamin_empty'] = "Jenis kelamin tidak boleh dikosongkan.";
			}
			$tanggal_lahir = $_POST['tanggal_lahir'];
			if(empty($tanggal_lahir)) 
			{
				$got_error = TRUE;
				$_SESSION['tanggal_lahir_empty'] = "Tanggal lahir tidak boleh dikosongkan.";
			}
			else if (!preg_match("/^([1][9][0-9][0-9]|[2][0][0][0-9])-([0][1-9]|[1][0-2])-([0-2][1-9]|[3][0-1])$/", $tanggal_lahir))
			{
				$got_error = TRUE;
				$_SESSION['tanggal_lahir_format'] = "Tanggal lahir harus ditulis dengan format seperti contoh diatas.";
			}
			$umat_santo_laurensius = $_POST['umat_santo_laurensius'];
			if(empty($umat_santo_laurensius)) 
			{
				$got_error = TRUE;
				$_SESSION['umat_santo_laurensius_empty'] = "Status umat tidak boleh dikosongkan.";
			}
			$alamat_lengkap = $wpdb->escape($_POST['alamat_lengkap']);
			if(empty($alamat_lengkap)) 
			{
				$got_error = TRUE;
				$_SESSION['alamat_lengkap_empty'] = "Alamat harus diisi lengkap.";
			}
			else if (!preg_match("/^[-\/._0-9a-zA-Z ]{16,256}$/", $alamat_lengkap))
			{
				$got_error = TRUE;
				$_SESSION['alamat_lengkap_format'] = "Alamat yang anda masukkan tidak valid.";
			}
			$lingkungan = $wpdb->escape($_POST['lingkungan']);
			if(!empty($lingkungan) AND !preg_match("/^[a-zA-Z ]{6,64}$/", $lingkungan)) 
			{
				$got_error = TRUE;
				$_SESSION['lingkungan_format'] = "Jika Anda ingin mengisi nama lingkungan, entri Anda tidak boleh melebihi 64 karakter, serta minimal panjang entri 6 karakter.";
			}
			$wilayah = $wpdb->escape($_POST['wilayah']);
			if(!empty($lingkungan) AND !preg_match("/^[a-zA-Z ]{1,32}$/", $wilayah)) 
			{
				$got_error = TRUE;
				$_SESSION['wilayah_format'] = "Jika Anda ingin mengisi nama wilayah, entri Anda tidak boleh melebihi 32 karakter.";
			}
			$kota_kelahiran = $wpdb->escape($_POST['kota_kelahiran']);
			if(empty($kota_kelahiran)) 
			{
				$got_error = TRUE;
				$_SESSION['kota_kelahiran_empty'] = "Nama kota kelahiran tidak boleh dikosongkan.";
			}
			else if (!preg_match("/^[a-zA-Z ]{3,64}$/", $kota_kelahiran))
			{
				$got_error = TRUE;
				$_SESSION['kota_kelahiran_format'] = "Nama depan tidak boleh melebihi 64 karakter dan minimal 3 karakter.";
			}
			$negara_kelahiran = $wpdb->escape($_POST['negara_kelahiran']);
			if(empty($negara_kelahiran)) 
			{
				$got_error = TRUE;
				$_SESSION['negara_kelahiran_empty'] = "Negara kelahiran tidak boleh dikosongkan.";
			}
			else if (!preg_match("/^[a-zA-Z ]{4,32}$/", $negara_kelahiran))
			{
				$got_error = TRUE;
				$_SESSION['negara_kelahiran_format'] = "Nama negara kelahiran tidak boleh melebihi 32 karakter dan minimal 4 karakter.";
			}
			$nomor_telepon = $wpdb->escape($_POST['nomor_telepon']);
			if(empty($nomor_telepon)) 
			{
				$got_error = TRUE;
				$_SESSION['nomor_telepon_empty'] = "Nomor telepon tidak boleh dikosongkan.";
			}
			else if (!preg_match("/^\(?([0-9]{0,3})\)?[-]?\(?([0-9]{0,3})\)?[-]?([0-9]{7,})$/", $nomor_telepon))
			{
				$got_error = TRUE;
				$_SESSION['nomor_telepon_format'] = "Format nomor telepon yang anda masukkan tidak valid.";
			}
			$setuju = $_POST['setuju'];
			$nomor_kkk = $_POST['nomor_kartu_keluarga_katolik'];
			$nomor_sb = $_POST['nomor_surat_baptis'];
			$nama_kk = $_POST['nama_kepala_keluarga'];
			$posisi_dalam_keluarga = $_POST['posisi_dalam_keluarga'];
			if(empty($posisi_dalam_keluarga)) 
			{
				$got_error = TRUE;
				$_SESSION['posisi_dalam_keluarga_empty'] = "Posisi Anda dalam keluarga tidak boleh dikosongkan.";
			}
			else if ($posisi_dalam_keluarga == 'anak' OR $posisi_dalam_keluarga == 'istri' OR $posisi_dalam_keluarga == 'saudara' OR $posisi_dalam_keluarga == 'lainlain' OR empty($posisi_dalam_keluarga))
			{
				if(!empty($umat_santo_laurensius) AND $umat_santo_laurensius == 'ya')
				{
					if (empty($nomor_kkk) AND empty($nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_empty'] = 'Nomor Kartu Keluarga Katolik atau Nomor Surat Baptis tidak boleh dikosongkan (isi salah satu).';
					}
					else if (empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_kkk))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_format'] = 'Nomor Kartu Keluarga Katolik tidak valid.';
					}
					else if (empty($nomor_kkk) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
					else if (!empty($nomor_kkk) AND !empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
					else if (!empty($nomor_kkk) AND !empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_kkk))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_format'] = 'Nomor Kartu Keluarga Katolik tidak valid.';
					}
					
					if(empty($nama_kk)) 
					{
						$got_error = TRUE;
						$_SESSION['nama_kepala_keluarga_empty'] = "Nama Kepala Keluarga tidak boleh dikosongkan.";
					}
					else if (!preg_match("/^[a-zA-Z ]{6,32}$/", $nama_kk))
					{
						$got_error = TRUE;
						$_SESSION['nama_kepala_keluarga_format'] = "Nama Kepala Keluarga tidak boleh melebihi 64 karakter dan minimal 6 karakter.";
					}
				}
				else if (!empty($umat_santo_laurensius) AND $umat_santo_laurensius == 'bukan' OR empty($umat_santo_laurensius))
				{
					if (empty($nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_empty'] = 'Nomor Surat Baptis tidak boleh dikosongkan.';
					}
					else if (!preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
					if(empty($nama_kk)) 
					{
						$got_error = TRUE;
						$_SESSION['nama_kepala_keluarga_empty'] = "Nama Kepala Keluarga tidak boleh dikosongkan.";
					}
					else if (!preg_match("/^[a-zA-Z ]{6,32}$/", $nama_kk))
					{
						$got_error = TRUE;
						$_SESSION['nama_kepala_keluarga_format'] = "Nama Kepala Keluarga tidak boleh melebihi 64 karakter dan minimal 6 karakter.";
					}
				}
			}
			else
			{
				if(!empty($umat_santo_laurensius) AND $umat_santo_laurensius == 'ya')
				{
					if (empty($nomor_kkk) AND empty($nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_empty'] = 'Nomor Kartu Keluarga Katolik atau Nomor Surat Baptis tidak boleh dikosongkan (isi salah satu).';
					}
					else if (empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_kkk))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_format'] = 'Nomor Kartu Keluarga Katolik tidak valid.';
					}
					else if (empty($nomor_kkk) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
					else if (!empty($nomor_kkk) AND !empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
					else if (!empty($nomor_kkk) AND !empty($nomor_sb) AND !preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_kkk))
					{
						$got_error = TRUE;
						$_SESSION['nomor_kartu_keluarga_katolik_format'] = 'Nomor Kartu Keluarga Katolik tidak valid.';
					}
				}
				else if (!empty($umat_santo_laurensius) AND $umat_santo_laurensius == 'bukan')
				{
					if (empty($nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_empty'] = 'Nomor Surat Baptis tidak boleh dikosongkan.';
					}
					else if (!preg_match("/^[-._0-9a-zA-Z ]{7,64}$/", $nomor_sb))
					{
						$got_error = TRUE;
						$_SESSION['nomor_surat_baptis_format'] = 'Nomor Surat Baptis tidak valid.';
					}
				}
			}
			if (empty($umat_santo_laurensius))
			{
				if (empty($nomor_sb))
				{
					$got_error = TRUE;
					$_SESSION['nomor_surat_baptis_empty'] = 'Nomor Surat Baptis tidak boleh dikosongkan.';
				}
			}
			if (!$got_error)
			{
				if (empty($setuju))
				{
					$_SESSION['setuju_empty'] = 'Anda harus menyetujui syarat dan ketentuan yang berlaku.';
					$_SESSION['previous_nama_depan'] = $_POST['nama_depan'];
					$_SESSION['previous_nama_belakang'] = $_POST['nama_belakang'];
					$_SESSION['previous_jenis_kelamin'] = $_POST['jenis_kelamin'];
					$_SESSION['previous_tanggal_lahir'] = $_POST['tanggal_lahir'];
					$_SESSION['previous_sakramen_yang_pernah_diterima'] = $_POST['sakramen_yang_pernah_diterima'];
					$_SESSION['previous_umat_santo_laurensius'] = $_POST['umat_santo_laurensius'];
					$_SESSION['previous_alamat_lengkap'] = $_POST['alamat_lengkap'];
					$_SESSION['previous_lingkungan'] = $_POST['lingkungan'];
					$_SESSION['previous_wilayah'] = $_POST['wilayah'];
					$_SESSION['previous_kota_kelahiran'] = $_POST['kota_kelahiran'];
					$_SESSION['previous_negara_kelahiran'] = $_POST['negara_kelahiran'];
					$_SESSION['previous_nomor_telepon'] = $_POST['nomor_telepon'];
					$_SESSION['previous_posisi_dalam_keluarga'] = $_POST['posisi_dalam_keluarga'];
					$_SESSION['previous_nama_kepala_keluarga'] = $_POST['nama_kepala_keluarga'];
					$_SESSION['previous_nomor_surat_baptis'] = $_POST['nomor_surat_baptis'];
					$_SESSION['previous_nomor_kartu_keluarga_katolik'] = $_POST['nomor_kartu_keluarga_katolik'];
					wp_redirect(site_url() . '/data-umat/');
					exit();
				}
				$nama_lengkap = '';
  				if ($wpdb->escape($_POST['nama_belakang']) == '')
  				{
  					$nama_lengkap = $wpdb->escape($_POST['nama_depan']);
  				}
  				else
  				{
  					$nama_lengkap = $wpdb->escape($_POST['nama_depan']) . ' ' . $wpdb->escape($_POST['nama_belakang']);
  				}
  				$my_post = array(
     				'post_title' => $nama_lengkap,
     				'post_status' => 'publish',
     				'post_author' => $current_user->ID,
     				'post_type' => 'data-umat'
  				);
  				$new_postid = wp_insert_post( $my_post );
  				update_post_meta($new_postid, 'nama_lengkap', $nama_lengkap);
  				update_post_meta($new_postid, 'jenis_kelamin', $wpdb->escape($_POST['jenis_kelamin']));
  				update_post_meta($new_postid, 'tanggal_lahir', $wpdb->escape($_POST['tanggal_lahir']));
  				update_post_meta($new_postid, 'sakramen_yang_pernah_diterima', $wpdb->escape($_POST['sakramen_yang_pernah_diterima']));
  				update_post_meta($new_postid, 'umat_santo_laurensius', $wpdb->escape($_POST['umat_santo_laurensius']));
  				update_post_meta($new_postid, 'alamat_lengkap', $wpdb->escape($_POST['alamat_lengkap']));
  				update_post_meta($new_postid, 'lingkungan', $wpdb->escape($_POST['lingkungan']));
  				update_post_meta($new_postid, 'wilayah', $wpdb->escape($_POST['wilayah']));
  				update_post_meta($new_postid, 'kota_kelahiran', $wpdb->escape($_POST['kota_kelahiran']));
  				update_post_meta($new_postid, 'negara_kelahiran', $wpdb->escape($_POST['negara_kelahiran']));
  				update_post_meta($new_postid, 'nomor_telepon', $wpdb->escape($_POST['nomor_telepon']));
  				update_post_meta($new_postid, 'posisi_dalam_keluarga', $wpdb->escape($_POST['posisi_dalam_keluarga']));
  				update_post_meta($new_postid, 'nama_kepala_keluarga', $wpdb->escape($_POST['nama_kepala_keluarga']));
  				update_post_meta($new_postid, 'nomor_surat_baptis', $wpdb->escape($_POST['nomor_surat_baptis']));
  				update_post_meta($new_postid, 'nomor_kartu_keluarga_katolik', $wpdb->escape($_POST['nomor_kartu_keluarga_katolik']));
				update_user_meta($current_user->ID, 'first_name', $wpdb->escape($_POST['nama_depan']));
				update_user_meta($current_user->ID, 'last_name', $wpdb->escape($_POST['nama_belakang']));
				
				$_SESSION['berhasil'] = 'Terima kasih atas waktu dan kesediaan Anda melengkapi data umat kami.';
				wp_redirect(site_url());
				exit();
			}
			else
			{
				$got_error = FALSE;
				$_SESSION['previous_nama_depan'] = $_POST['nama_depan'];
				$_SESSION['previous_nama_belakang'] = $_POST['nama_belakang'];
				$_SESSION['previous_jenis_kelamin'] = $_POST['jenis_kelamin'];
				$_SESSION['previous_tanggal_lahir'] = $_POST['tanggal_lahir'];
				$_SESSION['previous_sakramen_yang_pernah_diterima'] = $_POST['sakramen_yang_pernah_diterima'];
				$_SESSION['previous_umat_santo_laurensius'] = $_POST['umat_santo_laurensius'];
				$_SESSION['previous_alamat_lengkap'] = $_POST['alamat_lengkap'];
				$_SESSION['previous_lingkungan'] = $_POST['lingkungan'];
				$_SESSION['previous_wilayah'] = $_POST['wilayah'];
				$_SESSION['previous_kota_kelahiran'] = $_POST['kota_kelahiran'];
				$_SESSION['previous_negara_kelahiran'] = $_POST['negara_kelahiran'];
				$_SESSION['previous_nomor_telepon'] = $_POST['nomor_telepon'];
				$_SESSION['previous_posisi_dalam_keluarga'] = $_POST['posisi_dalam_keluarga'];
				$_SESSION['previous_nama_kepala_keluarga'] = $_POST['nama_kepala_keluarga'];
				$_SESSION['previous_nomor_surat_baptis'] = $_POST['nomor_surat_baptis'];
				$_SESSION['previous_nomor_kartu_keluarga_katolik'] = $_POST['nomor_kartu_keluarga_katolik'];
				wp_redirect(site_url() . '/data-umat/');
				exit();
			}
	}
?>
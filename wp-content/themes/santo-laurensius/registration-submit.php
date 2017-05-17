<?php
/**
 * Template Name: Registration Submit
 */
session_start();
global $wpdb;
$got_error = FALSE;
	if($_POST)
	{
		//We shall SQL escape all inputs
		$username = $wpdb->escape($_POST['username']);
		if(empty($username)) 
		{
			$got_error = TRUE;
			$_SESSION['username_empty'] = "Nama akun tidak boleh dikosongkan.";
		}
		else if (!preg_match("/^[_a-zA-Z0-9]{6,32}$/", $username))
		{
			$got_error = TRUE;
			$_SESSION['username_format'] = "Format nama akun harus mengikuti peraturan diatas";
		}
		$email = $wpdb->escape($_POST['email']);
		if(empty($email)) 
		{
			$got_error = TRUE;
			$_SESSION['email_empty'] = "Alamat <em>e-mail</em> tidak boleh dikosongkan.";
		}
		else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) 
		{
			$got_error = TRUE;
			$_SESSION['email_format'] = "Alamat <em>e-mail</em> tidak valid; mohon periksa kembali.";
		}
		$password = $wpdb->escape($_POST['password']);
		if(empty($password)) 
		{
			$got_error = TRUE;
			$_SESSION['password_empty'] = "Kode sandi tidak boleh dikosongkan.";
		}
		else if (!preg_match("/^[\-_a-zA-Z0-9]{6,32}$/", $password))
		{
			$got_error = TRUE;
			$_SESSION['password_format'] = "Format kode sandi / <em>password</em> harus mengikuti peraturan diatas.";
		}
		$password_konfirmasi = $wpdb->escape($_POST['password_konfirmasi']);
		if(empty($password_konfirmasi)) 
		{
			$got_error = TRUE;
			$_SESSION['password_konfirmasi_empty'] = "Kode sandi harus diketik dua kali.";
		}
		else if ($password != $password_konfirmasi)
		{
			$got_error = TRUE;
			$_SESSION['password_konfirmasi_gagal'] = "Kode sandi yang diketik untuk kedua kalinya tidak sama dengan yang pertama kali diketik.";
		}
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
		$katolik = $wpdb->escape($_POST['katolik']);
		$amin_check = $wpdb->escape($_POST['amin_check']);
		if(empty($katolik)) 
		{
			$got_error = TRUE;
			$_SESSION['katolik_empty'] = "Anda harus menyatakan apakah Anda Katolik atau tidak.";
		}
		else if ($katolik == 'ya' AND empty($amin_check))
		{
			$got_error = TRUE;
			$_SESSION['amin_empty'] = "Jika Anda sungguh-sungguh Katolik, maka Anda harus mengakui iman Anda.";
		}
		$captcha_test = $wpdb->escape($_POST['captcha_test']);
		if(empty($captcha_test)) 
		{
			$got_error = TRUE;
			$_SESSION['captcha_empty'] = "Anda harus membuktikan bahwa registrasi dilakukan oleh Anda sendiri.";
		}
		else
		{
			$captcha_instance = new ReallySimpleCaptcha();
			$correct = $captcha_instance->check( $_SESSION['prefixvar'], $captcha_test );
			unset($_SESSION['prefixvar']);
			if (!$correct)
			{
				$got_error = TRUE;
				$_SESSION['captcha_false'] = "Maaf, kode Anti-Otomatisasi yang Anda masukkan salah.";
			}
		}
		
		if (!$got_error)
		{
			//$status = -1;
			$status = wp_create_user( $wpdb->escape($_POST['username']), $wpdb->escape($_POST['password']), $wpdb->escape($_POST['email']) );
			//$status = wp_create_user( $_POST['username'], $_POST['password'], $_POST['email'] );
			if (is_wp_error($status) )
			{
				$_SESSION['username_format'] = "Maaf, nama akun dan/atau e-mail yang ingin anda pakai sudah digunakan orang lain.";
				$_SESSION['previous_username'] = $_POST['username'];
				$_SESSION['previous_email'] = $_POST['email'];
				$_SESSION['previous_nama_depan'] = $_POST['nama_depan'];
				$_SESSION['previous_nama_belakang'] = $_POST['nama_belakang'];
				$_SESSION['previous_katolik'] = $_POST['katolik'];

				wp_redirect(site_url() . '/pendaftaran/');
				exit();
			}
			else 
			{
				update_user_meta($status, 'first_name', $wpdb->escape($_POST['nama_depan']));
				update_user_meta($status, 'last_name', $wpdb->escape($_POST['nama_belakang']));
				$_SESSION['berhasil'] = 'Terima kasih! Pendaftaran Akun yang Anda lakukan berhasil.';
				if ($_POST['katolik'] == 'ya')
				{
					unset($_POST['katolik']);
					wp_redirect(site_url() . '/data-umat/');
					exit();
				}
				else
				{
					$hasil = add_user_meta( $status, 'katolik', 'bukan');
					if ($hasil)
					{
						unset($_POST['katolik']);
						wp_redirect(site_url());
						exit();
					}
				}
			}
		}
		else
		{
			$_SESSION['previous_username'] = $_POST['username'];
			$_SESSION['previous_email'] = $_POST['email'];
			$_SESSION['previous_nama_depan'] = $_POST['nama_depan'];
			$_SESSION['previous_nama_belakang'] = $_POST['nama_belakang'];
			$_SESSION['previous_katolik'] = $_POST['katolik'];

			wp_redirect(site_url() . '/pendaftaran/');
			exit();
		}
	}
?>
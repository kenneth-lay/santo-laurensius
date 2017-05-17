<?php
/**
 * Template Name: You Catholic Submit
 */
	session_start();
	$got_error = FALSE;
	if($_POST)
	{
		$amin_check = $wpdb->escape($_POST['amin_check']);
		if(empty($amin_check))
		{
			$got_error = TRUE;
			$_SESSION['amin_empty'] = "Jika Anda sungguh-sungguh Katolik, maka Anda harus mengakui iman Anda.";
		}
		
		if (!$got_error)
		{
			global $current_user, $wpdb;
    	  	get_currentuserinfo();
  			update_user_meta( $current_user->ID, 'katolik', '');
  			$_SESSION['berhasil'] = 'Syukur kepada Allah karena Anda adalah seorang Katolik!';
			wp_redirect(site_url() . '/verifikasi/');
			exit();
		}
		else
		{
			$got_error = FALSE;
			wp_redirect(site_url() . '/anda-katolik/');
			exit();
		}
	}
?>
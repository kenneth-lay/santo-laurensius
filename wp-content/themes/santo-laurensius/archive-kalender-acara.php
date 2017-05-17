<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<?php
		global $tgl, $bln, $thn, $sttg, $penyusun, $tpt, $bulan_indonesia;
		if(isset($wp_query->query_vars['sttg'])) 
		{
			$sttg = urldecode($wp_query->query_vars['sttg']);
		}
		else
		{
			$sttg = '';
		}
		if(isset($wp_query->query_vars['penyusun'])) 
		{
			$penyusun = urldecode($wp_query->query_vars['penyusun']);
		}
		else
		{
			$penyusun = '';
		}
		if(isset($wp_query->query_vars['tempat'])) 
		{
			$tpt = urldecode($wp_query->query_vars['tempat']);
		}
		else
		{
			$tpt = '';
		}
		if(isset($wp_query->query_vars['tanggal'])) 
		{
			$tgl = urldecode($wp_query->query_vars['tanggal']);
		}
		if(isset($wp_query->query_vars['bulan'])) 
		{
			$bln = urldecode($wp_query->query_vars['bulan']);
		}
		if(isset($wp_query->query_vars['tahun'])) 
		{
			$thn = urldecode($wp_query->query_vars['tahun']);
		}
		//echo urldecode($wp_query->query_vars['tanggal']);

?>
<?php 
	get_template_part( 'snippet', 'archive-head' );
?>
<?php if ($sttg == '' AND $penyusun == '' AND $tpt == '')
	{
?>
		Arsip Acara dan Rencana
<?php
	}
	else if ($penyusun != '')
	{
		$uobject = get_user_by('login', $penyusun);
		$nama_lengkap = $penyusun;
		if ($uobject->first_name != '')
		{
			$nama_lengkap = $uobject->first_name;
		}
		if ($uobject->last_name != '')
		{
			$nama_lengkap = $nama_lengkap . ' ' . $uobject->last_name;
		}
		echo "Arsip Acara yang Disusun oleh " . $nama_lengkap;
	}
	else if ($tpt != '')
	{
		echo "Arsip Acara yang Berlokasi di " . $tpt;
	}
	else
	{
		if ($sttg == 'mulai')
			echo "Arsip Acara: Mulai ";
		else
			echo "Arsip Acara: ";
		if ($thn != '')
		{
			if ($bln != '')
			{
				if ($tgl != '')
				{
					echo $tgl . ' ' . $bulan_indonesia[$bln] . ' ' . $thn;
				}
				else
				{
					echo $bulan_indonesia[$bln] . ' ' . $thn;
				}
			}
			else
			{
				echo $thn;
			}
			if ($sttg == 'mulai')
				echo  ' dan sesudahnya';
			else
				echo ' dan sebelumnya';
		}
	}
?>
<?php get_template_part('snippet', 'archive-foot'); ?>
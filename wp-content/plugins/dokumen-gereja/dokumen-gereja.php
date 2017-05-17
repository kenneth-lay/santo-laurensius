<?php
/**
 * Plugin Name: Dokumen Gereja
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

add_action( 'init', 'create_dokumen_gereja' );

function create_dokumen_gereja()
{
	$args = array
	(
		'label' => 'Dokumen Gereja',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 23,
		'supports' => array(''),
		'has_archive' => true,
		'public' => true
	);
    register_post_type('dokumen-gereja', $args );
    $args = array
	(
		'label' => 'Tipe Dokumen Gereja',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 24,
		'supports' => array(''),
		'has_archive' => false,
		'public' => true
	);
    register_post_type('tipe-dokumen-gereja', $args );
}

?>
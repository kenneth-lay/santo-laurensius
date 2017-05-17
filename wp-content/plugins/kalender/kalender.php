<?php
/**
 * Plugin Name: Kalender Acara
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */


function instal_kalender_acara()
{
	register_taxonomy('kalender-acara', 'kalender-acara', array('hierarchical' => false, 'label' => 'Kalender Acara', 'show_ui' => false, 'query_var' => 'kalender-acara', 'rewrite' => array( 'slug' => 'kalender-acara')) );
} 

add_action('activate_kalender/kalender.php', 'instal_kalender_acara');

add_action( 'init', 'create_post_type' );

function create_post_type()
{
	$args = array
	(
		'label' => 'Kalender Acara',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 25,
		'supports' => array('comments'),
		'has_archive' => true,
		'public' => true
	);
    register_post_type('kalender-acara', $args );
}

////
add_action( 'widgets_init', 'load_widget_kalender_acara' );

function load_widget_kalender_acara() 
{
	register_widget('Kalender_Acara');
}


class Kalender_Acara extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Kalender_Acara() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'kalender-acara', 
		'description' => 'Sidebar kalender acara dan kegiatan');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'kalender-acara' );

		/* Create the widget. */
		$this->WP_Widget('kalender-acara', 'Kalender Acara', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );		
		echo $before_widget;
		?>
		<div class="coltitle">Jadwal Kegiatan</div>
		<div class="span-6 last fill-cream">
		<?php
			global $post;
			$args = array( 'post_type' => 'kalender-acara', 'posts_per_page' => 10, 'paged' => get_query_var('paged'), 'order' => 'ASC', 'orderby' => 'meta_value', 'meta_key' => 'tanggal_mulai');
			$loop = new WP_Query( $args );
			$k = 0;
			//print_r($loop);
			while ( $loop->have_posts() ) : $loop->the_post();
				$tanggal_notif = get_post_meta($post->ID, 'notifikasi_ditampilkan_mulai_tanggal', true);
				$tanggal_mulai = get_post_meta($post->ID, 'tanggal_mulai', true);
				$jam_mulai = get_post_meta($post->ID, 'jam_mulai', true);
				$tanggal_selesai = get_post_meta($post->ID, 'tanggal_selesai', true);
				$jam_selesai = get_post_meta($post->ID, 'jam_selesai', true);
				if (date('Y-m-d') >= $tanggal_notif AND date('Y-m-d') <= $tanggal_selesai)
				{
					if ($jam_selesai == '' OR date('Y-m-d') < $tanggal_selesai OR (date('Y-m-d') == $tanggal_selesai AND $jam_selesai <= date('H:m')))
					{
						if ($tanggal_mulai <= $tanggal_selesai)
						{
							if ($jam_mulai == '' OR $jam_selesai == '' OR $tanggal_mulai != $tanggal_selesai OR $jam_mulai <= $jam_selesai)
							{
								if ($tanggal_notif <= $tanggal_selesai)
								{
									global $bulan_indonesia;
									$tanggal_mulai = explode('-', get_post_meta($post->ID, 'tanggal_mulai', true));
									$tanggal_selesai = explode('-', get_post_meta($post->ID, 'tanggal_selesai', true));
									$onklik = 'window.location.href = \'' . get_permalink($post->ID) . '\'';
									echo '<div class = "kalender-instance box-small" onclick = "' . $onklik . '">';
									echo '<a href = "' . get_permalink($post->ID) . '" class = "link-maroon" style = "font-weight: bold; font-size: 14px; color: #CC3300;">';
									the_title();
									echo '</a>';
									echo '<br />';
									echo '<strong>Mulai: </strong>';
								?>
									<a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . $tanggal_mulai[2] . '/'; ?>"><?php echo $tanggal_mulai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . '">' . $bulan_indonesia[$tanggal_mulai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . '">' . $tanggal_mulai[0] . '</a>'; ?>	
								<?php
									if ($jam_mulai)
									{
										echo ' / ' . $jam_mulai;
									}
									echo '<br />';
									echo '<strong>Selesai: </strong>';
								?>
									<a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . $tanggal_selesai[2] . '/'; ?>"><?php echo $tanggal_selesai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . '">' . $bulan_indonesia[$tanggal_selesai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . '">' . $tanggal_selesai[0] . '</a>'; ?>
								<?php
									if ($jam_selesai)
									{
										echo ' / ' . $jam_selesai;
									}
									echo '</div>';
									$k = $k + 1;
									if ($k >= 10)
									{
										break;
									}
								}
							}
						}
					}
				}
			endwhile;
			if ($k == 0)
			{
				echo '<div class = "kalender-instance box-small">Belum ada acara dalam waktu dekat</div>';
			}
		?>
		</div>
		<div class="span-6 last" style = "text-align: right; margin-top: 5px; margin-bottom: -5px;"><a href = "<?php echo home_url('/kalender-acara/'); ?>" class = "link-maroon">— Lihat semua &rarr;</a></div>
		<?php
			/* After widget (defined by themes). */
			echo $after_widget;
			//echo '&nbsp;';
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form ($instance) 
	{
		/* Set up some default widget settings. */
		$defaults = array();
		$instance = wp_parse_args((array)$instance, $defaults );
	}
}

?>
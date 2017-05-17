<?php
/**
 * Plugin Name: Inspirasi Hari Ini
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */
 

function instal_inspirasi()
{
    global $wpdb;
    $table = $wpdb->prefix."inspirasi";
    $structure = "CREATE TABLE $table (
        id INT(8),
        tanggal VARCHAR(10),
        semaphore INT(1)
    );";
    $wpdb->query("INSERT INTO $table VALUES (-1,'0', NULL)");
    $wpdb->query($structure);
    $wpdb->query("CREATE UNIQUE INDEX indeks_id ON $table (id)");
    $table = $wpdb->prefix."inspirasi_count";
    $structure = "CREATE TABLE $table (
        id INT(8),
        frekuensi INT(8)
    );";
    $wpdb->query($structure);
    $wpdb->query("CREATE UNIQUE INDEX indeks_id2 ON $table (id)");
} 

add_action('activate_inspirasi/inspirasi.php', 'instal_inspirasi');

function uninstall_inspirasi()
{
    global $wpdb;
    $table = $wpdb->prefix."inspirasi";
    $structure = "DROP TABLE $table;";
    $wpdb->query($structure);
    $table = $wpdb->prefix."inspirasi_count";
    $structure = "DROP TABLE $table;";
    $wpdb->query($structure);
} 

add_action('deactivate_inspirasi/inspirasi.php', 'uninstall_inspirasi');

add_action( 'init', 'create_inspirasi' );

function create_inspirasi()
{
	$args = array
	(
		'label' => 'Inspirasi',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 26,
		'supports' => array(''),
		'has_archive' => false,
		'public' => true
	);
    register_post_type('inspirasi', $args );
    $args = array
	(
		'label' => 'Inspirator',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 27,
		'supports' => array(''),
		'has_archive' => false,
		'public' => true
	);
    register_post_type('inspirator', $args );
}

/////
 

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
 
add_action( 'widgets_init', 'load_widget_inspirasi' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_inspirasi() 
{
	register_widget('Inspirasi');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Inspirasi extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Inspirasi() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'inspirasi', 
		'description' => 'Panel berisi inspirasi yang diperbaharui setiap hari');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'inspirasi' );

		/* Create the widget. */
		$this->WP_Widget('inspirasi', 'Inspirasi Hari Ini', $widget_ops, $control_ops);
	}
	

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		global $wpdb;
		$konten_inspirasi = '';
		$nama_inspirator = '';
		$deskripsi_inspirator = '';
		$frek_new = 1;
		$id_hari_ini = -1;
		$table = $wpdb->prefix."inspirasi";
		$tanggals = $wpdb->get_results("SELECT tanggal FROM $table");
		$semap = FALSE;
		foreach($tanggals as $tanggal)
		{
			if ($tanggal->tanggal != date("Ymd"))
			{
				$semaphores = $wpdb->get_results("SELECT semaphore FROM $table");
				$semap = TRUE;
				foreach ($semaphores as $semaphore)
				{
					if ($semaphore->semaphore == NULL OR $semaphore->semaphore == -1)
					{
						$wpdb->query("UPDATE $table SET semaphore = 1");
						$table = $wpdb->prefix."inspirasi_count";
						$min_frekuensi = $wpdb->get_results("SELECT MIN(frekuensi) AS minf FROM $table");
						foreach ($min_frekuensi as $mf)
						{
							$minfrek = $mf->minf;
							$frek_new = $minfrek + 1;
							$inspirasi_hari_ini = $wpdb->get_results("SELECT id FROM $table WHERE frekuensi = $minfrek"); 
							foreach ($inspirasi_hari_ini as $ihi)
							{
								$id_hari_ini = $ihi->id;
								break;
							}
							$konten_inspirasi = get_post_meta($id_hari_ini, 'konten_inspirasi', true);
							$nama_inspirator = get_the_title(get_post_meta($id_hari_ini, 'nama_inspirator_select', true));	
							$deskripsi_inspirator = get_post_meta(get_post_meta($id_hari_ini, 'nama_inspirator_select', true), 'deskripsi_inspirator', true);	
							$table = $wpdb->prefix."inspirasi_count";
							$wpdb->query("UPDATE $table SET frekuensi = $frek_new WHERE id = $id_hari_ini");
							update_post_meta($id_hari_ini, 'frekuensi', $frek_new);
						}
					}
				}
			}
			else
			{
				$table = $wpdb->prefix."inspirasi";
				$inspirasi_hari_ini = $wpdb->get_results("SELECT id FROM $table");
				foreach ($inspirasi_hari_ini as $ihi)
				{
					$id_hari_ini = $ihi->id;
					break;
				}
				$konten_inspirasi = get_post_meta($id_hari_ini, 'konten_inspirasi', true);
				$nama_inspirator = get_the_title(get_post_meta($id_hari_ini, 'nama_inspirator_select', true));	
				$deskripsi_inspirator = get_post_meta(get_post_meta($id_hari_ini, 'nama_inspirator_select', true), 'deskripsi_inspirator', true);
			}
		}	
		extract( $args );
		echo $before_widget;
		global $bulan_indonesia;
		?>
			<?php
				//echo esc_url($before_title);
			?>
				<div class="coltitle">Inspirasi Hari Ini</div>
				<div class="span-6 last fill-cream" style = "border-bottom: 3px solid #CC3300;">
			<?php
				//echo esc_url($after_title);
			?>	
				<div class = "box-small" style = "text-align: justify; padding-right: 10px; padding-bottom: 10px;">
			<?php 
				$tanggal_today = date("d-m-Y");
				$tanggal_array = explode("-", $tanggal_today);
				echo '<div class = \'prepend-top-negative-medium\'>&nbsp</div><span style = "color: #CC3300; font-size: 14px;">' . $tanggal_array[0] . ' ' . $bulan_indonesia[$tanggal_array[1]] . ' ' . $tanggal_array[2] . '</span>';
				echo "<div class = 'prepend-top-negative-small'>&nbsp</div> $konten_inspirasi <div class = 'prepend-top-negative-medium'>&nbsp</div>___________________________________<br /> <strong>$nama_inspirator</strong> <br /> <span style = 'color: #606060;'><em>$deskripsi_inspirator</em></span>";
				$tanggal_new = date("Ymd");
				//echo $tanggal_new;
				$table = $wpdb->prefix."inspirasi";
				if ($semap)
				{
					$wpdb->query("UPDATE $table SET semaphore = -1, id = $id_hari_ini, tanggal = $tanggal_new");
					$semap = FALSE;
				}
		 	?> 
		 		</div>
		 	</div>
		<?php
			/* After widget (defined by themes). */
			echo $after_widget;
			echo "&nbsp;";
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
	function form( $instance ) 
	{
		/* Set up some default widget settings. */
		$defaults = array();
	}
}

?>
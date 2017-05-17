<?php
/**
 * Plugin Name: Mulai dan Sesudahnya
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_mulai_dan_sesudahnya' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_mulai_dan_sesudahnya() 
{
	register_widget('Mulai_Dan_Sesudahnya');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Mulai_Dan_Sesudahnya extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Mulai_Dan_Sesudahnya() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'mulai-dan-sesudahnya', 
		'description' => 'Panel arsip acara berdasarkan waktu mulai');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'mulai-dan-sesudahnya' );

		/* Create the widget. */
		$this->WP_Widget('mulai-dan-sesudahnya', 'Mulai dan Sesudahnya', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		global $bulan_indonesia;
		echo $before_widget;
		?>
			<?php
				//echo esc_url($before_title);
			?>
				<div class="coltitle">Mulai Acara:</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php 
				global $wpdb;
				$entri_acara = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"kalender-acara\" AND post_status = \"publish\"");
		 		$arsip = array();
		 		foreach ($entri_acara as $ea)
		 		{
		 			$tanggal_mulai = get_post_meta($ea->ID, 'tanggal_mulai', true);
		 			$tanggal_array = explode("-", $tanggal_mulai);
		 			$bulan_tahun = $tanggal_array[0] . '_' . $tanggal_array[1];
		 			if(!isset($arsip[$bulan_tahun]) OR $arsip[$bulan_tahun] != 1)
		 			{
		 				$arsip[$bulan_tahun] = 1;
		 			}
		 		}
		 		$key_array = array();
		 		foreach ($arsip as $key=>$value)
		 		{
		 			$key_array[] = $key;
		 		}
		 		rsort($key_array);
		 		//print_r ($key_array);
		 		foreach ($key_array as $key_inst)
		 		{
		 			$tanggal_mulai = explode('_', $key_inst);
		 			echo '<a class = "listblock acara-links" style = "color: #CC3300; text-decoration: none;" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . '">' . $bulan_indonesia[$tanggal_mulai[1]] . ' ' . $tanggal_mulai[0] . '</a>';
		 		}
		 	?> 
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
	function form ($instance) 
	{
		/* Set up some default widget settings. */
		$defaults = array();
		$instance = wp_parse_args((array)$instance, $defaults );
	}
}

?>
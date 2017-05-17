<?php
/**
 * Plugin Name: Panel Dokumen Gereja
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_panel_dg' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_panel_dg() 
{
	register_widget('Panel_dg');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Panel_dg extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Panel_dg() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'panel-dokumen-gereja', 
		'description' => 'Panel tautan untuk koleksi Dokumen Gereja');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'panel-dokumen-gereja' );

		/* Create the widget. */
		$this->WP_Widget('panel-dokumen-gereja', 'Panel Dokumen Gereja', $control_ops);
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
				<div class="coltitle">Dokumen Gereja</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php 
				global $wpdb;
				$jenis_dokumens = $wpdb->get_results("SELECT id, post_name FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\"");
		 		foreach ($jenis_dokumens as $jenis_dokumen)
		 		{
		 			echo '<a class = "listblock acara-links" style = "color: #CC3300; text-decoration: none;" href = "' . site_url('/dokumen-gereja/') . $jenis_dokumen->post_name . '/">' . get_the_title($jenis_dokumen->id) . '</a>';
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
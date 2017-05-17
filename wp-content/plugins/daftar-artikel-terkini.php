<?php
/**
 * Plugin Name: Daftar Artikel Terkini
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_daftar_artikel_terkini' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_daftar_artikel_terkini() 
{
	register_widget('Daftar_Artikel_Terkini');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Daftar_Artikel_Terkini extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Daftar_Artikel_Terkini() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'daftar-artikel-terkini', 
		'description' => 'Panel yang merincikan semua artikel terbaru');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'daftar-artikel-terkini' );

		/* Create the widget. */
		$this->WP_Widget('daftar-artikel-terkini', 'Artikel Terkini', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		
		echo $before_widget;
		?>
			<?php
				//echo esc_url($before_title);
			?>
				<div class="coltitle">Artikel Terkini</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php 
				$args = array
				(
    				'numberposts' => 5,
    				'offset' => 0,
    				'category' => 0,
    				'orderby' => 'post_date',
    				'order' => 'DESC',
    				'include' => '',
    				'exclude' => '',
    				'meta_key' => '',
    				'meta_value' => '',
    				'post_type' => 'post',
    				'post_status' => 'publish',
    				'suppress_filters' => true 
    			);
		 		$artikels = wp_get_recent_posts($args); 
		 		//print_r($artikels);
		 		foreach ($artikels as $artikel)
		 		{
					echo '<a class = "listblock link-maroon-2" style = "color: #CC3300;" href = "' . esc_url(get_permalink($artikel['ID'])) . '">' . $artikel['post_title'] . '</a>';
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
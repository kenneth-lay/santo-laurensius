<?php
/**
 * Plugin Name: Profil
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_profil' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_profil() 
{
	register_widget('Profil');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Profil extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Profil() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'panel-profil', 
		'description' => 'Profil singkat paroki');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'panel-profil' );

		/* Create the widget. */
		$this->WP_Widget('panel-profil', 'Profil Singkat Gereja', $widget_ops, $control_ops);
	}
	

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		$teks = $instance['teks'];
		echo $before_widget;
		?>
			<?php
				//echo esc_url($before_title);
			?>
				<div class="coltitle">Profil Singkat Gereja</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
				<div>
			<?php 
				if ($teks)
				{	
					echo $teks;
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
		$instance['teks'] = $new_instance['teks'];
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
		$instance = wp_parse_args((array)$instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('teks'); ?>">Teks: </label>
			<textarea style = "min-height: 200px;" id="<?php echo $this->get_field_id('teks'); ?>" name="<?php echo $this->get_field_name('teks'); ?>"><?php echo $instance['teks']; ?></textarea>
		</p>
	<?php
	}
}

?>
<?php
/**
 * Plugin Name: Kontak - Jejaring Sosial
 * Plugin URI: http://www.santo-laurensius.org/
 * Description: Kontak Facebook dan Twitter
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_kontak_jejaring_sosial' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_kontak_jejaring_sosial() 
{
	register_widget('Kontak_Jejaring_Sosial');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Kontak_Jejaring_Sosial extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Kontak_Jejaring_Sosial() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'kontak_jejaring_sosial', 
		'description' => 'Menampilkan tautan ke halaman Twitter dan Facebook kita');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'kontak_jejaring_sosial' );

		/* Create the widget. */
		$this->WP_Widget('kontak_jejaring_sosial', 'Kontak Jejaring Sosial', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );

		/* Our variables from the widget settings. */
		$facebook = $instance['facebook'];
		$twitter = $instance['twitter'];

		echo $before_widget;
		?>

		<div class="span-6">
		<?php
		echo $before_title;
		?>
		Kami ada di:
		<?php
		echo $after_title;
		/* Display name from widget settings if one was input. */
		if ($facebook)
		{
			echo '<div class="span-6 last">';
			echo '<a href="http://www.facebook.com/' . $facebook . '" class="link facebook">Facebook ';
			echo '<menu-explanation>' . $facebook . '</menu-explanation></a>';
			echo '</div>';
		}
		
		/* If show sex was selected, display the user's sex. */
		if ($twitter)
		{
			echo '<div class="span-6 last">';
			echo '<a href="http://www.twitter.com/' . $twitter . '" class="link twitter">Twitter ';
			echo '<menu-explanation>@' . $twitter . '</menu-explanation></a>';
			echo '</div>';
		}
		?>
		</div>
		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['facebook'] = $new_instance['facebook'];
		$instance['twitter'] = $new_instance['twitter'];
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
		$defaults = array('facebook' => 'GerejaSantoLaurensius', 'twitter' => 'SantoLaurensius');
		$instance = wp_parse_args((array)$instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('facebook'); ?>">Akun Facebook: http://www.facebook.com/</label>
			<input id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $instance['facebook']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('twitter'); ?>">Akun Twitter: http://www.twitter.com/</label>
			<input id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $instance['twitter']; ?>" />
		</p>

	<?php
	}
}

?>
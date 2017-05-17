<?php
/**
 * Plugin Name: Informasi Kontak
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_informasi_kontak' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_informasi_kontak() 
{
	register_widget('Informasi_Kontak');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Informasi_Kontak extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Informasi_Kontak() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'informasi_kontak', 
		'description' => 'Detail kontak untuk menghubungi paroki');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'informasi_kontak' );

		/* Create the widget. */
		$this->WP_Widget('informasi_kontak', 'Informasi Kontak', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );

		/* Our variables from the widget settings. */
		$tempat = $instance['tempat'];
		$alamat = $instance['alamat'];
		$telepon = $instance['telepon'];
		$faks = $instance['faks'];
		$email = $instance['email'];
		
		echo $before_widget;
		?>

		<div class="span-6">
		<?php
			echo $before_title;
		?>
		Kontak:
		<?php
			echo $after_title;
			/* Display name from widget settings if one was input. */
			if ($alamat)
			{
				echo '<strong>' . $tempat . '</strong>';
				echo '<ul class="indentation">';
				echo '<li>';
				echo nl2br($alamat);
				echo '</li>';
			}
			if ($telepon)
			{
				echo '<li>';
				echo '<strong>Telp: </strong>' . $telepon . '<br />';
				echo '</li>';
			}
			if ($faks)
			{
				echo '<li>';
				echo '<strong>Fax: </strong>' . $faks . '<br />';
				echo '</li>';
			}
			if ($email)
			{
				echo '<li><strong>e-mail: </strong><br />';
				$email1 = str_ireplace('@', ' [^] ', $email);
				$email2 = str_ireplace('.', ' [*] ', $email1);
				echo '<em style = "font-size: 10px;">' . $email2 . '</em>';
				
			?>
				<br />
				<span class = "small-text">Ganti [^] dengan @, dan [*] dengan . (titik)</span>
				<div class = "prepend-top-negative-small">&nbsp;</div>
			<?php
				echo '</li>';
			}
			echo '</ul>';
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
		$instance['tempat'] = $new_instance['tempat'];
		$instance['alamat'] = $new_instance['alamat'];
		$instance['telepon'] = $new_instance['telepon'];
		$instance['faks'] = $new_instance['faks'];
		$instance['email'] = $new_instance['email'];
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
		$instance = wp_parse_args((array)$instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id('tempat'); ?>">Tempat: </label>
			<input id="<?php echo $this->get_field_id('tempat'); ?>" name="<?php echo $this->get_field_name('tempat'); ?>" value="<?php echo $instance['tempat']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('alamat'); ?>">Alamat: </label>
			<textarea id="<?php echo $this->get_field_id('alamat'); ?>" name="<?php echo $this->get_field_name('alamat'); ?>"><?php print $instance['alamat'] ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('telepon'); ?>">Telepon: </label>
			<input id="<?php echo $this->get_field_id('telepon'); ?>" name="<?php echo $this->get_field_name('telepon'); ?>" value="<?php echo $instance['telepon']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('faks'); ?>">Faks: </label>
			<input id="<?php echo $this->get_field_id('faks'); ?>" name="<?php echo $this->get_field_name('faks'); ?>" value="<?php echo $instance['faks']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>">e-mail: </label>
			<input id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" value="<?php echo $instance['email']; ?>" />
		</p>
	<?php
	}
}

?>
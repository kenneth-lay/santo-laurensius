<?php
/**
 * Plugin Name: Jadwal Sakramen
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_jadwal_sakramen' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_jadwal_sakramen() 
{
	register_widget('Jadwal_Sakramen');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Jadwal_Sakramen extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Jadwal_Sakramen() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'jadwal_sakramen', 
		'description' => 'Jadwal misa dan sakramen rekonsiliasi');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'jadwal_sakramen' );

		/* Create the widget. */
		$this->WP_Widget('jadwal_sakramen', 'Jadwal Sakramen', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );

		/* Our variables from the widget settings. */
		$ekaristi_mingguan = $instance['ekaristi mingguan'];
		$ekaristi_harian = $instance['ekaristi harian'];
		$sakramen_rekonsiliasi = $instance['sakramen rekonsiliasi'];
		
		echo $before_widget;
		?>

		<div class="span-6">
		<?php
			echo $before_title;
		?>
		Perayaan Rutin:
		<?php
			echo $after_title;
			/* Display name from widget settings if one was input. */
			if ($ekaristi_mingguan)
			{
				echo '<strong>Ekaristi mingguan:</strong>';
				echo '<ul class="basic-list">';
				$em_array = explode('<br />', nl2br($ekaristi_mingguan));
				foreach ($em_array as $em_array_instance)
				{
					echo '<li>' . $em_array_instance . '</li>';
				}
				echo '</ul>';
			}
			if ($ekaristi_harian)
			{
				echo '<strong>Ekaristi harian:</strong>';
				echo '<ul class="basic-list">';
				$eh_array = explode('<br />', nl2br($ekaristi_harian));
				foreach ($eh_array as $eh_array_instance)
				{
					echo '<li>' . $eh_array_instance . '</li>';
				}
				echo '</ul>';
			}
			if ($sakramen_rekonsiliasi)
			{
				echo '<strong>Sakramen Rekonsiliasi:</strong>';
				$sr_array = explode('<br />', nl2br($sakramen_rekonsiliasi));
				if (count($sr_array) < 2)
				{
					echo '<ul class="indentation">';
					echo '<li>' . $sakramen_rekonsiliasi . '</li>';
				}
				else
				{
					echo '<ul class="basic-list">';
					foreach ($sr_array as $sr_array_instance)
					{
						echo '<li>' . $sr_array_instance . '</li>';
					}
				}
				echo '</ul>';
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
		$instance['ekaristi mingguan'] = $new_instance['ekaristi mingguan'];
		$instance['ekaristi harian'] = $new_instance['ekaristi harian'];
		$instance['sakramen rekonsiliasi'] = $new_instance['sakramen rekonsiliasi'];
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
			<label for="<?php echo $this->get_field_id('ekaristi mingguan'); ?>">Jadwal Ekaristi mingguan: </label>
			<textarea id="<?php echo $this->get_field_id('ekaristi mingguan'); ?>" name="<?php echo $this->get_field_name('ekaristi mingguan'); ?>"><?php print $instance['ekaristi mingguan'] ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('ekaristi harian'); ?>">Jadwal Ekaristi harian: </label>
			<textarea id="<?php echo $this->get_field_id('ekaristi harian'); ?>" name="<?php echo $this->get_field_name('ekaristi harian'); ?>"><?php print $instance['ekaristi harian'] ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('sakramen rekonsiliasi'); ?>">Jadwal Sakramen rekonsiliasi: </label>
			<textarea id="<?php echo $this->get_field_id('sakramen rekonsiliasi'); ?>" name="<?php echo $this->get_field_name('sakramen rekonsiliasi'); ?>"><?php print $instance['sakramen rekonsiliasi'] ?></textarea>
		</p>
	<?php
	}
}

?>
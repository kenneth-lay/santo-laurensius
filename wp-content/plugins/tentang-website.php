<?php
/**
 * Plugin Name: Tentang Website
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_tentang_website' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_tentang_website() 
{
	register_widget('Tentang_Website');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Tentang_Website extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Tentang_Website() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'tentang-website', 
		'description' => 'Tentang situs web ini');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'tentang-website' );

		/* Create the widget. */
		$this->WP_Widget('tentang-website', 'Tentang Website', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		$kenneth = $instance['kenneth'];

		/* Our variables from the widget settings. */
		
		echo $before_widget;
		?>

		<div class="span-6">
		<?php
			echo $before_title;
		?>
		Tentang Situs Ini:
		<?php
			echo $after_title;
			/* Display name from widget settings if one was input. */
		?>
		<?php $bloginfo = get_bloginfo('version'); ?> 
			Konten oleh <strong style = "text-style: italic;">Seksi Komunikasi Sosial Paroki Santo Laurensius</strong>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			Desain dan pembangunan situs oleh <a href = "<?php echo $kenneth; ?>" class = "kenneth">Kenneth Darmawan Lay</a>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			<!--
			<strong>Kontak: </strong><br />website [^] santo-laurensius [*] org
			<br />
			<span class = "small-text">Ganti [^] dengan @ (tanda 'at'), dan [*] dengan . (tanda titik)</span>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			-->
			☩ <em>Ad Maiorem Dei Gloriam</em>
			<div class = "wordpress-logo">
				Situs ini ditenagai oleh:
				<div class="span-6 last">
					<a href="http://www.wordpress.org/" class="link wplogo">
					Wordpress
					<menu-explanation>versi <?php echo $bloginfo; ?></menu-explanation></a>
				</div>
			</div>
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
		$instance['kenneth'] = $new_instance['kenneth'];
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
	?>
		<p>
			<label for="<?php echo $this->get_field_id('kenneth'); ?>">Kontak Kenneth: </label>
			<input id="<?php echo $this->get_field_id('kenneth'); ?>" name="<?php echo $this->get_field_name('kenneth'); ?>" value="<?php echo $instance['kenneth']; ?>" />
		</p>
	<?php
	}
}

?>
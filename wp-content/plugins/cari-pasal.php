<?php
/**
 * Plugin Name: Cari Pasal
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_cari_pasal' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_cari_pasal() 
{
	register_widget('Cari_Pasal');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Cari_Pasal extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Cari_Pasal() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'cari-pasal', 
		'description' => 'Panel untuk cari pasal dokumen gereja');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'cari-pasal' );

		/* Create the widget. */
		$this->WP_Widget('cari-pasal', 'Cari Nomor Pasal', $widget_ops, $control_ops);
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
				<div class="coltitle">Cari Nomor Pasal</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
					$iddok = -1;
					$iddok = id_dokumen_gereja();
					//echo $iddok;
			?>
					<form role="search" method="get" id="searchform" action="<?php echo get_home_url(); ?>">
						<div class = "box-small borderbottom" style="padding-bottom: 10px;">
							<div class = "prepend-top-negative-medium">&nbsp;</div>
							Cari berdasarkan <strong>nomor</strong> pasal dari dokumen ini:
							<br />
							<input type="text" style="width: 150px; height: 20px; font-size: 14px;" value="" name="nomor_pasal" id="nomor_pasal" />
							&nbsp;
							<input type="submit" id="searchsubmit" class = "medium" value="Cari" />
							<input type="hidden" name="post_type" value="dokumen-gereja" />
							<input type="hidden" name="kode_dokumen_gereja" value="<?php echo $iddok; ?>" />							
						</div>
					</form>
				</div>
		<?php
			/* After widget (defined by themes). */
		echo $after_widget;
		?>
		&nbsp;
		<?php
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
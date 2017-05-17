<?php
/**
 * Plugin Name: Cari Pasal (Antara)
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_cari_pasal_antara' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_cari_pasal_antara() 
{
	register_widget('Cari_Pasal_Antara');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Cari_Pasal_Antara extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Cari_Pasal_Antara() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'cari-pasal-antara', 
		'description' => 'Panel untuk cari pasal-pasal diantara dua pasal dalam dokumen gereja');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'cari-pasal-antara' );

		/* Create the widget. */
		$this->WP_Widget('cari-pasal-antara', 'Nomor Pasal Antara', $widget_ops, $control_ops);
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
				<div class="coltitle">Nomor Pasal Antara</div>
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
							Cari entri <strong>diantara kedua nomor</strong> pasal dari dokumen ini:
							<br />
							<input type="text" style="width: 75px; height: 20px; font-size: 14px;" value="" name="nomor_pasal_awal" id="nomor_pasal_awal" />
							&nbsp;sampai&nbsp;
							<input type="text" style="width: 75px; height: 20px; font-size: 14px;" value="" name="nomor_pasal_akhir" id="nomor_pasal_akhir" />
							<div style = "width: 170px;">
								<input style = "margin-left: 165px;" type="submit" id="searchsubmit" class = "medium" value="Cari" />
							</div>
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
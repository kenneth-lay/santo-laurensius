<?php
/**
 * Plugin Name: Pencarian
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_pencarian' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_pencarian() 
{
	register_widget('Pencarian');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Pencarian extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Pencarian() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'pencarian', 
		'description' => 'Panel untuk pencarian');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'pencarian' );

		/* Create the widget. */
		$this->WP_Widget('pencarian', 'Pencarian', $widget_ops, $control_ops);
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
				<?php
					if (substr_count(get_my_url(), 'kalender-acara'))
					{
				?>
						<div class="coltitle">Cari Acara</div>
				<?php
					}
					else
					{
				?>
						<div class="coltitle">Panel Pencarian</div>
				<?php
					}
				?>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
					$iddok = -1;
					global $wpdb;
					$tdok_a = explode("/dokumen-gereja/", get_my_url());
					if ($tdok_a[0] != get_my_url())
					{
						$tdok_a2 = explode("/", $tdok_a[1]);
						$tdok = $tdok_a2[0];
						$tipes = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\" AND post_name = \"$tdok\"");
						foreach ($tipes as $t)
						{
							$iddok = $t->ID;
						}
					}
					else
					{
						$tdok_a = explode("&kode_dokumen_gereja=", get_my_url());
						$tdok = $tdok_a[1];
						$iddok = $tdok;
					}
					//echo $iddok;
			?>
					<form role="search" method="get" id="searchform" action="<?php echo get_home_url(); ?>">
						<div class = "box-small borderbottom" style="padding-bottom: 10px;">
							<?php
								if (substr_count(get_my_url(), '/dokumen-gereja/') OR substr_count(get_my_url(), 'post_type=dokumen-gereja'))
								{
							?>
								<div class = "prepend-top-negative-medium">&nbsp;</div>
								Cari berdasarkan <strong>isi</strong> pasal dari dokumen ini:
								<br />
							<?php
								}
							?>
							<input type="text" style="width: 94%; height: 20px; font-size: 14px;" value="" name="s" id="s" />
							<br />
							<input type="submit" id="searchsubmit" class = "medium" value="Cari" />
							<?php
								if (substr_count(get_my_url(), 'kalender-acara'))
								{
							?>
									<input type="hidden" name="post_type" value="kalender-acara" />
							<?php
								}
								else if (substr_count(get_my_url(), '/dokumen-gereja/') OR substr_count(get_my_url(), 'post_type=dokumen-gereja'))
								{
							?>
									<input type="hidden" name="post_type" value="dokumen-gereja" />
									<input type="hidden" name="kode_dokumen_gereja" value="<?php echo $iddok; ?>" />
							<?php
								}
								else
								{
							?>
									<input type="hidden" name="post_type" value="post" />
							<?php
								}
							?>
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
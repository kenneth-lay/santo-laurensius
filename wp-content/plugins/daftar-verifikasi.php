<?php
/**
 * Plugin Name: Panel Daftar dan Verifikasi
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_daftar_verifikasi' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_daftar_verifikasi() 
{
	register_widget('Panel_Daftar_Verifikasi');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Panel_Daftar_Verifikasi extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Panel_Daftar_Verifikasi() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'panel-daftar-verifikasi', 
		'description' => 'Panel berisi tautan untuk pendaftaran dan verifikasi umat');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'panel-daftar-verifikasi' );

		/* Create the widget. */
		$this->WP_Widget('panel-daftar-verifikasi', 'Panel Daftar Verifikasi', $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		global $bulan_indonesia;
		?>
		<?php
			$okei = FALSE;
 			global $current_user;
			global $wpdb;
   			get_currentuserinfo();
   			$uid = $current_user->ID;
			$verif = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"data-umat\" AND post_status = \"publish\" AND post_author = $uid");
			foreach ($verif as $v)
			{
				$okei = TRUE;
			}
		?>
			<?php
				if (!is_user_logged_in())
				{
					echo $before_widget;
					$onclick_regis = "window.location.href = '" . site_url() . "/pendaftaran/';";
			?>
			<div class="span-6 last">
				<div class = "span-5 last regbutton fill-cream" onclick = "<?php echo $onclick_regis; ?>" >
					<h4 class = "menu-title"><a href = "<?php echo site_url() . '/pendaftaran/'; ?>" class = "link-italic">Pendaftaran</a></h4>
					<span class = "menu-explain">
						Klik disini untuk mendaftarkan diri Anda.
					</span>
				</div>
		 	</div>
		 	<?php
		 			echo $after_widget;
					echo "&nbsp;";
		 		}
		 	?>
		 	
		 	<?php
				if (is_user_logged_in() AND !current_user_can('edit_pages') AND !$okei AND get_user_meta($current_user->ID, 'katolik', true) == 'bukan')
				{
					echo $before_widget;
					$onclick_regis = "window.location.href = '" . site_url() . "/anda-katolik/';";
			?>
		 	<div class="span-6 last">
				<div class = "span-5 last regbutton cathbutton fill-cream" onclick = "<?php echo $onclick_regis; ?>" >
					<h4 class = "menu-title"><a href = "<?php echo site_url() . '/anda-katolik/'; ?>" class = "link-italic">Anda Katolik?</a></h4>
					<span class = "menu-explain">
						Jika ya, klik disini.
					</span>
				</div>
		 	</div>
		 	<?php
		 			echo $after_widget;
					echo "&nbsp;";
		 		}
		 	?>
		 	
		 	<?php
		 		if (is_user_logged_in() AND !$okei AND !current_user_can('edit_published_pages') AND !get_user_meta($current_user->ID, 'katolik', true))
				{
					echo $before_widget;
					$onclick_regis = "window.location.href = '" . site_url() . "/data-umat/';";
			?>
		 	<div class="span-6 last">
				<div class = "span-5 last regbutton verifbutton fill-cream" onclick = "<?php echo $onclick_regis; ?>" >
					<h4 class = "menu-title"><a href = "<?php echo site_url() . '/verifikasi/'; ?>" class = "link-italic">Pendataan Umat</a></h4>
					<span class = "menu-explain">
						Klik disini untuk melengkapi data Anda sebagai umat.
					</span>
				</div>
		 	</div>
		 	<?php
		 			echo $after_widget;
					echo "&nbsp;";
		 		}
		 	?>
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
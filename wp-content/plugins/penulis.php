<?php
/**
 * Plugin Name: Daftar Penulis
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_daftar_penulis' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_daftar_penulis() 
{
	register_widget('Daftar_Penulis');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Daftar_Penulis extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Daftar_Penulis() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'daftar-penulis', 
		'description' => 'Panel navigasi yang mendaftarkan semua penulis');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'daftar-penulis' );

		/* Create the widget. */
		$this->WP_Widget('daftar-penulis', 'Daftar Penulis', $widget_ops, $control_ops);
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
				if (substr_count(get_my_url(), "kalender-acara"))
				{
			?>
					<div class="coltitle">Daftar Penyusun</div>
			<?php
				}
				else
				{
			?>
					<div class="coltitle">Daftar Penulis</div>
			<?php
				}
			?>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php
				global $wpdb;
				$authors = null;
				if (substr_count(get_my_url(), "kalender-acara"))
				{
					$authors = $wpdb->get_results("SELECT DISTINCT(post_author) AS ID, COUNT(*) AS itungs FROM $wpdb->posts WHERE post_type = \"kalender-acara\" AND post_status = \"publish\" GROUP BY post_author ORDER BY itungs DESC");
				}
				else
				{
					$authors = $wpdb->get_results("SELECT DISTINCT(post_author) AS ID, COUNT(*) AS itungs FROM $wpdb->posts WHERE post_type = \"post\" AND post_status = \"publish\" GROUP BY post_author ORDER BY itungs DESC");
 				}
 				foreach($authors as $author) 
 				{
 					if (substr_count(get_my_url(), "kalender-acara"))
					{
						$window_href = "window.location.href='" . home_url('/kalender-acara/penyusun/') . urlencode(get_the_author_meta('login', $author->ID)) . "'";
					}
					else
					{
 						$window_href = "window.location.href='" . get_author_posts_url($author->ID) . "'";
 					}
 			?>
 					<div class = "author-list" onclick = "<?php echo $window_href; ?>">
 						<div class = "span-2" style = "width: 48px;">
 							<?php echo get_avatar($author->ID, 48); ?>
 						</div>
 						<div class = "author-details" style = "display: block" >
 							<?php
 								$user_info = get_userdata($author->ID);
 								if (substr_count(get_my_url(), "kalender-acara"))
								{
 							?>
 									<a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/penyusun/') . urlencode(get_the_author_meta('login', $author->ID)); ?>">
 							<?php
 								}
 								else
 								{
 							?>
 									<a class = "link-maroon" href = "<?php echo get_author_posts_url($author->ID); ?>">
 							<?php
 								}
 							?>
 							<?php
 								echo $user_info->first_name;
 								if ($user_info->last_name != '')
 								{
 									echo ' ' . $user_info->last_name;
 								}
 								echo '</a>';
 								echo '<br />';
 								if (substr_count(get_my_url(), "kalender-acara"))
								{
									echo '<span class = "jumlah-artikel">Telah menyusun ' . $author->itungs . ' acara</span>';
								}
								else
								{
 									echo '<span class = "jumlah-artikel">Telah menulis ' . $author->itungs . ' artikel</span>';
 								}
 							?>
 						</div>
 					</div>
			<?php
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
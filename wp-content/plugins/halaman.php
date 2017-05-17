<?php
/**
 * Plugin Name: Daftar Halaman
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_daftar_halaman' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_daftar_halaman() 
{
	register_widget('Daftar_Halaman');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Daftar_Halaman extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Daftar_Halaman() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'daftar-halaman', 
		'description' => 'Panel navigasi yang merincikan halaman');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'daftar-halaman' );

		/* Create the widget. */
		$this->WP_Widget('daftar-halaman', 'Daftar Halaman', $widget_ops, $control_ops);
	}
	
	function get_children($parent_id, &$halaman)
	{
		$i = 0;
		foreach($halaman as $hlm)
		{
			if ($hlm->post_parent == $parent_id)
			{
				if ($i == 0)
				{
					echo '<div class = "regularize" style = "background-color: #f86017; color: white; padding-left: 10px;">Dalam halaman ini:</div>';
					echo '<ul class = "subpages">';
					$i = $i + 1;
				}
				echo '<li><a class = "link-maroon regularize" href = "' . esc_url(get_page_link($hlm->ID)) . '">' . $hlm->post_title . '</a></li>';
			}		
		}
		if ($i == 1)
		{
			echo '</ul>';
		}
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		global $post;
		global $halamans;
		/*
		$args = array(
    		'child_of' => $post->ID
		);
		*/
		 $h = $halamans;
		 $counter = 0;
		 foreach ($h as $hi)
		 {
		 	if ($hi->post_parent == $post->ID)
		 	{
		 		$counter = $counter + 1;
		 	}
		 }
		 if($counter)
		 {
		 		$counter = 0;
				echo $before_widget;
				//echo esc_url($before_title);
				$judul_widget = '';
				$url = get_my_url();
				if ($post->post_type == "page" AND substr_count($url, "galeri"))
				{
					$judul_widget = 'Sub-Galeri';
				}
				else
				{
					$judul_widget = 'Sub-Halaman';
				}
			?>
			
				<div class="coltitle"><?php echo $judul_widget; ?></div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php 
				//echo $post->ID;
				$args = array(
    				'child_of' => $post->ID
				);
		 		//$halaman = get_pages($args); 
		 		$halaman = $halamans;
		 		$i = 0;
		 		$background_image = '';
		 		$perm = get_permalink( $post->ID );
		 		//$halaman_temp = get_pages(array("parent" => 0));
		 		$halaman_temp = $halamans;
		 		foreach($halaman_temp as $ht)
		 		{
		 			if(substr_count($perm, get_page_link($ht->ID)) AND $ht->post_parent == 0)
		 			{	
		 				$background_image = 'page_menu_bg_' . get_post_meta($ht->ID, 'Nomor Identitas', true);
		 			}
		 		}
		 		$filler1 = '<div style="height: 10px;">&nbsp;</div>';
		 		$filler2 = '<div style="height: 3px;">&nbsp;</div>';
		 		$filler = '';
		 		$signum = 0;
		 		//echo $background_image;
		 		//print_r($halaman);
		 		foreach ($halaman as $hlm)
		 		{
		 			$signum = 0;
		 			if ($hlm->post_parent == $post->ID)
		 			{
		 				foreach($halaman as $hlm2)
						{
							if ($hlm2->post_parent == $hlm->ID)
							{
								$signum = 1;	
							}
						}
						if ($signum)
						{
							$signum = 0;
						}
		 				else if (get_post_meta($hlm->ID, 'Deskripsi'))
		 				{
		 					$filler = $filler2;
		 				}
		 				else
		 				{
		 					$filler = $filler1;
		 				}
		 				echo '<div class = "pageblock ' . $background_image . ' halaman-links" style = "color: #CC3300; text-decoration: none; vertical-align: center;" onclick = "window.location.href = \'' . esc_url(get_page_link($hlm->ID)) . '\'">' . $filler . '<a class = "link-maroon" href = "' . esc_url(get_page_link($hlm->ID)) . '">' . $hlm->post_title . '</a>';
		 				$bg_image = "";
		 				$filler = '';
		 				if (get_post_meta($hlm->ID, 'Deskripsi'))
		 				{
		 					echo '<br /><menu-explanation-2>' . get_post_meta($hlm->ID, 'Deskripsi', true) . '</menu-explanation-2>';
		 				}
		 				$this->get_children($hlm->ID, $halaman);
		 				echo '</div>';
		 			}
		 		}
		 	?> 
		 	</div>
		<?php
			/* After widget (defined by themes). */
			echo $after_widget;
			echo '&nbsp;';
		}
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
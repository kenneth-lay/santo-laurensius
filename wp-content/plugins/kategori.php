<?php
/**
 * Plugin Name: Kategori
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_daftar_kategori' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_daftar_kategori() 
{
	register_widget('Daftar_Kategori');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Daftar_Kategori extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Daftar_Kategori() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'daftar-kategori', 
		'description' => 'Panel navigasi yang merincikan kategori');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'daftar-kategori' );

		/* Create the widget. */
		$this->WP_Widget('daftar-kategori', 'Daftar Kategori', $widget_ops, $control_ops);
	}
	
	function get_children($parent_id, $iterator, &$categories)
	{
		$addition = '';
		$addition2 = '';
		for ($i = 0; $i < $iterator; $i = $i + 1)
		{
			$addition = $addition . '&#8212;';
			$addition2 = $addition2 . '<span style="color: #FFFFCC;">&#8212;</span>';
		}
		foreach($categories as $cat)
		{
			if ($cat->parent == $parent_id)
			{
				echo '<a class = "link-maroon listblock" style = "color: #CC3300; text-decoration: none;" href = "' . esc_url(get_category_link($cat->cat_ID)) . '">' . $addition . ' ' . $cat->name . ' (' . $cat->count . ')';
				if ($cat->category_description)
		 		{
		 			echo '<br /><menu-explanation-2>' . $addition2 . ' ' . $cat->category_description . '</menu-explanation-2>';
		 		}
		 		echo '</a>';
				$this->get_children($cat->cat_ID, $iterator + 1, $categories);
			}		
		}
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
				<div class="coltitle">Daftar Kategori</div>
				<div class="span-6 last fill-cream">
			<?php
				//echo esc_url($after_title);
			?>	
			<?php 
				$args = array
				(
					'type'                     => 'post',
					'child_of'                 => 0,
					'parent'                   => '',
					'orderby'                  => 'name',
					'order'                    => 'ASC',
					'hide_empty'               => 0,
					'hierarchical'             => 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
					'taxonomy'                 => 'category',
					'pad_counts'               => true 
				);
		 		$categories = get_categories($args); 
		 		$i = 0;
		 		//print_r($categories);
		 		foreach ($categories as $cat)
		 		{
		 			if ($cat->parent == 0)
		 			{
		 				echo '<a class = "listblock kategori-links" style = "color: #CC3300; text-decoration: none;" href = "' . esc_url(get_category_link($cat->cat_ID)) . '">' . $cat->name . ' [' . $cat->count . ']';
		 				if ($cat->category_description)
		 				{
		 					echo '<br /><menu-explanation-2>' . $cat->category_description . '</menu-explanation-2>';
		 				}
		 				echo '</a>';
		 				$this->get_children($cat->cat_ID, 1, $categories);
		 			}
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
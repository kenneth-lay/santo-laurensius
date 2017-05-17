<?php
/**
 * Plugin Name: Semua Tags
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widget_semua_tags' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_semua_tags() 
{
	register_widget('Semua_Tags');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Semua_Tags extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Semua_Tags() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'semua-tags', 
		'description' => 'Daftar semua tags');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'semua-tags' );

		/* Create the widget. */
		$this->WP_Widget('semua-tags', 'Daftar Semua Tags', $widget_ops, $control_ops);
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
				<div class="coltitle">Semua Kata Kunci</div>
				<div class="span-6 last fill-cream borderbottom">
					<?php
				//echo esc_url($after_title);
						$args = array
						(
    						'smallest'                  => 9, 
    						'largest'                   => 18,
    						'unit'                      => 'pt', 
    						'number'                    => 45,  
    						'format'                    => 'flat',
    						'separator'                 => " ",
    						'orderby'                   => 'name', 
    						'order'                     => 'ASC',
    						'exclude'                   => null, 
   							'include'                   => null, 
    						'topic_count_text_callback' => default_topic_count_text,
    						'link'                      => 'view', 
   							'taxonomy'                  => 'post_tag', 
    						'echo'                      => true 
    					);
    				?>
    				<div class = "span-5 box-small-3">
    				<?php
    					wp_tag_cloud($args);
    				?>
    				</div>
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
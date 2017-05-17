<?php
/**
 * Plugin Name: Pengumuman
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */

add_action( 'widgets_init', 'load_widget_pengumuman' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function load_widget_pengumuman() 
{
	register_widget('Pengumuman');
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Pengumuman extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Pengumuman() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'pengumuman', 
		'description' => 'Panel untuk pengumuman');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'pengumuman' );

		/* Create the widget. */
		$this->WP_Widget('pengumuman', 'Pengumuman', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );
		$judul1 = $instance['judul1'];
		$konten1 = $instance['konten1'];
		$judul2 = $instance['judul2'];
		$konten2 = $instance['konten2'];
		$judul3 = $instance['judul3'];
		$konten3 = $instance['konten3'];
		$juduls = array($judul1, $judul2, $judul3);
		$kontens = array($konten1, $konten2, $konten3);
		echo $before_widget;
		?>	
		<?php if ($judul1 != '' OR $konten1 != '')
		{
		?>
			<div class = "span-6 last notifikasi-panel">
				<h4 class = "art-title">Pengumuman:</h4>
				<?php 
					for ($i = 1; $i <= 3; $i = $i + 1)
					{
				?>
				<?php if ($juduls[$i-1] OR $kontens[$i-1])
				{
				?>
				<div class = "pengumuman-entry">
					<?php if ($juduls[$i-1])
					{
					?>
						<h4 class = "art-subtitle"><?php echo $i . '. ' . $juduls[$i-1]; ?></h4>
					<?php
					}
					if ($kontens[$i-1])
					{
					?>
					<div style = "padding-left: 17px;"><?php echo nl2br($kontens[$i-1]); ?></div>
					<?php
					}
					?>
				</div>
				<?php
				}
				?>
				<?php
					}
				?>
			</div>
		<?php
		}
		?>
		<?php if ($juduls[0] OR $kontens[0])
				{
		echo $after_widget;
		?>
		&nbsp;
		<?php
		}
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['judul1'] = $new_instance['judul1'];
		$instance['konten1'] = $new_instance['konten1'];
		$instance['judul2'] = $new_instance['judul2'];
		$instance['konten2'] = $new_instance['konten2'];
		$instance['judul3'] = $new_instance['judul3'];
		$instance['konten3'] = $new_instance['konten3'];
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
	?>
		<p>
			<label for="<?php echo $this->get_field_id('judul1'); ?>">Judul Pengumuman 1:</label>
			<input id="<?php echo $this->get_field_id('judul1'); ?>" name="<?php echo $this->get_field_name('judul1'); ?>" value="<?php echo $instance['judul1']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('konten1'); ?>">Isi Pengumuman 1:</label>
			<textarea id="<?php echo $this->get_field_id('konten1'); ?>" name="<?php echo $this->get_field_name('konten1'); ?>"><?php echo $instance['konten1']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('judul2'); ?>">Judul Pengumuman 2:</label>
			<input id="<?php echo $this->get_field_id('judul2'); ?>" name="<?php echo $this->get_field_name('judul2'); ?>" value="<?php echo $instance['judul2']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('konten2'); ?>">Isi Pengumuman 2:</label>
			<textarea id="<?php echo $this->get_field_id('konten2'); ?>" name="<?php echo $this->get_field_name('konten2'); ?>"><?php echo $instance['konten2']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('judul3'); ?>">Judul Pengumuman 3:</label>
			<input id="<?php echo $this->get_field_id('judul3'); ?>" name="<?php echo $this->get_field_name('judul3'); ?>" value="<?php echo $instance['judul3']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('konten3'); ?>">Isi Pengumuman 3:</label>
			<textarea id="<?php echo $this->get_field_id('konten3'); ?>" name="<?php echo $this->get_field_name('konten3'); ?>"><?php echo $instance['konten3']; ?></textarea>
		</p>
<?php
	}
}

?>
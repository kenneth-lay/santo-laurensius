<?php
/**
 * Plugin Name: Formulir Kontak
 * Plugin URI: http://www.santo-laurensius.org/
 *
 */
 
require_once ABSPATH . WPINC . '/class-phpmailer.php';
require_once ABSPATH . WPINC . '/class-smtp.php';

function instal_formulir_kontak()
{
    global $wpdb;
    $table = $wpdb->prefix."setting_kontak";
    $structure = "CREATE TABLE $table (
        host VARCHAR(32) NOT NULL,
        port INT(3) NOT NULL,
        username VARCHAR(32) NOT NULL,
        password VARCHAR(32) NOT NULL
    );";
    $wpdb->query($structure);
    $wpdb->query("INSERT INTO $table VALUES ('smtp.gmail.com', 465, 'salus.amdg@gmail.com', 'rahasia dong')");
} 

add_action('activate_contact-form/contact-form.php', 'instal_formulir_kontak');

function uninstall_formulir_kontak()
{
    global $wpdb;
    $table = $wpdb->prefix."setting_kontak";
    $structure = "DROP TABLE $table;";
    $wpdb->query($structure);
} 

add_action('deactivate_contact-form/contact-form.php', 'uninstall_formulir_kontak');


function kontak_submit()
{
	if (isset($_POST['nama']) && $_POST['nama'] != '')
	{
		$nama = $_POST['nama'];
		$email = $_POST['email'];
		$subjek = $_POST['subjek'];
		$pesan = $_POST['pesan'];
		//$verifikasi = $_POST['email'];
		unset($_POST['nama']);
		unset($_POST['email']);
		unset($_POST['subjek']);
		unset($_POST['pesan']);
		
		$phpmailer = new PHPMailer();
		$phpmailer->SMTPAuth = true;
		$phpmailer->SMTPDebug = 2;
		$phpmailer->SMTPSecure = 'ssl';
		$phpmailer->IsSMTP();
	
	
		global $wpdb;
		
		$query = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."setting_kontak");
		foreach ($query as $row)
		{			
			$phpmailer->Host = $row->host;
			$phpmailer->Port = $row->port;
			$phpmailer->Username = $row->username;
			$phpmailer->Password = $row->password;
			$phpmailer->SetFrom($row->username);
			$phpmailer->AddAddress('unodostresquad@gmail.com');
		}
    	$phpmailer->Subject = "Pesan dari Pengunjung";
    	$phpmailer->Body = $nama . '<br /><br />' . $email . '<br /><br />' . $subjek . '<br /><br />' . $pesan;
		if(!$phpmailer->Send()) 
		{
 			echo "Mailer Error: " . $phpmailer->ErrorInfo . '<br />';
		}
    }
}

add_action('get_header', 'kontak_submit');


function contact_form_menu()
{
    global $wpdb;
    include 'contact-form-admin.php';
}
 
function contact_form_admin_actions()
{
    //add_dashboard_page("Setting Form Kontak", "Setting Form Kontak", "activate_plugins", "setting-form-kontak", "contact_form_menu");
	add_menu_page( "Setting Form Kontak", "Setting Form Kontak", "activate_plugins", "setting-form-kontak", "contact_form_menu", '', 22 );
}
 
add_action('admin_menu', 'contact_form_admin_actions');

////
add_action( 'widgets_init', 'load_widget_formulir_kontak' );

function load_widget_formulir_kontak() 
{
	register_widget('Formulir_Kontak');
}


class Formulir_Kontak extends WP_Widget 
{
	/**
	 * Widget setup.
	 */
	function Formulir_Kontak() 
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'formulir_kontak', 
		'description' => 'Formulir kontak pada kaki situs');

		/* Widget control settings. */
		$control_ops = array('id_base' => 'formulir_kontak' );

		/* Create the widget. */
		$this->WP_Widget('formulir_kontak', 'Formulir Kontak', $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) 
	{
		extract( $args );		
		echo $before_widget;
		?>

		<div class="span-6">
		<?php
			echo $before_title;
		?>
		Hubungi kami:
		<?php
			echo $after_title;
		?>
			<form action="<?php echo home_url('/'); ?>" method="post">
				<div class="span-2 align-right"><strong>Nama: </strong></div>
				<div class="span-4 last"><input type="text" name="nama" /></div>
				<div class="span-2 align-right"><strong>e-mail: </strong></div>
				<div class="span-4 last"><input type="text" name="email" /></div>
				<div class="span-2 align-right"></strong>Subjek: </strong></div>
				<div class="span-4 last">
					<select name="subjek">
						<option value="Umum" selected="selected">Umum</option>
						<option value="Komunitas">Komunitas</option>
						<option value="Liturgi">Liturgi</option>
						<option value="Situs Web">Situs Web</option>
						<option value="Lain-lain">Lain-lain</option>
					</select>
				</div>
				<div class="span-2 align-right"><strong>Pesan: </strong></div>
				<div class="span-4 last"><textarea name="pesan"></textarea></div>
				<div class="span-2 align-right"><strong>Verifikasi: </strong></div>
				<div class="span-2"><?php echo "<img src=\"" . get_template_directory_uri() . "/images/footer/captcha.php\" />";?></div>
				<div class="span-2 last" style = "width: 68px;"><input type="text" name="verifikasi" /></div>
				<div class="span-6 last align-right">
					<input type="submit" value="Kirim Pesan" />
				</div>
			</form>
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
		$instance = wp_parse_args((array)$instance, $defaults ); ?>
	<?php
	}
}

?>
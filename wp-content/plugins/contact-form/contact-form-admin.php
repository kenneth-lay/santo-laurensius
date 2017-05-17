<?php
if (isset($_POST["changed"]) AND $_POST["changed"] != "")
{
	unset($_POST["changed"]);
	$table = $wpdb->prefix . "setting_kontak";
	$structure = "UPDATE $table SET host = '" . $_POST["host"] . "', port = " . $_POST['port'] . ", username = '" . $_POST['username'] . "', password = '" . $_POST['password'] . "'" ;
	$wpdb->query($structure);
	unset($_POST["host"]);
	unset($_POST["port"]);
	unset($_POST["username"]);
	unset($_POST["password"]);
?>
	<div id="message" class="updated fade">
		Pengubahan <em>setting</em> formulir kontak <strong>berhasil</strong>.
	</div>
<?php
}
	$query = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."setting_kontak");
	$host = '';
	$port = '';
	$username = '';
	$password = '';
	foreach ($query as $row)
	{
		$host = $row->host;
		$port = $row->port;
		$username = $row->username;
		$password = $row->password;
	}
?>

<div class="wrap">
	<h2>Pengaturan Formulir Kontak</h2>
	<form action="?page=<?php echo $_GET['page']; ?>" method="post">
		<p>
			Host: <input type="text" name="host" value = "<?php print $host; ?>" />
		</p>
		<p>
			Port: <input type="text" name="port" value = "<?php print $port; ?>" />
		</p>
		<p>
			Username: <input type="text" name="username" value = "<?php print $username; ?>" />
		</p>
		<p>
			Password: <input type="password" name="password" value = "<?php print $password; ?>" />
		</p>
		<input type="hidden" name="changed" value="yeah" />
		<p>
			<input type="submit" value="Perbaharui" />
		</p>
	</form>
</div>
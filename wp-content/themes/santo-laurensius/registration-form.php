<?php
/**
 * Template Name: Registration Form
 */
session_start();
if (!is_user_logged_in())
{
get_header();
$captcha_instance = new ReallySimpleCaptcha();
if (isset($_SESSION['imgprefix']) AND $_SESSION['imgprefix'] != '')
{
	$captcha_instance->remove($_SESSION['imgprefix']);
	unset($_SESSION['imgprefix']);
}
global $post;
?>

<script type = "text/javascript">
	$(document).ready(function(){
    	if ($("#katolik_ya").attr("checked") == "checked")
    	{
    		$("#pernyataan-iman").show();
    		$("#vfiller").show();
    		$("#amin").show();
    		$("#amin_check").attr('checked', false);
    	}
    	else
    	{
    		$("#pernyataan-iman").hide();
    		$("#vfiller").hide();
    		$("#amin").hide();
    		$("#amin_check").attr('checked', false);
    	}
  		$("#katolik_ya").click(function(){
    		$("#pernyataan-iman").show();
    		$("#vfiller").show();
    		$("#amin").show();
  		});
  		$("#katolik_bukan").click(function(){
    		$("#pernyataan-iman").hide();
    		$("#vfiller").hide();
    		$("#amin").hide();
    		$("#amin_check").attr('checked', false);
  		});
	});
</script>
<?php
	function get_post_parents($prnt, &$halamans, &$printout, &$counter)
	{
		foreach ($halamans as $halaman) 
		{
  			if ($prnt == $halaman->ID)
  			{
  				$counter = $counter + 1;
  				$printout = '<a href = "' . get_page_link($halaman->ID) . '" class = "span-5 last breadcrumb-instance" style = "replaceable">' . $halaman->post_title . '</a><div class = "span-1 last panah">></div>' . $printout;
  				get_post_parents($halaman->post_parent, $halamans, $printout, $counter);
  			}
  		}
  	}
?>
<br />
<div class = "container main-content">
	<div class="span-17 span-17-680 main-left">
		<div class = "prepend-small">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div class = "fill-cream border-bottom-top" style = "margin-bottom: 20px; height: auto;">
				<div class = "container">
					<?php
						global $post;
						$printout = '';
//						print_r($halamans);
						$counter = 1;
						$width = 190;
						get_post_parents($post->post_parent, $halamans, $printout, $counter);
						$printout = $printout . '<a class = "span-5 last breadcrumb-location" style = "replaceable">' . $post->post_title . '</a>';
						if ($counter > 2)
						{
							$width = 660 - ($counter - 1) * 20 - $counter * 10;
							$width = ceil($width / $counter);
							echo str_ireplace("replaceable", "width: " . $width . "px;", $printout);
							//echo '<br /><br />' . $width;
						}
						else
						{
							echo str_ireplace("replacable", "", $printout);
						}
						$counter = 0;
					?>
				</div>
			</div>
			<h2 class = "art-title">
				<?php if ( is_front_page() ) { ?>
				<?php the_title(); ?>
				<?php } else { ?>	
					<?php the_title(); ?>
				<?php } ?>	
			</h2>
			<div class = "prepend-top-negative-small">&nbsp;</div>
			<div class = "registration-form single-column">
				<div style = "font-size: 13px;">
					<?php the_content(); ?>
				</div>
				Bagian dengan&nbsp; <div class = "sample-bordir">bordir merah</div> di sebelah kiri kotak pengisian menandakan bahwa kotak tersebut wajib diisi/dijawab oleh setiap calon anggota.
				<div class = "prepend-top-negative">&nbsp;</div>
				<?php
					require_once(ABSPATH . WPINC . '/registration.php');  
				?>
				<?php
					$previous_username = '';
					if (isset($_SESSION['previous_username']) AND $_SESSION['previous_username'] != '')
					{
						$previous_username = $_SESSION['previous_username'];
						unset($_SESSION['previous_username']);
					}
					if (isset($_SESSION['previous_email']) AND $_SESSION['previous_email'] != '')
					{
						$previous_email = $_SESSION['previous_email'];
						unset($_SESSION['previous_email']);
					}
					if (isset($_SESSION['previous_nama_depan']) AND $_SESSION['previous_nama_depan'] != '')
					{
						$previous_nama_depan = $_SESSION['previous_nama_depan'];
						unset($_SESSION['previous_nama_depan']);
					}
					if (isset($_SESSION['previous_nama_belakang']) AND $_SESSION['previous_nama_belakang'] != '')
					{
						$previous_nama_belakang = $_SESSION['previous_nama_belakang'];
						unset($_SESSION['previous_nama_belakang']);
					}
					$cek01 = '';
					$cek02 = '';
					if (isset($_SESSION['previous_katolik']) AND $_SESSION['previous_katolik'] != '')
					{
						$previous_katolik = $_SESSION['previous_katolik'];
						unset($_SESSION['previous_katolik']);
						if ($previous_katolik == 'ya')
						{
							$cek01 = 'checked = "checked"';
						}
						else
						{
							$cek02 = 'checked = "checked"';
						}
					}
				?>
				<form action = "<?php echo site_url() . '/kirim-pendaftaran/' ?>" method="post">
					<div class = "registration-form-field">
						<label>Nama Akun / <em>Username</em></label>
						Nama akun dibutuhkan untuk <em>log-in</em> dan akses fitur-fitur dari situs web ini. Hanya boleh menggunakan
						<strong>a-z</strong>, <strong>A-Z</strong>, <strong>0-9</strong>, [<strong>garis bawah</strong> / _ ], [<strong>titik</strong> / . ], dan [<strong>strip</strong> / - ]. Tidak boleh melebihi 32 karakter dan minimal 6 karakter.
						<input type="text" name="username" class="text" value="<?php echo $previous_username; ?>" /><br />
						<span class = "error-msg">
							<?php
								if (isset($_SESSION['username_empty']) AND $_SESSION['username_empty'] != '')
								{
									echo '&rarr; ' . $_SESSION['username_empty'] . '<br />';
									unset($_SESSION['username_empty']);
								}
								if (isset($_SESSION['username_format']) AND $_SESSION['username_format'] != '')
								{
									echo '&rarr; ' . $_SESSION['username_format'] . '<br />';
									unset($_SESSION['username_format']);
								}
							?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Alamat e-mail</label>
						<input type="text" name="email" class="text" value="<?php echo $previous_email; ?>" /> <br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['email_empty']) AND $_SESSION['email_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['email_empty'] . '<br />';
								unset($_SESSION['email_empty']);
							}
							if (isset($_SESSION['email_format']) AND $_SESSION['email_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['email_format'] . '<br />';
								unset($_SESSION['email_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
					<label>Kode Sandi / <em>Password</em></label>
						Kode sandi juga dibutuhkan untuk <em>log-in</em>. Tidak boleh melebihi 32 karakter, minimal 6 karakter, dan hanya terdiri
			dari <strong>garis bawah</strong>/<em>underscore</em> ( _ ), <strong>strip</strong> / <em>dash</em> ( - ), <strong>angka</strong>, <strong>huruf besar</strong>, serta <strong>huruf kecil</strong>.
						<input type="password" name="password" class="text" value="" /> <br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['password_empty']) AND $_SESSION['password_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['password_empty'] . '<br />';
								unset($_SESSION['password_empty']);
							}
							if (isset($_SESSION['password_format']) AND $_SESSION['password_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['password_format'] . '<br />';
								unset($_SESSION['password_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Kode Sandi <span style = "font-weight: normal;">(Konfirmasi)</span></label>
						Mohon tulis ulang kode sandi pada kotak di bawah ini sama persis dengan yang telah anda tulis di kotak sebelumnya. Hal ini
						bertujuan untuk memastikan bahwa Anda tidak melakukan kesalahan pengetikan.
						<input type="password" name="password_konfirmasi" class="text" value="" /> <br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['password_konfirmasi_empty']) AND $_SESSION['password_konfirmasi_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['password_konfirmasi_empty'] . '<br />';
								unset($_SESSION['password_konfirmasi_empty']);
							}
							if (isset($_SESSION['password_konfirmasi_gagal']) AND $_SESSION['password_konfirmasi_gagal'] != '')
							{
								echo '&rarr; ' . $_SESSION['password_konfirmasi_gagal'] . '<br />';
								unset($_SESSION['password_konfirmasi_gagal']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Nama Depan</label>
						<input type="text" name="nama_depan" class="text" value="<?php echo $previous_nama_depan; ?>" /> <br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nama_depan_empty']) AND $_SESSION['nama_depan_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_depan_empty'] . '<br />';
								unset($_SESSION['nama_depan_empty']);
							}
							if (isset($_SESSION['nama_depan_format']) AND $_SESSION['nama_depan_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_depan_format'] . '<br />';
								unset($_SESSION['nama_depan_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field not-required-field">
						<label>Nama Belakang</label>
						<input type="text" name="nama_belakang" class="text" value="<?php echo $previous_nama_belakang; ?>" /> <br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['nama_belakang_format']) AND $_SESSION['nama_belakang_format'] != '')
							{
								echo '&rarr; ' . $_SESSION['nama_belakang_format'] . '<br />';
								unset($_SESSION['nama_belakang_format']);
							}
						?>
						</span>
					</div>
					<div class = "registration-form-field">
						<label>Apakah Anda Katolik?</label>
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<input type="radio" name="katolik" id = "katolik_ya" value="ya" <?php echo $cek01; ?> /><span> Ya</span><br />
						<input type="radio" name="katolik" id = "katolik_bukan" value="bukan" <?php echo $cek02; ?> /><span> Tidak</span><br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['katolik_empty']) AND $_SESSION['katolik_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['katolik_empty'] . '<br />';
								unset($_SESSION['katolik_empty']);
							}
						?>
						</span>
					</div>
					<?php
						// Change the background color of CAPTCHA image to black
						$captcha_instance->bg = array( 204, 51, 0 );
						$word = $captcha_instance->generate_random_word();
						$prefix = mt_rand();
						$_SESSION['prefixvar'] = $prefix;
						$_SESSION['imgprefix'] = $prefix;
					?>
					<div class = "registration-form-field">
						<label>Anti-Otomatisasi Pendaftaran</label>
						Mohon ketik ulang tulisan yang anda lihat pada gambar pada samping kotak di bawah ini, agar kami 
						dapat memastikan bahwa pendaftaran anda tidak diotomatisasi oleh <em>bot</em>.:
						<div class = "prepend-top-negative-small">&nbsp;</div>
						<div style = "display: inline-block; padding-top: 5px;">
							<image src = "<?php echo bloginfo('wpurl') . '/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image( $prefix, $word ); ?>" />
						</div>
						<div class = "span-8" style = "margin-top: -5px; margin-right: 30px;">
							<input type="text" name="captcha_test" class="text" />
						</div>
						<div class = "prepend-top-negative-large">&nbsp;</div>
						<br />
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['captcha_empty']) AND $_SESSION['captcha_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['captcha_empty'] . '<br />';
								unset($_SESSION['captcha_empty']);
							}
							if (isset($_SESSION['captcha_false']) AND $_SESSION['captcha_false'] != '')
							{
								echo '&rarr; ' . $_SESSION['captcha_false'] . '<br />';
								unset($_SESSION['captcha_false']);
							}
						?>
						</span>
					</div>
					<br />
					<?php pernyataan_iman(); ?>
					<div id = "vfiller" class = "span-17" style = "display: none;">&nbsp;</div>
					<div class = "registration">
						<input type="submit" id="submitbtn" name="submit" value="Daftar" />
					</div>
				</form>
			</div>
		</div>
		<br />
		<br />
	</div>
	<div class = "span-6 main-right last">
		<?php get_sidebar(); ?>
	</div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
<?php
	}
	else
	{
		wp_redirect(site_url());
		exit();
	}
?>

<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="UTF-8" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 * We filter the output of wp_title() a bit -- see
	 * twentyten_filter_wp_title() in functions.php.
	 */
	wp_title( '|', true, 'right' );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--
<script type = "text/javascript" src = "<?php echo bloginfo('template_directory') . '/js/jquery-1.8.0.min.js'; ?>"></script>
-->
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
<?php
	$sduri = get_stylesheet_directory_uri() . '/';
	$slideruri = $sduri . 'jcobb-bjqs/';
?>
	<?php if (get_my_url() == site_url('/') OR get_my_url() == site_url() OR get_my_url() == site_url('/verifikasi') OR get_my_url() == site_url('/verifikasi/') OR get_my_url() == site_url('/pendaftaran') OR get_my_url() == site_url('/pendaftaran/'))
	{
	?>
		<!-- Include the jQuery library (local or CDN) -->
		<script src="<?php echo $slideruri; ?>js/libs/jquery-1.6.2.min.js"></script>
	<?php
	}
	?>

	<!-- Include the plugin *after* the jQuery library -->
	<script src="<?php echo $slideruri; ?>js/basic-jquery-slider.js"></script>

	<!-- Include the basic styles -->
	<link type="text/css" rel="Stylesheet" href="<?php echo $slideruri; ?>css/basic-jquery-slider.css" />
	<link href='http://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>
</head>

<body <?php body_class(); ?>>
	<?php global $halamans; ?>
	<div id="header">
		<div class="container">
			<div class="span-11">
				<!-- LOGO -->
				<?php 
					echo '<a href = "' . home_url() . '"><img src="' . get_stylesheet_directory_uri() . '/images/header/logo.png" id="logo" /></a>'; 
				?>
			</div>
			<div class="span-13 last align-right prepend-top-medium" style="padding-top: 5px;">
				<?php
					if (is_user_logged_in())
					{
						$user = wp_get_current_user();
						if ($user->first_name != '')
						{
							$user_identity = $user->first_name;
						}
						else
						{
							$user_identity = $user->display_name;
						}
						if ($user->last_name != '')
						{
							$user_identity = $user_identity . ' ' . $user->last_name;
						}
				?>
					Halo, <?php echo '<a class = "link-maroon" href="' . admin_url('profile.php') . '"><strong>' . $user_identity . '</strong></a>'; ?>
				<strong> | 
				<a class = "link-maroon" href = "<?php echo get_admin_url(); ?>">Panel Kontrol</a> | 
				<?php
					global $id;
					if ( null === $post_id )
						$post_id = null;
					else
						$id = $post_id;
					if (!is_home())
					{
						echo '<a class = "link-maroon" href="' . wp_logout_url( get_my_url() ) . '" title="Logout">Log-out</a>';
					}
					else
					{
						echo '<a class = "link-maroon" href="' . wp_logout_url(home_url()) . '" title="Logout">Log-out</a>';
					}
				?>
				</strong>
				<?php
					}
					else
					{
				?>	
					<form action = "<?php echo get_option('home'); ?>/wp-login.php" method = "post">
						Username: <input type="text" class = "tb-small" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" style = "width: 120px;" />&nbsp;&nbsp;&nbsp;
						Password: <input type="password" class = "tb-small" name="pwd" id="pwd" style = "width: 120px;" />&nbsp;
						<input type="submit" class = "small-submit" name="submit" value="Log-in" />
						<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					</form>
				<?php
					}
				?>
			</div>
		</div>
		<div class="container">
			<div class="span-23 last" id="page-title">
				<?php bloginfo( 'description' ); ?>
			</div>
		</div>
	</div>
	<?php
		/* DEBUG: SCROLLBAR ADDER
		for ($i = 0; $i < 100; $i = $i + 1)
		{
			echo 'a<br />';
		}
		*/
	?>
	<!--
		<h1>
			<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		</h1>
	-->
	<?php
		$unlink = FALSE;
		$perm = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$halaman_temp = $halamans;
		foreach($halaman_temp as $ht)
		{
		 	if(substr_count($perm, get_page_link($ht->ID)) AND $ht->post_parent == 0)
		 	{	
		 		$unlink = $ht->ID;
		 	}
		 }
	?>
	<div id="access" role="navigation">
	  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
		<!--
			<a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a>
		-->
		<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
		<div class="container">
			<div class="span-4">
				<?php
					global $wp;
					$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );	
					if ($current_url == home_url('/') OR $current_url == home_url(''))
					{
						echo '<a class="main-menu-item">Halaman Utama <menu-explanation>Sekilas pandang situs web Santo Laurensius</menu-explanation></a>';
					}
					else 
					{
						echo '<a href = "' . home_url('/') . '" class="main-menu-item link">Halaman Utama <menu-explanation>Sekilas pandang situs web Santo Laurensius</menu-explanation></a>';
					}
				?>
			</div>
				<?php
					$i = 1;
					$count_halaman = 0;
					//print_r ($halamans);

					foreach ($halamans as $page)
					{
						if ($page->post_parent == 0)
						{
							$count_halaman = $count_halaman + 1;
						}
					}
					foreach($halamans as $page) 
					{
						if ($i < 6 AND get_post_meta($page->ID, 'Deskripsi', true) AND get_post_meta($page->ID, 'kunci', true) == '~~7')
						{
				?>
					<?php
						if ($page->post_parent == 0)
						{
							if ($i < 5)
							{
					?>
								<div class="span-4">	
					<?php
							}
							else
							{
					?>
								<div class="span-4 last">
					<?php
							}
							if ($unlink == $page->ID OR is_page($page->ID))
							{
								echo '<a class="main-menu-item">';
								$unlink = FALSE;
							}
							else 
							{
								echo '<a href = "' . get_permalink($page->ID) . '" class="main-menu-item link">';
							}
							echo $page->post_title . ' ';
					?>
							<menu-explanation>
						<?php
								echo get_post_meta($page->ID, 'Deskripsi', true);
						?>
							</menu-explanation>
							</a>
							</div>
				<?php
							$i = $i + 1;
						}
					?>
					<?php
						}
					}
				?>
			</div>
		</div>
	</div><!-- #access -->
</div>
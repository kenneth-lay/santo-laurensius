<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to twentyten_comment which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<?php if ( post_password_required() ) : ?>
				<p><?php _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ); ?></p>
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<!-- STARKERS NOTE: The following h3 id is left intact so that comments can be referenced on the page -->
<a name = "comments" style: "color: inherit; text-decoration: none;">
<div id="comments-title" class = "span-17 last span-18_second-column colsubtitle" style = "width: 680px;">
				<?php
					if (get_post_type($post->ID) != 'kalender-acara')
					{
						printf( _n( 'Ada satu komentar untuk artikel ini:', 'Ada %1$s komentar untuk artikel ini:', get_comments_number(), 'twentyten' ),
						number_format_i18n( get_comments_number() ), '' . get_the_title() . '' );
					}
					else
					{
						printf( _n( 'Ada satu komentar untuk acara ini:', 'Ada %1$s komentar untuk acara ini:', get_comments_number(), 'twentyten' ),
						number_format_i18n( get_comments_number() ), '' . get_the_title() . '' );
					}
				?>
</div>
</a>
<br />
<div id = "comment-list" class = "span-17 last span-18_second-column last fill-yellow" style = "width: 680px;">
	<ol>
		<?php
			/* Loop through and list the comments. Tell wp_list_comments()
			 * to use twentyten_comment() to format the comments.
			 * If you want to overload this in a child theme then you can
			 * define twentyten_comment() and that will be used instead.
			 * See twentyten_comment() in twentyten/functions.php for more.
			 */
				wp_list_comments( array( 'callback' => 'twentyten_comment' ) );
		?>
	</ol>
</div>
<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<?php
					$noprev = FALSE;
					$pag = get_query_var('cpage');
					$prevpage = intval($pag) - 1;
					if ( intval($pag) <= 1 )
						$noprev = TRUE;
					$prev_link = "window.location.href = '" . esc_url(get_comments_pagenum_link($prevpage)) . "'";
					$nonext = FALSE;
					$nextpage = intval($pag) + 1;
					$max_page = 0;
					if ( empty($max_page) )
						$max_page = $wp_query->max_num_comment_pages;

					if ( empty($max_page) )
						$max_page = get_comment_pages_count();
						
					if ( $nextpage > $max_page )
						$nonext = TRUE;
					$next_link = "window.location.href = '" . esc_url( get_comments_pagenum_link( $nextpage, $max_page ) ) . "'";
				?>
<div class = "previous-next span-17 last span-18_second-column fill-grey-light" style= "width: 690px; border-bottom: 1px dashed grey;">
					<?php
						if (!$noprev)
						{
					?>	
	<div class = "span-8 box-small hover-grey-light" onclick = "<?php echo $prev_link; ?>" style = "width: 325px; padding-right: 10px;">
								<?php previous_comments_link( __( '&larr; Komentar lebih awal', 'twentyten' ) ); ?>
	</div>
					<?php
						}
						else
						{
					?>
	<div class = "span-8 box-small" style = "width: 332px; padding-right: 10px;">
								&nbsp;
	</div>
					<?php	
						}
						if (!$nonext)
						{
					?>	
	<div class = "span-9 box-small hover-grey-light" onclick = "<?php echo $next_link; ?>" style = "text-align: right; width: 325px; padding-right: 10px;">
							<?php next_comments_link( __( 'Komentar lebih baru &rarr;', 'twentyten' ) ); ?>
	</div>
					<?php
						}
					?>
</div>
<?php endif; // check for comment navigation ?>
<div style = "margin: 0px 0px 0px 0px;">&nbsp;</div>
<?php else : // or, if we don't have comments:

	/* If there are no comments and comments are closed,
	 * let's leave a little note, shall we?
	 */
	if ( ! comments_open() ) :
?>
	<p><?php _e( 'Comments are closed.', 'twentyten' ); ?></p>
	
<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>

<?php
	if (get_option('comment_registration') && !is_user_logged_in())
	{
		if (!(current_user_can('level_0')))
		{
?>
<div class = "box-small">
	<a name = "komentar-login" style = "text-decoration: none;">
		<h2 class="art-title size-medium" style="border-bottom: 1px dashed #CC3300;">Anda harus <em>log-in</em> sebelum memberikan komentar:</h2>
	</a>
	<br />
	<form action="<?php echo wp_login_url( get_permalink() ) . '#form-komentar'; ?>" method="post">
		<span class = "fore-reddish-orange size-regular"><strong>Username: </strong></span><br />
		<input type="text" class = "regular" style="width: 450px;" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="20" />
		<br />
		<span class = "fore-reddish-orange size-regular"><strong>Password: </strong></span><br />
		<input type="password" class = "regular short" style="width: 450px;" name="pwd" id="pwd" size="20" />
		<br />
		<p>
			<br />
       		<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /><span class = "fore-reddish-orange size-regular"> Ingatlah saya!</span></label>
       		<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
    	</p>
		<input type="submit" name="submit" value="Log-in" class="button medium" />&nbsp; &nbsp;
		<a href="<?php echo get_option('home'); ?>/wp-login.php?action=lostpassword">Lupa kode sandi?</a>
	</form>
</div>
<?php 
		} 
		else 
		{ 
?>
			<h2>Logout</h2>
			<a href="<?php echo wp_logout_url(urlencode($_SERVER['REQUEST_URI'])); ?>">logout</a><br />
			<a href="http://XXX/wp-admin/">admin</a>
<?php 
		}
	}
	else
	{
?>
	<br />
<div class = "span-17 last span-18_second-column last" style = "width: 680px;">
	<a name="form-komentar" style = "text-decoration: none; color: inherit;">
<?php
		comment_form();
?>
	</a>
</div>
<?php
	}
?>
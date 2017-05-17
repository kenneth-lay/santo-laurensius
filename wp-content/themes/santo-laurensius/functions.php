<?php
/**
 * TwentyTen functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyten_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 675 );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'twentyten_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/starkers.png',
			'thumbnail_url' => '%s/images/headers/starkers-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'Starkers', 'twentyten' )
		)
	) );
}
endif;

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since Twenty Ten 1.0
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since Twenty Ten 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function twentyten_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'twentyten' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'display' );

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'twentyten_filter_wp_title', 10, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<div id = "comment-instance" class ="span-17 span-18_second-column fill-cream box-small" style = "width: 670px;">
			<div id="comment-<?php comment_ID(); ?>">
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<div class = "span-2">
				<div class = "prepend-top-negative-small">
					&nbsp;
				</div>
				<?php echo get_avatar( $comment, 64 ); ?>
			</div>
		<div class = "span-15 last box-small-2" style = "width: 580px;">
			<?php
				$nama = '';
				if ($comment->user_id) 
				{
					$user=get_userdata($comment->user_id);
					$nama = $user->first_name;
					if ($user->last_name != '')
					{
						$nama = $nama . ' ' . $user->last_name;
					}
					if (comment_author_url() != '')
					{
						$nama = '<a class = "link-maroon" href="' . comment_author_url(); '">' . $nama . '</a>';
					}
				} 
				else 
				{ 
					$nama = get_comment_author_link(); 
				}
			?>
			<?php printf( __( '%s', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', $nama ) ); ?>
		<!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( '<br />Sebelum bisa ditampilkan pada publik, komentar Anda harus menunggu persetujuan Administrator terlebih dahulu.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a>
		</div><!-- .comment-meta .commentmetadata -->

		<?php comment_text(); ?>
		</div>
		</div>
		<!--
			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div>
		-->
		</div><!-- #comment-##  -->
	</div>

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<!--
		<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	-->	
		<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'twentyten' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'twentyten' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'twentyten' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'twentyten' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'twentyten' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'twentyten' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'twentyten_remove_recent_comments_style' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() 
{
	//$prepared_text = '<img src = "' . get_stylesheet_directory_uri() . '/images/post-decor/calicon.jpeg" style = "padding-top: 10px;" />';
	$prepared_text = 'pada tanggal';
	if (get_the_author_meta('last_name') != '')
	{
		$spasi = ' ';
	}
	else
	{
		$spasi = '';
	}
	if (is_archive() || is_search())
	{
		$oleh = 'Ditulis oleh';
	}
	else
	{
		$oleh = 'Ditulis oleh';
	}
	$nomen = '';
	if (get_the_author_meta( 'first_name' ) != '')
	{
		$nomen = get_the_author_meta( 'first_name' );
	}
	else
	{
		$nomen = get_the_author_meta( 'display_name' );
	}
	if (get_the_author_meta( 'last_name' ) != '')
	{
		$nomen = $nomen . ' ' . get_the_author_meta( 'last_name' );
	}
	printf( __( '<span class="%1$s">' . $oleh . '</span> %2$s <span class="meta-sep">' . $prepared_text . '</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<span class="author vcard"><a class="url fn n link-maroon" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), $nomen  ),
			$nomen
		),
		sprintf( '<a href="%1$s" class= "link-maroon" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;


function new_excerpt_more($more) {
       global $post;
	return '<div class = "align-right prepend-top-negative-large"><a class = "link-maroon" href="'. get_permalink($post->ID) . '">Baca keseluruhan -></a></div>';
}

$thn = '';
$bln = '';
$tgl = '';
$penyusun = '';
$tpt = '';
$sttg = '';

	/* Start the Loop.
	 *
	 * In Twenty Ten we use the same loop in multiple contexts.
	 * It is broken into three main parts: when we're displaying
	 * posts that are in the gallery category, when we're displaying
	 * posts in the asides category, and finally all other posts.
	 *
	 * Additionally, we sometimes check for whether we are on an
	 * archive page, a search page, etc., allowing for small differences
	 * in the loop on each template without actually duplicating
	 * the rest of the loop that is shared.
	 *
	 * Without further ado, the loop:
	 */ 
	 
	function myloop($itervalue, $iter)
	{
?>
<?php global $post; ?>
<?php while ( have_posts() ) : the_post();

?>
<?php $ptags = wp_get_post_tags($post->ID); ?>
<?php /* How to display posts in the Gallery category. */ ?>

	<?php if ( in_category( _x('gallery', 'gallery category slug', 'twentyten') ) ) : ?>


<?php /* How to display posts in the asides category */ ?>

	<?php elseif ( in_category( _x('asides', 'asides category slug', 'twentyten') ) ) : ?>

<?php /* How to display all other posts. */ ?>

	<?php else : ?>
	<?php
		if (fmod($iter, 2) == $itervalue)
		{
	?>
			<div class = "excerpt-instance hover-cream fill-white">
				<a class = "excerpt-title" href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				<br />
					<?php if ( count( get_the_category() ) ) : ?>
						<?php printf( __( '<strong>Kategori: <span style = "color: #f86017;">%2$s</span></strong>', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', print_category_hierarchy() ); ?>
					<?php endif; ?>
				<?php
					if ($post->post_type == 'post' OR $post->post_type == 'page')
					{
				?>
						<br />
						<span class = "fore-reddish-orange"><strong><?php twentyten_posted_on(); ?></strong></span>
						<br />
				<?php
					}
					else if ($post->post_type == 'kalender-acara')
					{
						$jam_mulai = '';
						global $bulan_indonesia;
						if (get_post_meta($post->ID, 'jam_mulai', true) != '')
						{
							$jam_mulai = ' / ' . get_post_meta($post->ID, 'jam_mulai', true);
						}
						$jam_selesai = '';
						if (get_post_meta($post->ID, 'jam_selesai', true) != '')
						{
							$jam_selesai = ' / ' . get_post_meta($post->ID, 'jam_selesai', true);
						}
						$author_full_name = get_the_author_meta('first_name');
						if (get_the_author_meta('last_name'))
						{
							$author_full_name = $author_full_name . ' ' . get_the_author_meta('last_name');
						}
						$tanggal_mulai = explode('-', get_post_meta($post->ID, 'tanggal_mulai', true));
						$tanggal_selesai = explode('-', get_post_meta($post->ID, 'tanggal_selesai', true));
						
				?>
						<span class = "fore-reddish-orange"><strong>Mulai: <a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . $tanggal_mulai[2] . '/'; ?>"><?php echo $tanggal_mulai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . $tanggal_mulai[1] . '/' . '">' . $bulan_indonesia[$tanggal_mulai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/') . $tanggal_mulai[0] . '/' . '">' . $tanggal_mulai[0] . '</a>' .  $jam_mulai; ?></strong></span>
						<br />
						<span class = "fore-reddish-orange"><strong>Selesai: <a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . $tanggal_selesai[2] . '/'; ?>"><?php echo $tanggal_selesai[2] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . $tanggal_selesai[1] . '/' . '">' . $bulan_indonesia[$tanggal_selesai[1]] . '</a> <a class = "link-maroon" href = "' . home_url('/kalender-acara/selesai/') . $tanggal_selesai[0] . '/' . '">' . $tanggal_selesai[0] . '</a>' .  $jam_selesai; ?></strong></span>
						<br />
						<span class = "fore-reddish-orange"><strong>Tempat: <a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/tempat/') . urlencode(get_post_meta($post->ID, 'tempat', true)); ?>"><?php echo get_post_meta($post->ID, 'tempat', true) . '</a>'; ?></strong></span>
						<br />
						<span class = "fore-reddish-orange"><strong>Penyusun: <a class = "link-maroon" href = "<?php echo home_url('/kalender-acara/penyusun/') . urlencode(get_the_author_meta('login')); ?>"><?php echo $author_full_name . '</a>'; ?></strong></span>
				<?php
					}
				?>
				<?php //the_excerpt(); ?>
				<!--
				<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) ); ?>
				-->
					<div class = "fill-semi-orange" style = "margin-top: 5px; padding: 2px 5px 2px 5px;">
					<?php
						$link_komentar = '';
						if (get_comments_number($post->ID) == 0)
						{
							if (is_user_logged_in())
							{
								$link_komentar = '#form-komentar';
							}
							else
							{
								$link_komentar = '#komentar-login';
							}
						}
						else
						{
							$link_komentar = '#comments';
						}
						//echo $link_komentar;
					?>
						<a class = "link-extra-maroon" href = "<?php echo get_permalink() . $link_komentar; ?>"><?php comments_number('0 komentar', '1 komentar', '% komentar' ); ?></a>
						<?php
							if (count($ptags))
							{
						?>	
								&nbsp; | &nbsp;Kata kunci:&nbsp;
						<?php
								$k = 0;
								foreach ($ptags as $pt)
								{
									if ($k < count($ptags) - 1)
									{
						?>	
										<a class = "link-extra-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>,
						<?php
									}	
									else
									{
						?>		
										<a class = "link-extra-maroon" href = "<?php echo get_tag_link($pt->term_id); ?>"><?php echo $pt->name; ?></a>
						<?php
									}
									$k = $k + 1;
								}
							}
						?>
					</div>
				</div>
					<?php
						$tags_list = get_the_tag_list( '', ', ' );
						if ( $tags_list ):
					?>
						<?php //printf( __( '<em>Tags</em> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					
					<?php endif; ?>
					<!--
					<?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% komentar', 'twentyten' ) ); ?>
					<?php edit_post_link( __( 'Edit', 'twentyten' ), '| ', '' ); ?>
					-->

			<?php comments_template( '', true ); ?>
	<?php
		}
		else if ($post->post_type == 'dokumen-gereja' OR substr_count(get_my_url(), 'post_type=dokumen-gereja'))
		{
			if ($iter == 0)
			{
				echo '<br />';
				global $wpdb;
				$myurl = get_my_url();
				$myurl_array = explode("/dokumen-gereja/", $myurl);
				$tipe_dokumen = $myurl_array[1];
				$myurl_array = explode("/", $tipe_dokumen);
				$tipe_dokumen = $myurl_array[0];
				$jenis_dokumens = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\" AND post_name = \"" . $tipe_dokumen . "\"");
				foreach ($jenis_dokumens as $jd)
				{
					$iddok = $jd->ID;
					if (get_post_meta ($iddok, 'deskripsi_dan_hak_cipta', true) != '')
					{
						echo '<div class = "document-copyright">' . get_post_meta ($iddok, 'deskripsi_dan_hak_cipta', true) . '</div>';
						echo '<div class = "prepend-top-negative">&nbsp;</div>';
					}
				}
			}
	?>
	<?php
		$bagian = get_post_meta($post->ID, 'bagian', true);
		if ($bagian == '' OR $bagian == 'pasal')
		{
	?>
			<div class = "span-17" style = "width: 680px; margin-bottom: 10px;">
				<div class = "pasal span-1" style = "width: 70px; padding-left: 10px;">
					<a class = "pasal-dokumen"><?php echo get_post_meta($post->ID, 'nomor_pasal', true); ?></a>
				</div>
				<div class = "konten-pasal span-15 last" style = "width: 550px;">
					<?php the_content(); ?>
				</div>
			</div>
				<br />
				<br />
	<?php
		}
		else
		{
			if ($bagian == 'tema')
			{
				echo '<h1 class = "dg-tema">';
				the_content();
				echo '</h1>';
			}
			else if ($bagian == 'subtema')
			{
				echo '<h2 class ="dg-subtema">';
				the_content();
				echo '</h2>';
			}
			else if ($bagian == 'seksi')
			{
				echo '<h3 class = "dg-seksi">';
				the_content();
				echo '</h3>';
			}
			else if ($bagian == 'bab')
			{
				echo '<h4 class = "dg-bab">';
				the_content();
				echo '</h4>';
			}
			else
			{
				echo '<h5 class = "dg-subbab"">';
				the_content();
				echo '</h5>';
			}
		}
	?>
	<?php
		}
		$iter = $iter + 1; 
	?>
		<?php endif; // This was the if statement that broke the loop into three parts based on categories. ?>
	<?php endwhile; // End the loop. Whew. ?>
<?php
	}

function print_category_children(&$categories, &$output, $cat_parent)
	{
		$j = 0;
		foreach ($categories as $category)
		{
			if ($category->parent == $cat_parent)
			{
				if ($j > 0)
				{
					$output = $output . ' / ';
				}
				else
				{
					$output = $output . ' > '; 
				}
				$output .= '<a href="' . get_category_link($category->term_id ).'" title="' . esc_attr( sprintf( __( "Lihat semua artikel di kategori %s" ), $category->name ) ) . '">'.$category->cat_name.'</a>';
				print_category_children($categories, $output, $category->term_id);
				$j = $j + 1;
			}
		}
	}

function get_my_url()
{
	$pageURL = 'http';
 	if ($_SERVER["HTTPS"] == "on") 
 	{
 		$pageURL .= "s";
 	}
 	$pageURL .= "://";
 	if ($_SERVER["SERVER_PORT"] != "80") 
 	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 	} 
 	else 
 	{
  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 	}
 	return $pageURL;
}

function custom_404_error($option1 = 1) 
{
	if ($option1 == 1)
	{
?>
			<div class = "span-17 last">
			<div class = "span-6 exclamation">
				<img src = "<?php echo get_template_directory_uri() . "/images/etc/exclamation.png"; ?>" />
			</div>
			<div class = "span-9 last">
				<div class = "prepend-top-negative-medium">&nbsp;</div>
				<h2 class = "art-title">Maaf, kami tidak berhasil menemukan halaman yang Anda cari ...</h2>
				<div class = "prepend-top-negative-small">&nbsp;</div>
				Kami mohon maaf atas ketidaknyamanan yang terjadi. Jika anda meyakini bahwa halaman yang Anda cari seharusnya ada
				dalam situs ini, Anda dapat menghubungi Administrator kami melalui alamat <em>e-mail</em> yang ada di kaki halaman ini.
			</div>
		</div>
		<div class = "prepend-top-negative">&nbsp;</div>

<?php
	}
?>
		<h2 class = "art-title" style = "font-size: 18px; border-bottom: 1px dashed #CC3300; padding-bottom: 5px;">Atau apakah anda sedang mencari artikel-artikel berikut ini?</h2>
		<div class = "prepend-top-negative-small">&nbsp;</div>
		<?php 
			$args = array
			(
    			'numberposts'     => 6,
    			'offset'          => 0,
    			'category'        => NULL,
    			'orderby'         => 'post_date',
    			'order'           => 'DESC',
    			'include'         => NULL,
    			'exclude'         => NULL,
    			'meta_key'        => NULL,
    			'meta_value'      => NULL,
    			'post_type'       => 'post',
    			'post_mime_type'  => NULL,
    			'post_parent'     => NULL,
    			'post_status'     => 'publish' ); 
    			$posts_array = get_posts( $args );
    			$numbah = 1;
    			$btn_href = "window.location.href = '" . get_home_url() . "'";
    			$author_metas = '';
    		?>
    				<?php foreach( $posts_array as $post ) : ?>
    				<?php
    					if (get_the_author_meta( 'first_name', $post->post_author ) != '')
    					{
    						$author_metas = get_the_author_meta( 'first_name', $post->post_author );
    					}
    					else
    					{
    						$author_metas = get_the_author_meta( 'display_name', $post->post_author );
    					}
    					if (get_the_author_meta( 'last_name', $post->post_author ) != '')
    					{
    						$author_metas = $author_metas . ' ' . get_the_author_meta( 'last_name', $post->post_author );
    					}
    				?>
    					<?php
    						//echo $post->post_date;
    						$post_date_array = explode(" ", $post->post_date);
    						$post_date = $post_date_array[0];
    						$post_date_array = explode("-", $post_date);
    						$day_link = get_day_link( $post_date_array[0], $post_date_array[1], $post_date_array[2] );
    					?>
    					<?php echo $numbah . '.&nbsp;&nbsp;<a href = "' . get_permalink($post->ID) . '">' . $post->post_title . '</a> - <span style = "font-size: 12px;">Ditulis oleh <a href = "' . get_author_posts_url($post->post_author) . '">' . $author_metas . '</a> pada tanggal <a href = "' . $day_link . '">' . date_i18n(get_option('date_format'), strtotime($post->post_date)) . '</a></span><br />'; ?>
						<?php $numbah = $numbah + 1; ?>
					<?php endforeach; ?>
			<br />
			<button onclick = "<?php echo $btn_href; ?>" class = "medium">&larr;&nbsp;Kembali ke Halaman Muka</button>
			<br />
			<div class = "prepend-top-negative-small">&nbsp;</div>
	</div>
	<br />
<?php
}

function print_category_hierarchy()
{
	$categories = get_the_category();
	$output = '';
	//print_r($categories);
	$j = 0;
	if($categories)
	{
		foreach($categories as $category) 
		{
			if ($category->parent == 0)
			{
				if ($j > 0)
				{
					$output = $output . ' | ';
				}
				$output .= '<a href="' . get_category_link($category->term_id ).'" title="' . esc_attr( sprintf( __( "Lihat semua artikel di kategori %s" ), $category->name ) ) . '">'.$category->cat_name.'</a>';
				print_category_children($categories, $output, $category->term_id);
				$j = $j + 1;
			}
		}
		return trim($output, $seperator);
	}
}


add_filter('excerpt_more', 'new_excerpt_more');

function remove_wmp_image_sizes($sizes) 
{
        unset( $sizes['full']);
        
        return $sizes;
}

add_filter('intermediate_image_sizes_advanced', 'remove_wmp_image_sizes');
add_image_size( 'custom-kecil', 150, 150 );
add_image_size( 'custom-sedang', 330, 660 );
add_image_size( 'custom-agak-besar', 600, 1200 );
add_image_size( 'custom-besar', 695, 1400 );

add_action('admin_head', 'record_post_type');
$recorded_type = '';
$recorded_type2 = '';

function record_post_type()
{
	global $recorded_type, $recorded_type2;
	$myurl = get_my_url();
	$myurl_array = explode("/media-upload.php?post_id=", $myurl);
	$myurl_array2 = explode("/post.php?post=", $myurl);
	$myurl2 = '';
	if (isset($myurl_array[1]) AND $myurl_array[1] != '')
	{
		$myurl2 = $myurl_array[1];
		$myurl_array = explode("&tab=library", $myurl2);
		if (isset($myurl_array[0]) AND $myurl_array[0] != '')
		{
			$recorded_type = get_post_type($myurl_array[0]);
		}
	}
	else
	{
		$recorded_type = 'post';
	}
	$myurl3 = '';
	if (isset($myurl_array2[1]) AND $myurl_array2[1] != '')
	{
		$myurl3 = $myurl_array2[1];
		$myurl_array = explode("&action=edit", $myurl3);
		if (isset($myurl_array[0]) AND $myurl_array[0] != '')
		{
			$recorded_type2 = get_post_type($myurl_array[0]);
		}
	}
	else
	{
		$recorded_type2 = 'post';
	}
}

function custom_wmu_image_sizes($sizes) 
{
	global $recorded_type;
       unset( $sizes['thumbnail'] );
       if ($recorded_type == 'kalender-acara')
       {
      	 	$sizes = array(
       			'custom-kecil'	=> 'Kecil',
    			'large'     => 'Sedang',
    			'custom-agak-besar'     => 'Agak Besar',
    			'custom-besar' => 'Besar',
    			'full' => 'Penuh'
  			);
  		}
  		else
  		{
  			$sizes = array(
    			'large'     => 'Sedang'
  			);
  		}
       return $sizes;
}

function sitemap_instance() 
{
	global $halamans;
	foreach ($halamans as $h)
	{
		if ($h->post_parent == 0 AND get_post_meta($h->ID, 'kunci', true) == '~~7')
		{
?>
			<a href = "<?php get_permalink($h->ID); ?>" class = "span-17 last colsubtitle colsubtitle-link" style = "display: block; text-decoration: none; width: 650px;">
				<?php echo $h->post_title; ?>
			</a>
<?php
			foreach($halamans as $h2)
			{
				if ($h->ID == $h2->post_parent)
				{
			?>
					<div class = "span-17 last sitemap-instance">
						<?php sitemap_subpages($h->ID); ?>
					</div>
			<?php
					break;
				}
			}	
		}
	}
}

function sitemap_subpages($id)
{
	global $halamans;
	$i = 0;
	$j = 0;
	$open = 0;
	foreach ($halamans as $h)
	{
		if ($id == $h->post_parent)
		{
			if ($i == 0)
			{
				echo '<div style = "width: 100%;">';
				$open = 1;
			}
?>
			<div class = "span-5">
				<h3 class = "panel-subtitle"><a class = "link-bold" href = "<?php echo get_permalink($h->ID); ?>"><?php echo $h->post_title; ?></a></h3>
				<p><?php echo get_post_meta($h->ID, 'Deskripsi', true);?></p>
				<?php sitemap_subsubpages($h->ID, 1); ?>
			</div>
<?php
			$i = $i + 1;
		}
		$j = $j + 1;
		if ($i == 3)
		{
			$i = 0;
			$open = 0;
			echo '</div>';
		}
		if ($j == count($halamans) AND $open)
		{
			echo '</div>';
			break;
		}
	}
?>
<?php
}

function sitemap_subsubpages($id, $depth)
{
	global $halamans;
	$i = 0;
	$open = FALSE;
	foreach ($halamans as $h)
	{
		if ($id == $h->post_parent)
		{
			if ($i == 0)
			{
				echo '<ul style = "color: #660000;">';
				$open = TRUE;
			}
			if ($depth < 2)
			{
				echo '<li><a class = "link" href = "' . get_permalink($h->ID) . '">' . $h->post_title . '</a></li>';
			}
			else
			{
				echo '<li style = "list-style-type: circle;"><a class = "link" href = "' . get_permalink($h->ID) . '">' . $h->post_title . '</a></li>';
			}
			sitemap_subsubpages($h->ID, $depth + 1);
			$i = $i + 1;
		}
	}
	if ($open)
	{
		echo '</ul>';
	}
}

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter('manage_edit-kalender-acara_columns', 'add_new_kalender_acara_columns');
function add_new_kalender_acara_columns($kalender_acara_columns) 
{
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Judul Acara', 'column name');
	$new_columns['date'] = __('Dibuat pada');
	$new_columns['tempat'] = __('Tempat');
	$new_columns['tanggal_mulai'] = __('Mulai (th-bl-tg)');
	$new_columns['tanggal_selesai'] = __('Selesai (th-bl-tg)');
	$new_columns['notifikasi_ditampilkan_mulai_tanggal'] = __('Mulai Pengumuman');
	return $new_columns;
}

add_filter('manage_edit-inspirator_columns', 'add_new_inspirator_columns');
function add_new_inspirator_columns($inspirator_columns) 
{
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Nama Inspirator', 'column name');
	return $new_columns;
}

add_filter('manage_edit-inspirasi_columns', 'add_new_inspirasi_columns');
function add_new_inspirasi_columns($inspirasi_columns) 
{
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Nama Inspirator', 'column name');
	$new_columns['frekuensi'] = __('Frekuensi');
	return $new_columns;
}

add_filter('manage_edit-dokumen-gereja_columns', 'add_new_dokumen_gereja_columns');
function add_new_dokumen_gereja_columns($inspirasi_columns) 
{
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Pasal', 'column name');
	$new_columns['tipe_dokumen'] = __('Nama Dokumen');
	return $new_columns;
}

add_action('manage_kalender-acara_posts_custom_column', 'manage_kalender_acara_columns', 10, 2);
function manage_kalender_acara_columns($column_name, $id) 
{
	global $post;
	switch ($column_name) 
	{
		case 'tanggal_mulai':
			echo get_post_meta( $post->ID , 'tanggal_mulai' , true );
			break;
		case 'tanggal_selesai':
			echo get_post_meta( $post->ID , 'tanggal_selesai' , true );
			break;
		case 'tempat':
			echo get_post_meta( $post->ID , 'tempat' , true );
			break;
		case 'notifikasi_ditampilkan_mulai_tanggal':
			echo get_post_meta( $post->ID , 'notifikasi_ditampilkan_mulai_tanggal' , true );
			break;
		default:
			break;
	}
}

add_action('manage_inspirasi_posts_custom_column', 'manage_inspirasi_columns', 10, 2);
function manage_inspirasi_columns($column_name, $id) 
{
	global $post;
	switch ($column_name) 
	{
		case 'frekuensi':
			echo get_post_meta( $post->ID , 'frekuensi' , true );
			break;
		default:
			break;
	}
}

add_action('manage_dokumen-gereja_posts_custom_column', 'manage_dokumen_gereja_columns', 10, 2);
function manage_dokumen_gereja_columns($column_name, $id) 
{
	global $post;
	switch ($column_name) 
	{
		case 'tipe_dokumen':
			echo get_the_title(get_post_meta( $post->ID , 'tipe_dokumen' , true ));
			break;
		default:
			break;
	}
}

/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
add_filter("manage_edit-kalender-acara_sortable_columns", 'kalender_acara_sort');
function kalender_acara_sort($columns) 
{
	$custom = array(
		'judul_acara' 							=> 'judul_acara',
		'tanggal_mulai' 						=> 'tanggal_mulai',
		'tanggal_selesai' 						=> 'tanggal_selesai',
		'tempat' 								=> 'tempat',
		'notifikasi_ditampilkan_mulai_tanggal' 	=> 'notifikasi_ditampilkan_mulai_tanggal'
	);
	return wp_parse_args($custom, $columns);
}

add_filter("manage_edit-inspirasi_sortable_columns", 'inspirasi_sort');
function inspirasi_sort($columns) 
{
	$custom = array(
		'frekuensi' 							=> get_the_title(get_post_meta( $post->ID , 'tipe_dokumen' , true ))
	);
	return wp_parse_args($custom, $columns);
}

add_filter("manage_edit-dokumen-gereja_sortable_columns", 'dokumen_gereja_sort');
function dokumen_gereja_sort($columns) 
{
	$custom = array(
		'tipe_dokumen' 							=> 'tipe_dokumen'
	);
	return wp_parse_args($custom, $columns);
}

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'kalender_acara_column_orderby' );
function kalender_acara_column_orderby( $vars ) 
{
	if ( isset( $vars['orderby'] ) && 'judul_acara' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'judul_acara',
			'orderby' => 'meta_value'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'tempat' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'tempat',
			'orderby' => 'meta_value'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'tanggal_mulai' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'tanggal_mulai',
			'orderby' => 'meta_value'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'tanggal_selesai' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'tanggal_selesai',
			'orderby' => 'meta_value'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'notifikasi_ditampilkan_mulai_tanggal' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'notifikasi_ditampilkan_mulai_tanggal',
			'orderby' => 'meta_value'
		) );
	}
	if ( isset( $vars['orderby'] ) && 'frekuensi' == $vars['orderby'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'frekuensi',
			'orderby' => 'meta_value'
		) );
	}
	
	if ( isset( $vars['orderby'] ) && 'tipe_dokumen' == $vars['tipe_dokumen'] ) 
	{
		$vars = array_merge( $vars, array(
			'meta_key' => 'tipe_dokumen',
			'orderby' => 'meta_value'
		) );
	}
	return $vars;
}


if(function_exists('register_field')) {
	register_field('acf_time_picker', dirname(__File__) . '/acf_time_picker/acf_time_picker.php');
}

add_action( 'updated_post_meta', 'set_judul', 10, 4 );
add_action( 'added_post_meta', 'set_judul', 10, 4 );
add_action( 'save_post', 'set_temp_post_id');
add_action( 'deleted_post', 'delete_frekuensi');
add_action( 'trashed_post', 'delete_frekuensi');
	

function delete_frekuensi($post_id)
{
	global $wpdb;
	$table = $wpdb->prefix."inspirasi_count";
    $wpdb->query("DELETE FROM $table WHERE id = $post_id");
}


$temp_id = -1;

function set_temp_post_id($post_id)
{
	global $post;
	global $temp_id;
	global $wpdb;
	$slug = 'kalender-acara';
	/* check whether anything should be done */
	$_POST += array("{$slug}_edit_nonce" => '');
    if ( $slug != $_POST['post_type'] ) 
    {
        $slug = 'inspirator';
        if ( $slug != $_POST['post_type'] ) 
    	{
    		$slug = 'inspirasi';
    		if ( $slug != $_POST['post_type'] ) 
    		{
    			$slug = 'dokumen-gereja';
    			if ( $slug != $_POST['post_type'] ) 
    			{
    				$slug = 'tipe-dokumen-gereja';
    				if ( $slug != $_POST['post_type'] ) 
    				{
        				return;
        			}
        			else
        			{
        				$temp_id = $post_id;
        			}
        		}
        		else
        		{
        			$temp_id = $post_id;
        		}
        	}
        	else
        	{	
        		if (get_post_meta($post_id, 'frekuensi', true) == '')
        		{
        			add_post_meta($post_id, 'frekuensi', '0');
        			$table = $wpdb->prefix."inspirasi_count";
        			$wpdb->query("INSERT INTO $table VALUES ($post_id, 0)");
        		}
        		$temp_id = $post_id;
        	}
        }
        else
        {
        	$temp_id = $post_id;
        }
    }
    else
    {
		$temp_id = $post_id;
	}
}

add_action('init', 'process_qs');

function process_qs()
{
	add_rewrite_tag('%tahun%','([^&]+)');
	add_rewrite_tag('%bulan%','([^&]+)');
	add_rewrite_tag('%tanggal%','([^&]+)');
	add_rewrite_tag('%tempat%','([^&]+)');
	add_rewrite_tag('%penyusun%','([^&]+)');
	add_rewrite_tag('%sttg%','([^&]+)');
	add_rewrite_tag('%tipe_dokumen%','([^&]+)');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=mulai&paged=$matches[4]', 'top');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/([0-1][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&paged=$matches[3]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/([0-1][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&paged=$matches[2]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/mulai/([2][0][1-2][2-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&sttg=mulai', 'top');
	
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=mulai&paged=$matches[4]', 'top');
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/([0-1][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&paged=$matches[3]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/([0-1][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&paged=$matches[2]&sttg=mulai', 'top');
	add_rewrite_rule('kalender-acara/([2][0][1-2][2-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&sttg=mulai', 'top');
	
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=selesai&paged=$matches[4]', 'top');
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/([0-1][1-9])/([0-3][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&tanggal=$matches[3]&sttg=selesai', 'top');
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/([0-1][1-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&paged=$matches[3]&sttg=selesai', 'top');
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/([0-1][1-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&bulan=$matches[2]&sttg=selesai', 'top');
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&paged=$matches[2]&sttg=selesai', 'top');
	add_rewrite_rule('kalender-acara/selesai/([2][0][1-2][2-9])/?$', 'index.php?post_type=kalender-acara&tahun=$matches[1]&sttg=selesai', 'top');

	add_rewrite_rule('kalender-acara/penyusun/([^&]+)/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&penyusun=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('kalender-acara/penyusun/([^&]+)/?$', 'index.php?post_type=kalender-acara&penyusun=$matches[1]', 'top');
	add_rewrite_rule('kalender-acara/tempat/([^&]+)/page/([0-9]+)/?$', 'index.php?post_type=kalender-acara&tempat=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('kalender-acara/tempat/([^&]+)/?$', 'index.php?post_type=kalender-acara&tempat=$matches[1]', 'top');
	add_rewrite_rule('dokumen-gereja/([^&]+)/page/([0-9]+)/?$', 'index.php?post_type=dokumen-gereja&tipe_dokumen=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('dokumen-gereja/([^&]+)/?$', 'index.php?post_type=dokumen-gereja&tipe_dokumen=$matches[1]', 'top');
}

function id_dokumen_gereja()
{
	$iddok = -1;
	global $wpdb;
	$tdok_a = explode("/dokumen-gereja/", get_my_url());
	if ($tdok_a[0] != get_my_url())
	{
		$tdok_a2 = explode("/", $tdok_a[1]);
		$tdok = $tdok_a2[0];
		$tipes = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\" AND post_name = \"$tdok\"");
		foreach ($tipes as $t)
		{
			$iddok = $t->ID;
		}
	}
	else
	{
		$tdok_a = explode("&kode_dokumen_gereja=", get_my_url());
		$tdok = $tdok_a[1];
		$iddok = $tdok;
	}
	return $iddok;
}

function modify_menu()
{
  global $submenu;
  if (!current_user_can('activate_plugins'))
  {
  		global $submenu;
  		$submenu['edit.php?post_type=page'][10][1] = 'publish_pages';
  }

  // for posts it should be: 
  // unset($submenu['edit.php'][10]);
}
// call the function to modify the menu when the admin menu is drawn
add_action('admin_menu','modify_menu');

function hide_buttons()
{
  global $current_screen;
  
  if(!current_user_can('activate_plugins'))
  {
    echo '<style>.menu-icon-page .wp-submenu-wrap ul li:nth-child(2){display: none;}</style>'; 
    echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
    global $submenu;
    unset($submenu['edit.php?post_type=page'][10]);
    $submenu['edit.php?post_type=page'][10][1] = 'publish_pages';
  }
  
  // for posts the if statement would be:
  // if($current_screen->id == 'edit-post' && !current_user_can('publish_posts'))
}
add_action('admin_head','hide_buttons');

function permissions_admin_redirect() {
	if(!current_user_can('activate_plugins'))
  	{
		$result = stripos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=page');
		if ($result!==false) {
			wp_redirect(get_option('siteurl') . '/wp-admin/index.php?permissions_error=true');
		}
	}
}

function permissions_admin_notice() {
	// use the class "error" for red notices, and "update" for yellow notices
	if(!current_user_can('activate_plugins'))
  	{
		echo "<div id='permissions-warning' class='error fade'><p><strong>".__('Anda tidak diizinkan untuk menambah halaman baru. Silahkan hubungi Administrator untuk membuat permohonan pembuatan halaman baru. Terima kasih.')."</strong></p></div>";
	}
}


function permissions_show_notice(){
	if(!current_user_can('activate_plugins'))
  	{
		if($_GET['permissions_error']){
			add_action('admin_notices', 'permissions_admin_notice');  
		}
	}
}

function tml_new_user_registered( $user_id ) 
{
    wp_set_auth_cookie( $user_id, false, is_ssl() );
}

function pernyataan_iman($setting = 0)
{
	if ($setting == 0)
	{
?>
		<div id = "pernyataan-iman" class = "fill-cream span-17">
	<?php
	}
	else
	{
	?>
		<div id = "pernyataan-iman" class = "fill-cream span-17" style = "display: block;">
	<?php
	}
	?>
						<h4>Pernyataan Iman</h4>
						<p>
							1. Saya percaya akan Allah Bapa, pencipta langit dan bumi.
						</p>
						<p>
							2. Saya percaya akan Yesus Kristus, Putera Allah yang tunggal. Ia sehakikat dengan Sang Bapa.
						</p>
						<p>
							3. Saya percaya akan Allah Roh Kudus
						</p>
						<p>
							4. Saya percaya akan Gereja yang Satu, Kudus, Katolik, dan Apostolik.
						</p>
						<p>
							5. Saya setia kepada Bapa Suci (Paus) di Vatikan.
						</p>
						<br />
						<div id = "amin">
							<input name = "amin_check" id = "amin_check" type = "checkbox" value = "amin" /> &nbsp;Amin!
						</div>
						<span class = "error-msg">
						<?php
							if (isset($_SESSION['amin_empty']) AND $_SESSION['amin_empty'] != '')
							{
								echo '&rarr; ' . $_SESSION['amin_empty'] . '<br />';
								unset($_SESSION['amin_empty']);
							}
						?>
						</span>
					</div>
<?php
}

add_action( 'user_register', 'tml_new_user_registered' );

add_action( 'init', 'create_post_type_data_umat' );

function create_post_type_data_umat()
{
	$args = array
	(
		'label' => 'Data Umat',
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 23,
		'supports' => array('author'),
		'has_archive' => false,
		'public' => true
	);
    register_post_type('data-umat', $args );
}

add_action('admin_menu','permissions_admin_redirect');
add_action('admin_head','hilangkan_beberapa_setting');
add_action('admin_init','permissions_show_notice');
//add_action('comment_post', 'masukkan_komentar');

function hilangkan_beberapa_setting()
{
	if (!current_user_can('activate_plugins'))
	{
		echo '<style type = "text/css">#coauthorsdiv{display: none;}#mappress{display: none;}#postcustom{display: none;}#customsidebars-mb, #acf_841, #minor-publishing, #pageparentdiv{display: none !important;}</style>';
	}
	echo '<style type = "text/css">#wypiekacz_sectionid{display: none;} #customsidebars-mb{display: none;}</style>';
}

/*
function masukkan_komentar($komen_id)
{
	global $current_user;
	global $wpdb;
    get_currentuserinfo();
    $uid = $current_user->ID;
	$verif = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"data-umat\" AND post_status = \"publish\" AND post_author = $uid");
	if (!current_user_can('edit_pages'))
	{
		foreach ($verif as $v)
		{
			wp_set_comment_status( $komen_id, 'approve' );
		}
	}
	else
	{
		wp_set_comment_status( $komen_id, 'approve' );
	}
}
*/

function set_judul() 
{
	global $post;
	global $temp_id;
	$slug = 'kalender-acara';
	/* check whether anything should be done */
	$_POST += array("{$slug}_edit_nonce" => '');
    if ( $slug != $_POST['post_type'] ) 
    {
    	$slug = 'inspirator';
    	if ( $slug != $_POST['post_type'] ) 
    	{
    		$slug = 'inspirasi';
    		if ( $slug != $_POST['post_type'] ) 
    		{
    			$slug = 'dokumen-gereja';
    			if ( $slug != $_POST['post_type'] ) 
    			{
    				$slug = 'tipe-dokumen-gereja';
    				if ( $slug != $_POST['post_type'] ) 
    				{
        				return;
        			}
        			else if ($temp_id > -1)
        			{
        				$my_post = array();
    					$my_post['ID'] = $temp_id;
						$my_post['post_title'] = get_post_meta($temp_id, 'nama_dokumen_original', true);
    					$my_post['post_name'] = sanitize_title_with_dashes($my_post['post_title']);
    					if (get_post_meta($temp_id, 'nama_dokumen_original', true) != '')
    					{
    						wp_update_post( $my_post );
    					}	
        				$temp_id = -1;
        			}
        			else
        			{
        				return;
        			}
        		}
        		else if ($temp_id > -1)
        		{
        			$my_post = array();
    				$my_post['ID'] = $temp_id;
    				$panjang_nomor = strlen(get_post_meta($temp_id, 'nomor_pasal', true));
    				$nomor_pasal_baru = get_post_meta($temp_id, 'nomor_pasal', true);
    				$butuh_nol = 4 - $panjang_nomor;
    				for ($l = 0; $l < $butuh_nol; $l = $l + 1)
    				{
    					$nomor_pasal_baru = '0' . $nomor_pasal_baru;
    				}
    				if (get_post_meta($temp_id, 'bagian', true) == '' OR get_post_meta($temp_id, 'bagian', true) == 'pasal')
    				{
						$my_post['post_title'] = $nomor_pasal_baru;
    				}
    				else
    				{
    					if (get_post_meta($temp_id, 'bagian', true) == 'tema')
    					{
    						$my_post['post_title'] = $nomor_pasal_baru . ': ' . get_post_meta($temp_id, 'isi_pasal', true);
    					}
    					else if (get_post_meta($temp_id, 'bagian', true) == 'subtema')
    					{
    						$my_post['post_title'] = $nomor_pasal_baru . ':: ' . get_post_meta($temp_id, 'isi_pasal', true);
    					}
    					else if (get_post_meta($temp_id, 'bagian', true) == 'seksi')
    					{
    						$my_post['post_title'] = $nomor_pasal_baru . '::: ' . get_post_meta($temp_id, 'isi_pasal', true);
    					}
    					else if (get_post_meta($temp_id, 'bagian', true) == 'bab')
    					{
    						$my_post['post_title'] = $nomor_pasal_baru . ':::: ' . get_post_meta($temp_id, 'isi_pasal', true);
    					}
    					else if (get_post_meta($temp_id, 'bagian', true) == 'subbab')
    					{
    						$my_post['post_title'] = $nomor_pasal_baru . '::::: ' . get_post_meta($temp_id, 'isi_pasal', true);
    					}
    				}
    				$my_post['post_name'] = sanitize_title_with_dashes($my_post['post_title']);
    				$my_post['post_content'] = get_post_meta($temp_id, 'isi_pasal', true);
    				if ($nomor_pasal_baru != '')
    				{
    					wp_update_post( $my_post );
    				}	
        			$temp_id = -1;
        		}
        		else
        		{
        			return;
        		}
        	}
        	else if ($temp_id > -1)
        	{
        		$my_post = array();
    			$my_post['ID'] = $temp_id;
				$my_post['post_title'] = get_the_title(get_post_meta($temp_id, 'nama_inspirator_select', true));
    			$my_post['post_name'] = sanitize_title_with_dashes($my_post['post_title']);
    			if (get_post_meta($temp_id, 'nama_inspirator_select', true) != '')
    			{
    				wp_update_post( $my_post );
    			}	
    			$temp_id = -1;
        	}
        	else
        	{
        		return;
        	}
        }
        else if ($temp_id > -1)
        {
        	$my_post = array();
    		$my_post['ID'] = $temp_id;
    		$my_post['post_title'] = get_post_meta($temp_id, 'nama_inspirator', true);
    		$my_post['post_name'] = sanitize_title_with_dashes($my_post['post_title']);
    		if (get_post_meta($temp_id, 'nama_inspirator', true) != '')
    		{
    			wp_update_post( $my_post );
    		}	
    		$temp_id = -1;
        }
        else
        {
        	return;
        }
    }
    else if ($temp_id > -1)
    {
    	//echo get_post_meta($temp_id, 'judul_acara', true);
    	$my_post = array();
    	$my_post['ID'] = $temp_id;
    	$my_post['post_title'] = get_post_meta($temp_id, 'judul_acara', true);
    	$my_post['post_name'] = sanitize_title_with_dashes($my_post['post_title']);
    	if (get_post_meta($temp_id, 'judul_acara', true) != '')
    	{
    		wp_update_post( $my_post );
    	}
    	$temp_id = -1;
    }
    else
    {
    	return;
    }
}



add_filter('image_size_names_choose', 'custom_wmu_image_sizes');

$attachment_page = FALSE;

$halamans = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'menu_order'));

$chek = FALSE;

$mymeta = array();

$search_error = FALSE;

$category_error = FALSE;

$bulan_indonesia = array
					(
						"01" => "Januari",
						"02" => "Februari",
						"03" => "Maret",
						"04" => "April",
						"05" => "Mei",
						"06" => "Juni",
						"07" => "Juli",
						"08" => "Agustus",
						"09" => "September",
						"10" => "Oktober",
						"11" => "November",
						"12" => "Desember"
					);

?>
<?php
/*
Plugin Name: WyPiekacz
Plugin URI: http://www.poradnik-webmastera.com/projekty/wypiekacz/
Description: Checks if posts submitted for review and posted satisfies set of rules.
Author: Daniel Frużyński
Version: 2.2
Author URI: http://www.poradnik-webmastera.com/
Text Domain: wypiekacz
License: GPL2
*/

/*  Copyright 2009-2011  Daniel Frużyński  (email : daniel [A-T] poradnik-webmastera.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$nodraft = FALSE;

if ( !class_exists( 'WyPiekacz' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {

/*// Include compatibility file if WP version is not current (WP 3.2.x)
if ( version_compare( $wp_version, '3.2', '<' ) ) {
	include( dirname( __FILE__ ) . '/compat.php' );
}*/

class WyPiekacz {
	// List of all errors found during rule check
	var $errors = array();
	// True if we are on supported post page
	var $post_page = false;
	// Link counter - used by RX for removing links
	var $link_counter = 0;
	// Number of initial links to remove - used by RX for removing links
	var $links_to_remove = 0;
	// List of supported post types
	var $post_types = array();
	// Flag if we are deleting orphaned posts, to avoid infinite recursion
	var $deleting_orphaned_posts = false;
	
	// WP versions
	var $has_wp_28 = false;
	var $has_wp_29 = false;
	var $has_wp_30 = false;
	
	// True if User Locker 1.2+ is active
	var $has_user_locker = false;
	
	// Constructor
	function WyPiekacz() {
		global $wp_version;
		$this->has_wp_28 = version_compare( $wp_version, '2.7.999', '>' );
		$this->has_wp_29 = version_compare( $wp_version, '2.8.999', '>' );
		$this->has_wp_30 = version_compare( $wp_version, '2.9.999', '>' );
		
		// Initialise plugin
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'init_late' ), 9999999 );
		
		// Save post - filter for data (this is always registered to make sure rules are always checked)
		add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post' ), 100, 2 );
		
		if ( is_admin() ) {
			// Initialise plugin - admin part
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			
			// Add option to Admin menu
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			
			// Provide icon for Ozh' Admin Drop Down Menu plugin
			add_action( 'ozh_adminmenu_icon_'.plugin_basename( __FILE__ ), array( &$this, 'ozh_adminmenu_icon' ) );
			
			// Add new row to Right Now widget in Admin Dashboard
			if ( get_option( 'wypiekacz_right_now_stats' ) ) {
				if ( $this->has_wp_30 ) {
					add_action( 'right_now_content_table_end', array( &$this, 'right_now_table_end' ) );
				} else {
					add_action( 'right_now_table_end', array( &$this, 'right_now_table_end' ) );
				}
			}
			
			// Save post - called after post is saved
			add_action( 'save_post', array( &$this, 'save_post' ) );
			// Display notices in Admin panel
			add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
			// Required to check if we are on Edit Post page
			add_action( 'do_meta_boxes', array( &$this, 'do_meta_boxes' ) );
			// Change redirect URL used after post is saved (WP2.9+)
			add_filter( 'redirect_post_location', array( &$this, 'redirect_post_location' ) );
			
			// Default post template handling
			add_action( 'submitpost_box', array( &$this, 'submitpost_box' ) );
			
			// Unload autosave script if needed
			if ( get_option( 'wypiekacz_autosave_interval' ) == 0 ) {
				add_action( 'wp_print_scripts', array( &$this, 'wp_print_scripts' ) );
			}
		}
	}
	
	// Plugin initialization
	function init() {
		load_plugin_textdomain( 'wypiekacz', false, dirname( plugin_basename( __FILE__ ) ).'/lang' );
		
		// Check if User Locker 1.2+ is active
		if ( function_exists( 'user_locker_lock_user' ) ) {
			$this->has_user_locker = true;
			
			// Register hooks for filters and actions which depends on User Locker
			add_action( 'user_locker_unlock_user', array( &$this, 'user_locker_unlock_user' ) );
			add_action( 'user_locker_enable_user', array( &$this, 'user_locker_unlock_user' ) );
			
			if ( is_admin() ) {
				// Add new column to the user list
				add_filter( 'manage_users_columns', array( &$this, 'manage_users_columns' ) );
				add_filter( 'manage_users_custom_column', array( &$this, 'manage_users_custom_column' ), 10, 3 );
			}
		}
	}
	
	// Plugin initialization - 2nd phase
	function init_late() {
		// Setup list of supported post types
		if ( $this->has_wp_30 ) {
			$this->post_types = get_option( 'wypiekacz_post_types' );
		} else {
			$this->post_types = array( 'post' );
		}
		
		// Register post-type-specific things
		if ( is_admin() ) {
			if ( $this->has_wp_30 ) { // Post-WP 3.0
				foreach ( $this->post_types as $post_type ) {
					// Add new column on the Edit Posts page
					add_filter( 'manage_edit-'.$post_type.'_columns', array( &$this, 'manage_edit_columns' ) );
				}
			} else { // Pre-WP3.0
				// Add new column on the Edit Posts page
				add_filter( 'manage_edit_columns', array( &$this, 'manage_edit_columns' ) );
			}
			// Print values for new column on the Edit Posts page
			add_action( 'manage_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );
		}
	}
	
	// Plugin initialization - admin
	function admin_init() {
		// Register plugin options
		register_setting( 'wypiekacz', 'wypiekacz_min_len', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_len_words', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_links', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_max_links', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_link_after', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_link_after_words', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_title_len', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_title_len_words', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_max_title_len', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_max_title_len_words', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_use_def_cat', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_cats', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_max_cats', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_min_tags', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_max_tags', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_limit_for_all', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_def_title' );
		register_setting( 'wypiekacz', 'wypiekacz_def_text' );
		register_setting( 'wypiekacz', 'wypiekacz_new_login_email', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_pass_reset_email', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_right_now_stats', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_post_menu_links', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_badwords', array( &$this, 'sanitize_stringlist' ) );
		register_setting( 'wypiekacz', 'wypiekacz_check_badwords_title', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_check_badwords_content', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_check_badwords_tags', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_goodwords', array( &$this, 'sanitize_stringlist' ) );
		register_setting( 'wypiekacz', 'wypiekacz_post_thumbnail', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_links', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_link_positions', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_title', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_add_dots', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_cats', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_enforce_tags', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_allow_skip_rules', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_post_types', array( &$this, 'sanitize_post_types' ) );
		register_setting( 'wypiekacz', 'wypiekacz_dont_save_invalid_post', array( &$this, 'sanitize_01' ) );
		register_setting( 'wypiekacz', 'wypiekacz_autosave_interval', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_post_revisions', array( &$this, 'sanitize_nonnegative_or_minus1' ) );
		register_setting( 'wypiekacz', 'wypiekacz_empty_trash_days', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_delete_orphaned_drafts', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'wypiekacz', 'wypiekacz_force_delete_orphaned_drafts', array( &$this, 'sanitize_01' ) );
		
		// Do not register these options if User Locker is not active - otherwise WP would clear them when options are saved
		if ( $this->has_user_locker ) {
			register_setting( 'wypiekacz', 'wypiekacz_lock_account', array( &$this, 'sanitize_01' ) );
			register_setting( 'wypiekacz', 'wypiekacz_lock_account_after', array( &$this, 'sanitize_positive' ) );
			register_setting( 'wypiekacz', 'wypiekacz_lock_method', array( &$this, 'sanitize_01' ) );
			register_setting( 'wypiekacz', 'wypiekacz_lock_reason', 'trim' );
			register_setting( 'wypiekacz', 'wypiekacz_lock_show_details', array( &$this, 'sanitize_01' ) );
		}
	}
	
	// Provide icon for Ozh' Admin Drop Down Menu plugin
	function ozh_adminmenu_icon() {
		return plugins_url( 'icon.gif', __FILE__ );
	}
	
	// Add Admin menu option
	function admin_menu() {
		// Menu
		add_submenu_page( 'options-general.php', 'WyPiekacz', 
			'WyPiekacz', 'manage_options', __FILE__, array( &$this, 'options_panel' ) );
		
		// Add links to Posts menu
		if ( get_option( 'wypiekacz_post_menu_links' ) ) {
			$can_edit = current_user_can( 'edit_posts' );
			$can_publish = current_user_can('publish_posts');
			if ( $can_edit || $can_publish ) {
				$num_posts = wp_count_posts( 'post' );
				
				$drafts = $num_posts->draft;
				add_submenu_page( 'edit.php', __('Drafts', 'wypiekacz'), 
					sprintf( __('Drafts %s', 'wypiekacz'), "<span class='awaiting-mod count-$drafts'><span class='pending-count'>" . 
						number_format_i18n( $drafts ) . "</span></span>" ),
					'edit_posts', 'edit.php?post_status=draft' );
				
				$pending = $num_posts->pending;
				add_submenu_page( 'edit.php', __('Pending', 'wypiekacz'), 
					sprintf( __('Pending %s', 'wypiekacz'), "<span class='awaiting-mod count-$pending'><span class='pending-count'>" . 
						number_format_i18n( $pending ) . "</span></span>" ),
					'edit_posts', 'edit.php?post_status=pending' );
			}
		}
		
		// Add metabox to edit post page
		foreach ( $this->post_types as $post_type ) {
			add_meta_box( 'wypiekacz_sectionid', 'WyPiekacz', array( &$this, 'post_metabox' ), 
				$post_type, 'normal', 'high' );
		}
	}
	
	// Check if we are on Edit Post page
	function do_meta_boxes( $type ) {
		if ( in_array( $type, $this->post_types ) ) {
			$this->post_page = true;
		}
	}
	
	// Display notice in Admin panel
	function admin_notice() {
		global $post;
		$meta = '';
		if ( $this->post_page && is_object( $post ) ) {
			$meta = get_post_meta($post->ID, 'WyPiekacz_msg', true);
		}
		global $nodraft;
		if ('' != $meta && !$nodraft) {
			// Display error message
			echo '<div id="notice" class="error"><p>', $meta, 
				'<br />', __('', 'wypiekacz');
			if ( isset( $_GET['message'] ) && ( $_GET['message'] == '85614' ) ) {
				echo '<br />', __('Post was *NOT* saved.', 'wypiekacz');
			}
			echo '</p></div>', "\n";
			
			// Remove this message
			delete_post_meta( $post->ID, 'WyPiekacz_msg' );
			
			// redirect_post_location filter is supported starting from WP2.9
			if ( !$this->has_wp_29 ) { // 2.8 and below
				// Change WP message to 'Post saved'
				if ( isset( $_GET['message'] ) ) {
					if ( '6' == $_GET['message'] ) {
						$_GET['message'] = '7';
					} elseif ( '85614' == $_GET['message'] ) {
						unset( $_GET['message'] );
					}
				}
			}
		}
		else
		{
			$nodraft = FALSE;
		}
	}
	
	// Change redirect URL used after post is saved (WP2.9+)
	function redirect_post_location( $location ) {
		global $nodraft;
		if ( count( $this->errors ) > 0 ) {
			$location = remove_query_arg( 'message', $location );
			// When invalid post was not saved, WyPiekacz will display appropriate message
			if ( get_option( 'wypiekacz_dont_save_invalid_post' ) ) {
				$location = add_query_arg( 'message', '85614', $location );
			} else if (!$nodraft){
				$location = add_query_arg( 'message', '10', $location );
			} else {
				$nodraft = FALSE;
				$location = add_query_arg( 'message', '1', $location );
			}
		}
		return $location;
	}
	
	// Check submitted post data
	function wp_insert_post( $data, $post_arr ) {
		// Skip post revisions and auto-drafts
		if ( ( $data['post_type'] == 'revision' ) || ( $data['post_status'] == 'auto-draft' ) ) {
			return $data;
		}

		// TODO: although it is possible to stop creation of auto drafts from here by breaking the query,
		// it does not work as expected - there are many PHP warnings in debug mode, and finally
		// "You are not allowed to edit this post." error on next post save attempt.
		// WP Core must be fixed first in order to make this work.
		
		
		// Delete orphaned post drafts
		if ( $this->deleting_orphaned_posts ) { // Avoid infinite recursion
			return $data;
		} else {
			$this->deleting_orphaned_posts = true;
			$this->delete_orphaned_drafts();
			$this->deleting_orphaned_posts = false;
		}
		
		if (
			// Check selected post types only
			in_array( $data['post_type'], $this->post_types )
			// Check only if status is Published or Pending Review or Future
			&& ( in_array( $data['post_status'], array( 'publish', 'pending', 'future' ) ) )
			// Editors (and above) can have limits too
			&& ( ( !get_option( 'wypiekacz_limit_for_all' ) && !current_user_can( 'edit_others_posts' ) )
				|| get_option( 'wypiekacz_limit_for_all' ) )
		) {
			$ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
			// Need to check this here, because save_post hook is called later
			// Also need to handle AJAX calls - both quickedit and autosave
			$skip_check = false;
			if ( get_option( 'wypiekacz_allow_skip_rules' ) && current_user_can( 'edit_others_posts' ) ) {
				if ( $ajax ) { // AJAX call
					if ( !$autosave ) { // Quickedit
						$skip_check = get_post_meta( $post_arr['ID'], '_wypiekacz_skip_check', true );
					}
				} elseif ( isset( $post_arr['wypiekacz_nonce'] ) ) { // Normal post save, with our nonce
					if ( wp_verify_nonce( $post_arr['wypiekacz_nonce'], plugin_basename( __FILE__ ) ) &&
						isset( $post_arr['wypiekacz_skip_check'] ) ) { // Nonce is OK and option checked
						$skip_check = true;
					}
				} else { // 2nd call when saved post contains attachments - there is no nonce this time
					$skip_check = get_post_meta( $post_arr['ID'], '_wypiekacz_skip_check', true );
				}
			}
			
			if ( !$skip_check ) {
				// Enforce some rules before checking them
				$data = $this->enforce_rules( $data );
				global $nodraft;
				// Check rules
				$result = $this->check_precel_post( $data['post_content'], $data['post_title'], $post_arr );
				if ( true !== $result && !$nodraft) {
					// Revert post status to Draft
					$data['post_status'] = 'draft';
					
					if ( !current_user_can( 'edit_others_posts' ) && isset( $post_arr['ID'] ) ) {
						// Delete 'skip check' flag when normal user will spoil the post
						delete_post_meta( $post_arr['ID'], '_wypiekacz_skip_check' );
					}
					
					// This is not supported in the core yet
					// See https://core.trac.wordpress.org/ticket/10480
					/*if ( $ajax ) {
						$errors = $this->errors;
						$this->errors = array();
						return new WP_Error( 'edit_refused', implode( '; ', $errors ) );
					}*/
					
					// Do not save invalid post if user asked for this. So far the only way is to break SQL query, so register new filter to do this.
					if ( get_option( 'wypiekacz_dont_save_invalid_post' ) ) {
						// save_post hook will not be called later, so need to perform some extra steps here
						
						// Save errors to post meta
						if ( isset( $post_arr['ID'] ) ) {
							delete_post_meta( $post_arr['ID'], 'WyPiekacz_msg' );
							add_post_meta( $post_arr['ID'], 'WyPiekacz_msg', $this->pack_errors( '<br />' ), true );
						}
						
						// Lock user account if needed
						$this->lock_user_account( isset( $post_arr['ID'] ) ? $post_arr['ID'] : 0, false );
						
						// Do not want to execute any extra SQL queries - just proceeded to INSERT/UPDATE query for current post
						remove_all_actions( 'pre_post_update' );
						
						// Now we can add the filter
						add_filter( 'query', array( &$this, 'kill_sql_query' ) );
						
						// Do not execute any extra code beyond this point - return data only
						return $data;
					}
				}
			}
		}
		
		return $data;
	}
	
	// Replace INSERT/UPDATE query with some junk. This filter is used to prevent creating/updating invalid post
	function kill_sql_query( $query ) {
		if ( preg_match( '/^\s*(insert|update:?)\s/i', $query ) ) {
			return 'xxx';
		} else {
			return $query;
		}
	}
	
	// Delete orphaned post drafts
	function delete_orphaned_drafts() {
		$interval = get_option( 'wypiekacz_delete_orphaned_drafts', 0 );
		if ( $interval == 0 ) { // Feature disabled
			return;
		}
		$force = get_option( 'wypiekacz_force_delete_orphaned_drafts' ) ? true : false;
		
		global $wpdb;
		$orphaned_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'draft' AND DATE_SUB( NOW(), INTERVAL $interval DAY ) > post_date" );
		foreach ( (array) $orphaned_posts as $delete ) {
			wp_delete_post( $delete, $force );
		}
	}
	
	// Lock/disable user account if needed
	function lock_user_account( $post_id, $check_result ) {
		if ( !$this->has_user_locker || !get_option( 'wypiekacz_lock_account' ) ) {
			return;
		}
		
		if ( $post_id <= 0 ) { // Make sure post_id == -1 if it is unknown (new post)
			$post_id = -1;
		}
		
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		
		$last_post_id   = get_user_option( '_wypiekacz_last_post', $user_id, false );
		$bad_post_count = get_user_option( '_wypiekacz_bad_posts', $user_id, false );
		if ( empty( $last_post_id ) ) {
			$last_post_id = 0;
		}
		if ( empty( $bad_post_count ) ) {
			$bad_post_count = 0;
		}
		
		if ( $check_result ) { // Rule check succeeded
			$bad_post_count = 0; // Reset bad post count
		} else { // Rule check failed
			// Following cases are not checked here (do nothing for them):
			// id > 0, last_id == -1 : most probably last failed publish attempt was for the same post (new post), so do nothing
			// id == last_id > 0 : another failed publish attempt for the same post
			
			if ( $post_id < 0 ) { // Post ID is not known (new post)
				++$bad_post_count;
			} elseif ( $last_post_id == 0 ) { // No user meta yet, treat this as a new post
				++$bad_post_count;
			} elseif ( ( $last_post_id > 0 ) && ( $last_post_id != $post_id ) ) { // Post ID has changed
				++$bad_post_count;
			}
		}
		
		update_user_option( $user_id, '_wypiekacz_last_post', $post_id, false );
		update_user_option( $user_id, '_wypiekacz_bad_posts', $bad_post_count, false );
		
		$max_count = get_option( 'wypiekacz_lock_account_after' );
		if ( $bad_post_count > $max_count ) {
			$reason = get_option( 'wypiekacz_lock_reason' );
			if ( get_option( 'wypiekacz_lock_method' ) == 0 ) {
				user_locker_lock_user( $user_id, $reason );
			} else {
				user_locker_disable_user( $user_id, $reason );
			}
			
			// Force logout
			wp_logout();
		}
	}
	
	// User Locker plugin unlocks/enable user account
	function user_locker_unlock_user( $user_id ) {
		// Clear our data
		update_user_option( $user_id, '_wypiekacz_last_post', 0, false );
		update_user_option( $user_id, '_wypiekacz_bad_posts', 0, false );
	}
	
	// Called after post is saved - save error messages too
	function save_post( $post_ID ) {
		$post = get_post( $post_ID );
		
		// Skip post revisions and auto-drafts
		if ( ( $post->post_type == 'revision' ) || ( $post->post_status == 'auto-draft' ) ) {
			return;
		}
		
		// Check if 'Skip rule check' option was checked
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( get_option( 'wypiekacz_allow_skip_rules' ) && isset( $_POST['wypiekacz_nonce'] ) && 
			wp_verify_nonce( $_POST['wypiekacz_nonce'], plugin_basename(__FILE__) ) && 
			in_array( $_POST['post_type'], $this->post_types ) && current_user_can( 'edit_post', $post_ID ) &&
			current_user_can( 'edit_others_posts' ) 
		) {
			// OK, we're authenticated: do the work now
			$skip_check = isset( $_POST['wypiekacz_skip_check'] ) ? trim( $_POST['wypiekacz_skip_check'] ) : '';
			if ( !empty( $skip_check ) ) {
				update_post_meta( $post_ID, '_wypiekacz_skip_check', 1 );
			} else {
				delete_post_meta( $post_ID, '_wypiekacz_skip_check' );
			}
		}
		
		if ( count( $this->errors ) > 0 ) {
			delete_post_meta( $post_ID, 'WyPiekacz_msg' );
			add_post_meta( $post_ID, 'WyPiekacz_msg', $this->pack_errors( '<br />' ), true );
			
			// Lock user account if needed
			$this->lock_user_account( $post_ID, false );
		}
		
		// Lock/unlock user account if needed
		$autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		if ( !$autosave ) {
			$this->lock_user_account( $post_ID, count( $this->errors ) == 0 );
		}
	}
	
	// Add new column to the Edit Posts page
	function manage_edit_columns( $columns ) {
		if ( current_user_can( 'edit_others_posts' ) ) {
			$columns['wypiekacz'] = __('Ada masalah?', 'wypiekacz');
		}
		return $columns;
	}
	
	// Print values in new column on the Edit Posts page
	function manage_posts_custom_column( $column_name, $postID ) {
		if ( current_user_can( 'edit_others_posts' ) && $column_name === 'wypiekacz' ) {
			if ( get_option( 'wypiekacz_allow_skip_rules' ) && 
				get_post_meta( $postID , '_wypiekacz_skip_check', true ) ) {
				_e('[Skipped check]', 'wypiekacz');
			} else {
				$post = get_post( $postID );
				if ( $this->check_precel_post( $post->post_content, $post->post_title, $post ) === true) {
					_e('OK', 'wypiekacz');
				} else {
					echo '<span style="color:red">', $this->pack_errors( '<br />' ), "</span>\n";
					$this->errors = array();
				}
			}
		}
	}
	
	// Callback - remove extra links
	function rx_remove_links( $matches ) {
		// Remove links placed too early first
		if ( $this->links_to_remove > 0 ) {
			--$this->links_to_remove;
			return $matches[2];
		}
		
		// Remove links above limit
		++$this->link_counter;
		if ( $this->link_counter > get_option( 'wypiekacz_max_links' ) ) {
			return $matches[2];
		} else {
			return $matches[1].$matches[2].$matches[3];
		}
	}
	
	// Return word count (Note: empty string = 0 words)
	function count_words( $text ) {
		$text2 = preg_replace( '/<.[^<>]*?>/', ' ', $text );
		$text2 = preg_replace( '/&nbsp;|&#160;/i', ' ', $text2 );
		$text2 = preg_replace( '/[0-9.(),;:!?%#$¿\'"_+=\\/-]*/', '', $text2 );
		$text2 = trim( $text2 );
		if ( $text2 == '' ) {
			$count = 0;
		} else {
			$count = preg_match_all( '/\S\s+/', $text2, $matches );
			if ( $count !== false ) {
				$count += 1;
			} else {
				$count = -1; // Error!
			}
		}
		return $count;
	}
	
	// Enforce rules for posts
	function enforce_rules( $data ) {
		// Find how many initial links should be removed
		$this->links_to_remove = 0;
		if ( get_option( 'wypiekacz_enforce_link_positions' ) ) {
			$first_link_after_chars = get_option( 'wypiekacz_link_after' );
			$first_link_after_words = get_option( 'wypiekacz_link_after_words' );
			
			$cnt = preg_match_all( '/<\s*a\s/i', $data['post_content'], $matches, PREG_OFFSET_CAPTURE );
			for ( $n = 0; $n < $cnt; ++$n ) {
				// $matches[0][N][1] contains match offsets for links
				$link_pos = $matches[0][$n][1];
				$text_before_link = substr( $data['post_content'], 0, $link_pos );
				
				// Check position of link (in characters)
				$text2 = preg_replace( '/\s\s+/', ' ', ltrim( wp_strip_all_tags( $text_before_link ) ) );
				$len = strlen( $text2 );
				if ( $len < $first_link_after_chars ) {
					// Need to remove this link
					++$this->links_to_remove;
					continue;
				}
				
				// Check position of link (in words)
				$count = $this->count_words( $text_before_link );
				if ( $count < $first_link_after_words ) {
					// Need to remove this link
					++$this->links_to_remove;
					continue;
				}
				
				// Link is in correct place - next links will be too, so exit loop
				break;
			}
		}
		
		// Enforce max link count and link positions
		if ( ( $this->links_to_remove > 0 ) || get_option( 'wypiekacz_enforce_links' ) ) {
			$this->link_counter = 0;
			$data['post_content'] = preg_replace_callback( '#(<a [^>]*href\s*=\s*[^>]+[^>]*>)(.*?)(</a>)#', 
				array( &$this, 'rx_remove_links' ), $data['post_content'] );
		}
		
		$add_dots = get_option( 'wypiekacz_enforce_add_dots' );
		
		// Enforce max title length
		if ( get_option( 'wypiekacz_enforce_title' ) ) {
			$need_dots = false;
			
			// Enforce max length (in words)
			$max_len = get_option( 'wypiekacz_max_title_len_words' );
			$count = $this->count_words( $data['post_title'] );
			if ( ( $max_len > 0 ) && ( $count > $max_len ) ) {
				$title = trim( $data['post_title'] );
				$words = preg_split( '/((?:&nbsp;|&#160;|\s)+)/i', $title, -1, PREG_SPLIT_DELIM_CAPTURE );
				$data['post_title'] = implode( '', array_slice( $words, 0, $max_len * 2 - 1 ) );
				
				$need_dots = true;
			}
			
			// Enforce max length (in chars)
			$max_len = get_option( 'wypiekacz_max_title_len' );
			$len = strlen( $data['post_title'] );
			if ( ( $max_len > 0 ) && ( $len > $max_len ) ) {
				if ( $add_dots ) {
					$max_len -= 3; // Make room for three dots at the end
					$need_dots = true;
				}
				if ( version_compare( PHP_VERSION, '5', '<' ) ) {
					$data['post_title'] = substr( $data['post_title'], 0, $max_len + 1 );
					$pos = strrpos( $data['post_title'], ' ' );
				} else {
					$pos = strrpos( $data['post_title'], ' ', $max_len - $len );
				}
				
				if ( $pos !== false ) {
					$data['post_title'] = substr( $data['post_title'], 0, $pos );
				} else {
					// No spaces? Interesting...
					$data['post_title'] = substr( $data['post_title'], 0, $max_len );
				}
			}
			
			if ( $add_dots && $need_dots ) {
				$data['post_title'] .= '...';
			}
		}
		
		// TODO: Enforce max category count
		// Not supported in WP core yet - workaround implemented in enforce_rules_POST()
		/*if ( get_option( 'wypiekacz_enforce_cats' ) ) {
		}*/
		
		// TODO: Enforce max tag count
		// Not supported in WP core yet - workaround implemented in enforce_rules_POST()
		/*if ( get_option( 'wypiekacz_enforce_tags' ) ) {
		}*/
		
		// Allow other plugins to enforce additional rules
		$data = apply_filters( 'wypiekacz_enforce_rules', $data );
		
		return $data;
	}
	
	// Enforce rules for posts - modify $_POST array
	function enforce_rules_POST() {
		if ( isset( $_POST['post_category'] ) && is_array( $_POST['post_category'] ) ) {
			// Remove non-existing categories from user's POST data
			/*if ( true ) {
				$blog_cats = get_all_category_ids();
				$cats = array();
				foreach ( $_POST['post_category'] as $category ) {
					if ( in_array( $category, $blog_cats ) ) {
						$cats[] = $category;
					}
				}
				$_POST['post_category'] = $cats;
			}*/
			
			// Enforce max category count
			if ( get_option( 'wypiekacz_enforce_cats' ) ) {
				$max_cats = get_option( 'wypiekacz_max_cats' );
				
				// WP 3.0 adds extra invalid category with ID = 0 for its purposes - need to take care of it
				$pos = array_search( 0, $_POST['post_category'] );
				if ( $pos !== false ) {
					array_splice( $_POST['post_category'], $pos, 1 );
					$removed_fake_cat = true;
				} else {
					$removed_fake_cat = false;
				}
				
				if ( count( $_POST['post_category'] ) > $max_cats ) {
					// Try to remove default category first, if it is forbidden
					if ( !get_option( 'wypiekacz_use_def_cat' ) ) {
						$default_cat = get_option( 'default_category' );
						$pos = array_search( $default_cat, $_POST['post_category'] );
						if ( $pos !== false ) {
							array_splice( $_POST['post_category'], $pos, 1 );
						}
					}
				}
				
				if ( count( $_POST['post_category'] ) > $max_cats ) {
					// Remove extra categories if still there are too many
					array_splice( $_POST['post_category'], $max_cats );
				}
				
				if ( $removed_fake_cat ) {
					$_POST['post_category'] = array_merge( array( 0 ), $_POST['post_category'] );
				}
			}
		}
		
		// Enforce max tag count
		if ( get_option( 'wypiekacz_enforce_tags' ) ) {
			$max_tags = get_option( 'wypiekacz_max_tags' );
			$found_tags = 0;
			
			// Simple Post Tags plugin uses this
			if ( !empty( $_POST['adv-tags-input'] ) ) {
				$tags = explode( ',', $_POST['adv-tags-input'] );
				$cnt = 0;
				foreach ($tags as $tag) {
					if ( trim( $tag ) != '' ) {
						++$cnt;
					}
				}
				if ( $cnt > $max_tags ) {
					$tags = array_slice( $tags, 0, $max_tags );
					$_POST['adv-tags-input'] = implode( ',', $tags );
				}
				$found_tags += count( $tags );
			}
			
			// WordPress 2.8+ uses general taxonomy support for tags too
			// Need to check this first, because $post_data['tags_input'] is set too
			// Check default tag taxonomy
			if ( !empty( $_POST['tax_input'] ) && is_array( $_POST['tax_input'] ) 
				&& isset( $_POST['tax_input']['post_tag'] ) ) {
				$tags = explode( ',', $_POST['tax_input']['post_tag'] );
				if ( $found_tags + count( $tags ) > $max_tags ) {
					if ( $found_tags >= $max_tags ) {
						unset( $_POST['tax_input']['post_tag'] );
						$tags = array();
					} else {
						$tags = array_slice( $tags, 0, $max_tags - $found_tags );
						$_POST['tax_input']['post_tag'] = implode( ',', $tags );
					}
				}
				$found_tags += count( $tags );
			}
			
			// Default WordPress field (up to 2.7.1)
			// Note: For some reason WP2.8 puts here an array when updating post
			if ( !empty( $_POST['tags_input'] ) ) {
				/*if ( is_array( $post_data['tags_input'] ) ) {
					$tags = $post_data['tags_input'];
				} else {
					$tags = explode( ',', $post_data['tags_input'] );
				}*/
				$tags = explode( ',', $_POST['tags_input'] );
				if ( $found_tags + count( $tags ) > $max_tags ) {
					if ( $found_tags >= $max_tags ) {
						unset( $_POST['tags_input'] );
						//$tags = array();
					} else {
						$tags = array_slice( $tags, 0, $max_tags - $found_tags );
						$_POST['tags_input'] = implode( ',', $tags );
					}
				}
				//$found_tags += count( $tags );
			}
		}
	}
	
	// Check for badwords
	function check_badwords( $text ) {
		$has_mb = function_exists( 'mb_convert_case' );
		$words_found = array();
		
		if ( $has_mb ) {
			$text = mb_convert_case( $text, MB_CASE_LOWER );
			$len = mb_strlen( $text );
		} else {
			$text = strtolower( $text );
			$len = strlen( $text );
		}
		
		$badwords = get_option( 'wypiekacz_badwords', array() );
		$goodwords = get_option( 'wypiekacz_goodwords', array() );
		foreach ( $badwords as $badword ) {
			$pos_bad = 0;
			while ( $pos_bad < $len ) {
				// Try to find badword in text
				if ( $has_mb ) {
					$pos_bad = mb_strpos( $text, $badword, $pos_bad );
				} else {
					$pos_bad = strpos( $text, $badword, $pos_bad );
				}
				
				// Found badword in text
				if ( $pos_bad !== false ) {
					$found_good = false;
					foreach ( $goodwords as $goodword ) {
						// Try to find badword in goodword
						if ( $has_mb ) {
							$pos_good = mb_strpos( $goodword, $badword );
						} else {
							$pos_good = strpos( $goodword, $badword );
						}
						
						// Found badword in goodword
						if ( $pos_good !== false ) {
							$good_start = $pos_bad - $pos_good;
							if ( $has_mb ) {
								$good_len = mb_strlen( $goodword );
								$maybe_good = mb_substr( $text, $good_start, $good_len );
							} else {
								$good_len = strlen( $goodword );
								$maybe_good = substr( $text, $good_start, $good_len );
							}
							
							// Check if word in text is good
							if ( $maybe_good == $goodword ) {
								$found_good = true;
								break;
							}
						}
					}
					
					if ( !$found_good ) {
						$words_found[] = $badword;
						break; // exit $badwords loop
					} else {
						// This was a false hit - continue searching
						if ( $has_mb ) {
							$pos_bad += mb_strlen( $badword );
						} else {
							$pos_bad += strlen( $badword );
						}
					}
				} else {
					break; // exit loop - badword not found
				}
			}
		}
		
		return $words_found;
	}
	
	// Check if post obeys rules
	function check_precel_post( $text, $title, $post_data ) {
		// Check length (in characters)
		$min_len = get_option( 'wypiekacz_min_len' );
		$text2 = preg_replace( '/\s\s+/', ' ', trim( wp_strip_all_tags( $text ) ) );
		$len = strlen( $text2 );
		if ( $len < $min_len ) {
			$this->errors[] = array( 'min_len_chars', sprintf( __('Post is too short (minimum is %1$s chars, your post has %2$s).', 'wypiekacz'),
				$min_len, $len ) );
		}
		
		// Check length (in words)
		$min_len = get_option( 'wypiekacz_min_len_words' );
		$count = $this->count_words( $text );
		if ( $count < $min_len ) {
			$this->errors[] = array( 'min_len_words', sprintf( __('Post is too short (minimum is %1$s words, your post has %2$s).', 'wypiekacz'),
				$min_len, $count ) );
		}
		
		// Check links
		$min_links = get_option( 'wypiekacz_min_links' );
		$max_links = get_option( 'wypiekacz_max_links' );
		$cnt = preg_match_all( '/<\s*a\s/i', $text, $matches, PREG_OFFSET_CAPTURE );
		if ( $cnt < $min_links ) {
			$this->errors[] = array( 'min_links', sprintf( __('Post contains too few links (minimum is %1$s, your post has %2$s).', 'wypiekacz'),
				$max_links, $cnt ) );
		} elseif ( $cnt > $max_links ) {
			$this->errors[] = array( 'max_links', sprintf( __('Post contains too many links (maximum is %1$s, your post has %2$s).', 'wypiekacz'),
				$max_links, $cnt ) );
		}
		
		if ( $cnt > 0 ) {
			// $matches[0][N][1] contains match offsets for links
			$link_pos = $matches[0][0][1];
			$text_before_link = substr( $text, 0, $link_pos );
			
			// Check position of first link (in characters)
			$first_link_after = get_option( 'wypiekacz_link_after' );
			$text2 = preg_replace( '/\s\s+/', ' ', ltrim( wp_strip_all_tags( $text_before_link ) ) );
			$len = strlen( $text2 );
			if ( $len < $first_link_after ) {
				$this->errors[] = array( 'link_after_chars', sprintf( __('First link is too close to the beginning (minimum is after %1$s chars, your link is after %2$s).', 'wypiekacz'),
					$first_link_after, $len ) );
			}
			
			// Check position of first link (in words)
			$first_link_after = get_option( 'wypiekacz_link_after_words' );
			$count = $this->count_words( $text_before_link );
			if ( $count < $first_link_after ) {
				$this->errors[] = array( 'link_after_words', sprintf( __('First link is too close to the beginning (minimum is after %1$s words, your link is after %2$s).', 'wypiekacz'),
					$first_link_after, $count ) );
			}
		}
		
		// Check title length (in characters)
		$min_len = get_option( 'wypiekacz_min_title_len' );
		$max_len = get_option( 'wypiekacz_max_title_len' );
		$text2 = trim( $title );
		$len = strlen( $text2 );
		if ( $len < $min_len ) {
			$this->errors[] = array( 'min_title_len_chars', sprintf( __('Post Title is too short (minimum is %1$s chars, your Title has %2$s).', 'wypiekacz'),
				$min_len, $len ) );
		}
		elseif ( ( $max_len > 0 ) && ( $len > $max_len ) ) {
			$this->errors[] = array( 'max_title_len_chars', sprintf( __('Post Title is too long (maximum is %1$s chars, your Title has %2$s).', 'wypiekacz'),
				$max_len, $len ) );
		}
		
		// Check title length (in words)
		$min_len = get_option( 'wypiekacz_min_title_len_words' );
		$max_len = get_option( 'wypiekacz_max_title_len_words' );
		$count = $this->count_words( $title );
		if ( $count < $min_len ) {
			$this->errors[] = array( 'min_title_len_words', sprintf( __('Post Title is too short (minimum is %1$s words, your Title has %2$s).', 'wypiekacz'),
				$min_len, $count ) );
		}
		elseif ( ( $max_len > 0 ) && ( $count > $max_len ) ) {
			$this->errors[] = array( 'max_title_len_words', sprintf( __('Post Title is too long (maximum is %1$s words, your Title has %2$s).', 'wypiekacz'),
				$max_len, $count ) );
		}
		
		if ( is_array( $post_data ) ) {
			// Get categories from POST data
			if ( empty( $post_data['post_category'] ) || ( 0 == count( $post_data['post_category'] ) ) 
				|| !is_array( $post_data['post_category'] ) ) {
				$post_cat_cnt = 0;
				$categories = array();
			} else {
				// WP 3.0 adds extra invalid category with ID = 0 for its purposes - need to filter it out
				$categories = $post_data['post_category'];
				$pos = array_search( 0, $post_data['post_category'] );
				if ( $pos !== false ) {
					array_splice( $categories, $pos, 1 );
				}
				$post_cat_cnt = count( $categories );
			}
		} else {
			// Get categories from Post object
			$categories = wp_get_post_categories( $post_data->ID );
			$post_cat_cnt = count( $categories );
		}
		
		$has_default_cat = false;
		$default_cat = get_option( 'default_category' );
		foreach ( $categories as $category ) {
			if ( $category == $default_cat ) {
				$has_default_cat = true;
				break;
			}
		}
		
		// Check default category
		$use_default_cat = get_option( 'wypiekacz_use_def_cat' );
		if ( !$use_default_cat && $has_default_cat ) {
			$this->errors[] = array( 'no_def_cat', sprintf( __('Cannot add posts to the default category (%s).', 'wypiekacz'), 
				get_cat_name( $default_cat ) ) );
		}
		
		// When post doesn't have categories, default one will be used
		if ( ( 0 == $post_cat_cnt ) && $use_default_cat ) {
			$post_cat_cnt = 1;
		}
		
		// Check categories count
		$min_cats = get_option( 'wypiekacz_min_cats' );
		$max_cats = get_option( 'wypiekacz_max_cats' );
		if ( $post_cat_cnt < $min_cats ) {
			$this->errors[] = array( 'min_cats', sprintf( __('Too few categories selected (minimum is %1$s, your post has %2$s).', 'wypiekacz'),
				$min_cats, $post_cat_cnt ) );
		} else if ( $post_cat_cnt > $max_cats ) {
			$this->errors[] = array( 'max_cats', sprintf( __('Too many categories selected (maximum is %1$s, your post has %2$s).', 'wypiekacz'),
				$max_cats, $post_cat_cnt ) );
		}
		
		if ( is_array( $post_data ) ) {
			$post_tag_cnt = 0;
			$tags = array();
			// Get tags from POST data
			if ( !empty( $post_data['adv-tags-input'] ) ) {
				// Simple Post Tags plugin uses this
				$tags = explode( ',', $post_data['adv-tags-input'] );
			} else if ( !empty( $post_data['tax_input'] ) && is_array( $post_data['tax_input'] ) ) {
				// WordPress 2.8+ uses general taxonomy support for tags too
				// Need to check this first, because $post_data['tags_input'] is set too
				
				// Check default tag taxonomy
				if ( isset( $post_data['tax_input']['post_tag'] ) ) {
					$tags = explode( ',', $post_data['tax_input']['post_tag'] );
				}
			} else if ( !empty( $post_data['tags_input'] ) ) {
				// Default WordPress field (up to 2.7.1)
				// Note: For some reason WP2.8 puts here an array when updating post
				if ( is_array( $post_data['tags_input'] ) ) {
					$tags = $post_data['tags_input'];
				} else {
					$tags = explode( ',', $post_data['tags_input'] );
				}
			}
			
			foreach ($tags as $tag) {
				if ( trim( $tag ) != '' ) {
					++$post_tag_cnt;
				}
			}
		} else {
			// Get tags from Post object
			$tags = wp_get_post_tags( $post_data->ID );
			$post_tag_cnt = count( $tags );
		}
		
		
		// Check tag count
		$min_tags = get_option( 'wypiekacz_min_tags' );
		$max_tags = get_option( 'wypiekacz_max_tags' );
		
		if ( $post_tag_cnt < $min_tags ) {
			$this->errors[] = array( 'min_tags', sprintf( __('Too few tags (minimum is %1$s, your post has %2$s).', 'wypiekacz'),
				$min_tags, $post_tag_cnt ) );
		} else if ($post_tag_cnt > $max_tags) {
			$this->errors[] = array( 'max_tags', sprintf( __('Too many tags (maximum is %1$s, your post has %2$s).', 'wypiekacz'),
				$max_tags, $post_tag_cnt ) );
		}
		
		// Check forbidden words in title
		if ( get_option( 'wypiekacz_check_badwords_title' ) ) {
			$words_found = $this->check_badwords( $title );
			if ( count( $words_found ) > 0 ) {
				$this->errors[] = array( 'badwords_title', sprintf( __('Forbidden word(s) in title: %s', 'wypiekacz'),
					implode( ', ', $words_found ) ) );
			}
		}
		
		// Check forbidden words in content
		if ( get_option( 'wypiekacz_check_badwords_content' ) ) {
			$words_found = $this->check_badwords( $text );
			if ( count( $words_found ) > 0 ) {
				$this->errors[] = array( 'badwords_content', sprintf( __('Forbidden word(s) in content: %s', 'wypiekacz'),
					implode( ', ', $words_found ) ) );
			}
		}
		
		// Check forbidden words in tags
		if ( get_option( 'wypiekacz_check_badwords_tags' ) ) {
			// Convert objects to string if needed
			if ( ( count( $tags ) > 0 ) && is_object( $tags[0] ) ) {
				$tags_to_check = array();
				foreach ( $tags as $tag ) {
					$tags_to_check[] = $tag->name;
				}
			} else {
				$tags_to_check = $tags;
			}
			
			$words_found = $this->check_badwords( implode( ',', $tags_to_check ) );
			if ( count( $words_found ) > 0 ) {
				$this->errors[] = array( 'badwords_tags', sprintf( __('Forbidden word(s) in tags: %s', 'wypiekacz'),
					implode( ', ', $words_found ) ) );
			}
		}
		
		// Check post thumbnail
		if ( get_option( 'wypiekacz_post_thumbnail' ) ) {
			$post_id = 0;
			if ( is_array( $post_data ) ) {
				// Get ID from POST data
				if ( isset( $post_data['ID'] ) ) {
					$post_id = (int)$post_data['ID'];
				}
			} else {
				// Get ID from Post object
				$post_id = $post_data->ID;
			}
			
			
			
			$has_thumbnail_proper_dimension = false;
        	if (($post_id > 0) AND function_exists('has_post_thumbnail')) 
        	{
        	   	$has_thumbnail = has_post_thumbnail($post_id);
        	   	list($url, $width, $height) = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), "Full");
        	   	echo $imgsrc[0];
        	   	if ($width >= 695)
        	   	{
        	   		$has_thumbnail_proper_dimension=true;
        	  	}
       		}
			global $nodraft;
        	$has_thumbnail = apply_filters( 'wypiekacz_check_thumbnail', $has_thumbnail, $post_id, $post_data );
        	if ($has_thumbnail AND !$has_thumbnail_proper_dimension) 
        	{
        		delete_post_meta( $post_id, '_thumbnail_id' );
        	    $this->errors[] = array('post_thumbnail', __('Panjang Gambar Utama (Featured Image) harus tidak lebih kecil dari 695 pixel. Gambar yang anda pilih sudah dibatalkan secara otomatis oleh sistem.', 'wypiekacz'));
        		$nodraft = TRUE;
        	}
			
			
			/*
			
			$has_thumbnail = false;
			if ( ( $post_id > 0 ) && function_exists( 'has_post_thumbnail' ) ) {
				$has_thumbnail = has_post_thumbnail( $post_id );
			}
			
			$has_thumbnail = apply_filters( 'wypiekacz_check_thumbnail', $has_thumbnail, $post_id, $post_data );
			
			if ( !$has_thumbnail ) {
				$this->errors[] = array( 'post_thumbnail', __('Post thumbnail (Featured image) is required.', 'wypiekacz') );
			}
			*/
		}
		
		// Allow other plugins to check additional rules
		$this->errors = apply_filters( 'wypiekacz_check_post', $this->errors, $text, $title, $post_data );
		
		return count( $this->errors ) == 0;
	}
	
	// Set Title and Content using defined template
	function submitpost_box() {
		global $post;
		
		// Title
		if ( empty( $post->post_title ) || ( '' == $post->post_title ) ) {
			$post->post_title = get_option( 'wypiekacz_def_title' );
		}
		
		// Text
		if ( empty( $post->post_content ) || ( '' == $post->post_content ) ) {
			$post->post_content = get_option( 'wypiekacz_def_text' );
		}
	}
	
	// Show meta box on edit post page
	function post_metabox( $post ) {
		if ( !get_option( 'wypiekacz_allow_skip_rules' ) || !current_user_can( 'edit_others_posts' ) ) {
			return;
		}
		
		$skip_check = get_post_meta( $post->ID , '_wypiekacz_skip_check', true );
?>
<div class="inside">
<!--  Use nonce for verification -->
<input type="hidden" name="wypiekacz_nonce" id="wypiekacz_nonce" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" />
<!-- The actual fields for data entry -->
<p class="meta-options">
<label><input type="checkbox" name="wypiekacz_skip_check" id="wypiekacz_skip_check" <?php checked($skip_check, 1); ?>" /> <?php _e('Skip rule check (publish even if some conditions are not met)', 'wypiekacz'); ?>
</p>
</div>
<?php
	}
	
	// Add new row to Right Now widget in Admin Dashboard
	function right_now_table_end() {
		$can_edit = current_user_can( 'edit_posts' );
		$can_publish = current_user_can('publish_posts');
		if ( !$can_edit && !$can_publish ) {
			return;
		}
		
		$num_posts = wp_count_posts( 'post' );
		
		if ( $can_edit ) {
			// Post Drafts count
			$num = number_format_i18n( $num_posts->draft );
			$text = _n( 'Draft', 'Drafts', $num_posts->draft, 'wypiekacz' );
			$num = "<a href='edit.php?post_status=draft'>$num</a>";
			$text = "<a href='edit.php?post_status=draft'>$text</a>";
			echo '<tr><td class="first b">', $num, '</td><td class="t cats">', $text, '</td>';
			if ( $this->has_wp_30 ) {
				echo '</tr>';
			} else {
				echo '<td colspan="2">&nbsp;</td></tr>';
			}
		}
		
		if ( $can_publish ) {
			// Pending Posts count
			$num = number_format_i18n( $num_posts->pending );
			$text = _n( 'Pending', 'Pending', $num_posts->pending, 'wypiekacz' );
			$num = "<a href='edit.php?post_status=pending'>$num</a>";
			$text = "<a href='edit.php?post_status=pending'>$text</a>";
			echo '<tr><td class="first b">', $num, '</td><td class="t cats">', $text, '</td>';
			if ( $this->has_wp_30 ) {
				echo '</tr>';
			} else {
				echo '<td colspan="2">&nbsp;</td></tr>';
			}
		}
	}
	
	// Convert error array to displayable form
	function pack_errors( $separator ) {
		$ret = '';
		$first = true;
		foreach ( $this->errors as $error ) {
			if ( $first ) {
				$first = false;
			} else {
				$ret .= $separator;
			}
			$ret .= $error[1];
		}
		return $ret;
	}
	
	// Unload autosave script if needed
	function wp_print_scripts() {
		wp_deregister_script( 'autosave' );
	}
	
	// Add new column to the user list page
	function manage_users_columns( $columns ) {
		// This requires WP 2.8+
		global $wp_version;
		if ( $this->has_wp_28 && $this->has_user_locker && get_option( 'wypiekacz_lock_show_details' ) ) {
			$columns['wypiekacz'] = 'WyPiekacz';
		}
		return $columns;
	}
	
	// Add column content for each user on user list
	function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( $column_name == 'wypiekacz' ) {
			$last_post_id   = get_user_option( '_wypiekacz_last_post', $user_id, false );
			$bad_post_count = get_user_option( '_wypiekacz_bad_posts', $user_id, false );
			if ( empty( $last_post_id ) ) {
				$last_post_id = 0;
			}
			if ( empty( $bad_post_count ) ) {
				$bad_post_count = 0;
			}
			
			$value = sprintf( __('Bad Posts: %1$s, Last Post ID: %2$s', 'wypiekacz'), $bad_post_count, $last_post_id );
		}
		
		return $value;
	}
	
	// Handle options panel
	function options_panel() {
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('WyPiekacz - Options', 'wypiekacz'); ?></h2>

<form name="dofollow" action="options.php" method="post">
<?php settings_fields( 'wypiekacz' ); ?>
<table class="form-table">

<tr><th colspan="2"><h3><?php _e('Post:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_len"><?php _e('Minimum post length (in chars):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="6" size="10" id="wypiekacz_min_len" name="wypiekacz_min_len" value="<?php echo esc_attr( get_option( 'wypiekacz_min_len' ) ); ?>" /><br /><?php _e('Note: if you want to check character count only, set minimum word count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_len_words"><?php _e('Minimum post length (in words):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="6" size="10" id="wypiekacz_min_len_words" name="wypiekacz_min_len_words" value="<?php echo esc_attr( get_option( 'wypiekacz_min_len_words' ) ); ?>" /><br /><?php _e('Note: if you want to check word count only, set minimum character count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_links"><?php _e('Minimum link count in post:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_min_links" name="wypiekacz_min_links" value="<?php echo esc_attr( get_option( 'wypiekacz_min_links' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_max_links"><?php _e('Maximum link count in post:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_max_links" name="wypiekacz_max_links" value="<?php echo esc_attr( get_option( 'wypiekacz_max_links' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_link_after"><?php _e('First link is allowed after N initial characters:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="6" size="10" id="wypiekacz_link_after" name="wypiekacz_link_after" value="<?php echo esc_attr( get_option( 'wypiekacz_link_after' ) ); ?>" /><br /><?php _e('Note: if you want to check character count only, set minimum word count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_link_after_words"><?php _e('First link is allowed after N initial words:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="6" size="10" id="wypiekacz_link_after_words" name="wypiekacz_link_after_words" value="<?php echo esc_attr( get_option( 'wypiekacz_link_after_words' ) ); ?>" /><br /><?php _e('Note: if you want to check word count only, set minimum character count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_post_thumbnail"><?php _e('Post thumbnail (Featured image) is required:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_post_thumbnail" name="wypiekacz_post_thumbnail" value="yes" <?php checked( 1, get_option( 'wypiekacz_post_thumbnail' ) ); ?> />
<br /><?php _e('Note: WyPiekacz supports WordPress Featured image by default. You can also provide your own function to check if Post thumbnail is present - see FAQ for more details.', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Post\'s title:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_title_len"><?php _e('Minimum title length (in chars):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_min_title_len" name="wypiekacz_min_title_len" value="<?php echo esc_attr( get_option( 'wypiekacz_min_title_len' ) ); ?>" /><br /><?php _e('Note: if you want to check character count only, set minimum word count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_title_len_words"><?php _e('Minimum title length (in words):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_min_title_len_words" name="wypiekacz_min_title_len_words" value="<?php echo esc_attr( get_option( 'wypiekacz_min_title_len_words' ) ); ?>" /><br /><?php _e('Note: if you want to check word count only, set minimum character count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_max_title_len"><?php _e('Maximum title length (in chars):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_max_title_len" name="wypiekacz_max_title_len" value="<?php echo esc_attr( get_option( 'wypiekacz_max_title_len' ) ); ?>" /><br /><?php _e('Note: if you want to check character count only, set maximum word count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_max_title_len_words"><?php _e('Maximum title length (in words):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_max_title_len_words" name="wypiekacz_max_title_len_words" value="<?php echo esc_attr( get_option( 'wypiekacz_max_title_len_words' ) ); ?>" /><br /><?php _e('Note: if you want to check word count only, set maximum character count to zero.', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Categories:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_use_def_cat"><?php printf( __('Posts can be added to the default category (%s):', 'wypiekacz'), get_cat_name( get_option( 'default_category' ) ) ); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_use_def_cat" name="wypiekacz_use_def_cat" value="yes" <?php checked( 1, get_option( 'wypiekacz_use_def_cat' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_cats"><?php _e('Minimum categories selected:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_min_cats" name="wypiekacz_min_cats" value="<?php echo esc_attr( get_option( 'wypiekacz_min_cats' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_max_cats"><?php _e('Maximum categories selected:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_max_cats" name="wypiekacz_max_cats" value="<?php echo esc_attr( get_option( 'wypiekacz_max_cats' ) ); ?>" />
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Tags:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_min_tags"><?php _e('Minimum tag count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_min_tags" name="wypiekacz_min_tags" value="<?php echo esc_attr( get_option( 'wypiekacz_min_tags' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_max_tags"><?php _e('Maximum tag count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="3" size="10" id="wypiekacz_max_tags" name="wypiekacz_max_tags" value="<?php echo esc_attr( get_option( 'wypiekacz_max_tags' ) ); ?>" />
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Forbidden words:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_check_badwords_title"><?php _e('Check for forbidden words in title:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_check_badwords_title" name="wypiekacz_check_badwords_title" value="yes" <?php checked( 1, get_option( 'wypiekacz_check_badwords_title' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_check_badwords_content"><?php _e('Check for forbidden words in content:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_check_badwords_content" name="wypiekacz_check_badwords_content" value="yes" <?php checked( 1, get_option( 'wypiekacz_check_badwords_content' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_check_badwords_tags"><?php _e('Check for forbidden words in tags:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_check_badwords_tags" name="wypiekacz_check_badwords_tags" value="yes" <?php checked( 1, get_option( 'wypiekacz_check_badwords_tags' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_badwords"><?php _e('Forbidden words list:', 'wypiekacz'); ?></label>
</th>
<td>
<textarea id="wypiekacz_badwords" name="wypiekacz_badwords" rows="5" cols="30"><?php echo esc_html( implode( "\n", get_option( 'wypiekacz_badwords', array() ) ) ); ?></textarea><br />
<?php _e('Put one word per line', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_goodwords"><?php _e('Allowed words list:', 'wypiekacz'); ?></label>
</th>
<td>
<textarea id="wypiekacz_goodwords" name="wypiekacz_goodwords" rows="5" cols="30"><?php echo esc_html( implode( "\n", get_option( 'wypiekacz_goodwords', array() ) ) ); ?></textarea><br />
<?php _e('Put one word per line', 'wypiekacz'); ?><br /><?php _e('When WyPiekacz will found any word from Forbidden Word List in text, it will check if it part of any word from Allowed Words List (e.g. <b>fly</b> - forbidden, <b>butterfly</b> - allowed)', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Post Thumbnail:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_post_thumbnail"><?php _e('Post thumbnail (Featured image) is required:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_post_thumbnail" name="wypiekacz_post_thumbnail" value="yes" <?php checked( 1, get_option( 'wypiekacz_post_thumbnail' ) ); ?> />
<br /><?php _e('Note: WyPiekacz supports WordPress Featured image by default. You can also provide your own function to check if Post thumbnail is present - see FAQ for more details.', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Rule enforcement:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_links"><?php _e('Enforce max links count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_links" name="wypiekacz_enforce_links" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_links' ) ); ?> /><br /><?php _e('Extra links over limit will be automatically removed', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_link_positions"><?php _e('Enforce link positions:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_link_positions" name="wypiekacz_enforce_link_positions" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_link_positions' ) ); ?> /><br /><?php _e('Links inserted too close to the beginning of post content will be automatically removed', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_title"><?php _e('Enforce max title length:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_title" name="wypiekacz_enforce_title" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_title' ) ); ?> /><br /><?php _e('Too long titles will be automatically truncated', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_add_dots"><?php _e('Add &quot;...&quot; at the end of the truncated text:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_add_dots" name="wypiekacz_enforce_add_dots" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_add_dots' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_cats"><?php _e('Enforce max category count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_cats" name="wypiekacz_enforce_cats" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_cats' ) ); ?> /><br /><?php _e('Extra categories over limit will be automatically removed', 'wypiekacz'); ?><br /><?php _e('Note: If you do not allow to use the default category, WyPiekacz will try to remove it first', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_enforce_tags"><?php _e('Enforce max tag count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_enforce_tags" name="wypiekacz_enforce_tags" value="yes" <?php checked( 1, get_option( 'wypiekacz_enforce_tags' ) ); ?> /><br /><?php _e('Extra tags over limit will be automatically removed', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Invalid Posts:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_dont_save_invalid_post"><?php _e('Do not save posts which do not satisfy all rules:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_dont_save_invalid_post" name="wypiekacz_dont_save_invalid_post" value="yes" <?php checked( 1, get_option( 'wypiekacz_dont_save_invalid_post' ) ); ?> />
<br /><?php _e('When this option is enabled, posts submitted for review or publishing will not be saved if they do not satisfy all rules. This can negatively affect user experience, so enable it if you have to deal with lots of automated spam only.', 'wypiekacz'); ?>
<br /><?php _e('Note: this option does not prevent creation of auto drafts - so far there are too many dependencies in WordPress code to make it work correctly. Autosave and normal post saving as draft (without attempt to publish or send it for review) will work too.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_delete_orphaned_drafts"><?php _e('Delete abandoned post drafts interval (days):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_delete_orphaned_drafts" name="wypiekacz_delete_orphaned_drafts" value="<?php echo esc_attr( get_option( 'wypiekacz_delete_orphaned_drafts' ) ); ?>" /><br /><?php _e('Default is 0 days - disabled. When Trash is enabled, drafts will be moved to Trash.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_force_delete_orphaned_drafts"><?php _e('Force deletion of abandoned post drafts:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_force_delete_orphaned_drafts" name="wypiekacz_force_delete_orphaned_drafts" value="yes" <?php checked( 1, get_option( 'wypiekacz_force_delete_orphaned_drafts' ) ); ?> /><br /><?php _e('Do not move abandoned post drafts to Trash - delete them immediately.', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Build-in WordPress functionalities:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_autosave_interval"><?php _e('Post autosave interval (seconds):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_autosave_interval" name="wypiekacz_autosave_interval" value="<?php echo esc_attr( get_option( 'wypiekacz_autosave_interval' ) ); ?>" /><br /><?php _e('Default is 60 seconds. Enter 0 to disable autosave.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_post_revisions"><?php _e('Maximum post revisions count:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_post_revisions" name="wypiekacz_post_revisions" value="<?php echo esc_attr( get_option( 'wypiekacz_post_revisions' ) ); ?>" /><br /><?php _e('Default is -1 (no limit). Enter 0 to disable post revisions.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_empty_trash_days"><?php _e('Empty trash interval (days):', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_empty_trash_days" name="wypiekacz_empty_trash_days" value="<?php echo esc_attr( get_option( 'wypiekacz_empty_trash_days' ) ); ?>" /><br /><?php _e('Default is 30 days. Enter 0 to disable Trash.', 'wypiekacz'); ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Special:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_limit_for_all"><?php _e('Check above rules for Editors and Administrators:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_limit_for_all" name="wypiekacz_limit_for_all" value="yes" <?php checked( 1, get_option( 'wypiekacz_limit_for_all' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_allow_skip_rules"><?php _e('Allow Editors and Administrators to skip rule check:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_allow_skip_rules" name="wypiekacz_allow_skip_rules" value="yes" <?php checked( 1, get_option( 'wypiekacz_allow_skip_rules' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_right_now_stats"><?php _e('Show number of Drafts and Pending Posts in Dashboard:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_right_now_stats" name="wypiekacz_right_now_stats" value="yes" <?php checked( 1, get_option( 'wypiekacz_right_now_stats' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_post_menu_links"><?php _e('Add links to Draft and Pending Posts lists to Posts menu:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_post_menu_links" name="wypiekacz_post_menu_links" value="yes" <?php checked( 1, get_option( 'wypiekacz_post_menu_links' ) ); ?> />
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Supported post types:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<?php _e('Check rules for following post types:', 'wypiekacz'); ?>
</th>
<td>
<?php 
global $wp_version;
if ( $this->has_wp_30 ) {
	$post_types = $this->get_post_types();
	$selected_post_types = get_option( 'wypiekacz_post_types' );
	foreach ( $post_types as $post_type => $post_type_label ) {
?>
<label><input type="checkbox" id="wypiekacz_post_types_<?php echo esc_attr( $post_type ); ?>" name="wypiekacz_post_types[]" value="<?php echo esc_attr( $post_type );  ?>" <?php checked( true, in_array( $post_type, $selected_post_types ) ); ?> /> <?php echo esc_html( $post_type_label ); ?></label><br />
<?php
	}
} else {
	printf( __('Custom Post Types are supported starting from WordPress 3.0 (you are using version %s). For earlier version WyPiekacz supports Posts only.', 'wypiekacz'), $wp_version );
} ?>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Default post template:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_def_title"><?php _e('Title:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" size="59" id="wypiekacz_def_title" name="wypiekacz_def_title" value="<?php echo esc_attr( get_option( 'wypiekacz_def_title' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_def_text"><?php _e('Text:', 'wypiekacz'); ?></label>
</th>
<td>
<textarea rows="5" cols="57" id="wypiekacz_def_text" name="wypiekacz_def_text"><?php echo esc_textarea( get_option( 'wypiekacz_def_text' ) ); ?></textarea>
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Email notifications:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_new_login_email"><?php _e('Send notification of new login creation to admin:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_new_login_email" name="wypiekacz_new_login_email" value="yes" <?php checked( 1, get_option( 'wypiekacz_new_login_email' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_pass_reset_email"><?php _e('Send notification of password resets to admin:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_pass_reset_email" name="wypiekacz_pass_reset_email" value="yes" <?php checked( 1, get_option( 'wypiekacz_pass_reset_email' ) ); ?> />
</td>
</tr>

<tr><th colspan="2"><h3><?php _e('Account locking:', 'wypiekacz'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
&nbsp;
</th>
<td>
<?php printf( __('Current WyPiekacz version is integrated with the <a href="%s" target="_blank">User Locker</a> plugin (version 1.2 or newer). It can lock or disable user account when he/she will send too many invalid posts for review or attempt to publish it and abandon them. Multiple failed attempts for the same post in a row are counted only once. Every successful submission resets the counter. Users will be able to unlock locked account by requesting new password or asking admin for help; disabled accounts can be enabled by admin only.', 'wypiekacz'), 'http://wordpress.org/extend/plugins/user-locker/' ); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
&nbsp;
</th>
<td>
<?php 
if ( $this->has_user_locker ) {
	_e('User Locker 1.2+ is installed and active.', 'wypiekacz');
} else {
	_e('User Locker 1.2+ is not active. You need to install and activate it first.', 'wypiekacz');
}
?>
</td>
</tr>

<?php if ( $this->has_user_locker ): ?>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_lock_account"><?php _e('Enable account locking/disabling:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_lock_account" name="wypiekacz_lock_account" value="yes" <?php checked( 1, get_option( 'wypiekacz_lock_account' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_lock_account_after"><?php _e('Maximum allowed number of abandoned invalid posts:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="4" size="10" id="wypiekacz_lock_account_after" name="wypiekacz_lock_account_after" value="<?php echo esc_attr( get_option( 'wypiekacz_lock_account_after' ) ); ?>" /><br /><?php _e('Multiple failed attempts for the same post in a row are counted only once. Every successful submission resets the counter.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_lock_method"><?php _e('Account lock method:', 'wypiekacz'); ?></label>
</th>
<td>
<?php $wypiekacz_lock_method = get_option( 'wypiekacz_lock_method' ); ?>
<select id="wypiekacz_lock_method" name="wypiekacz_lock_method">
<option value="0" <?php selected( $wypiekacz_lock_method, 0 ); ?>><?php _e('Lock', 'wypiekacz'); ?></option>
<option value="1" <?php selected( $wypiekacz_lock_method, 1 ); ?>><?php _e('Disable', 'wypiekacz'); ?></option>
</select><br /><?php _e('Users will be able to unlock locked account by requesting new password or asking admin for help. Disabled accounts can be enabled by admin only.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_lock_reason"><?php _e('Lock/Disable reason:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="text" maxlength="500" size="80" id="wypiekacz_lock_reason" name="wypiekacz_lock_reason" value="<?php echo esc_attr( get_option( 'wypiekacz_lock_reason' ) ); ?>" />
<br /><?php _e('Reason text can be displayed after unsuccessful login attempt, and on User List. Make sure you enabled appropriate options in User Locker settings.', 'wypiekacz'); ?>
<br /><?php _e('Note: start text with \'@\' (AT sign) to keep it private.', 'wypiekacz'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="wypiekacz_lock_show_details"><?php _e('Show details on User List:', 'wypiekacz'); ?></label>
</th>
<td>
<input type="checkbox" id="wypiekacz_lock_show_details" name="wypiekacz_lock_show_details" value="yes" <?php checked( 1, get_option( 'wypiekacz_lock_show_details' ) ); ?> />
<br /><?php _e('Enable this option to add extra column to User List with Bad Posts count and last Post ID.', 'wypiekacz'); ?>
</td>
</tr>

<?php endif; /* if ( $this->has_user_locker ): */ ?>

</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save settings', 'wypiekacz'); ?>" /> 
</p>

</form>
</div>
<?php
	}
	
	// Sanitization functions
	function sanitize_01( $value ) {
		if ($value == 'yes') {
			$value = 1;
		} else {
			$value = (int)$value;
			if ( $value < 0 ) {
				$value = 0;
			} elseif ( $value > 1 ) {
				$value = 1;
			}
		}
		return $value;
	}
	
	function sanitize_nonnegative( $value ) {
		$value = (int)$value;
		if ( $value < 0 ) {
			$value = 0;
		}
		return $value;
	}
	
	function sanitize_nonnegative_or_minus1( $value ) {
		$value = (int)$value;
		if ( $value < -1 ) {
			$value = -1;
		}
		return $value;
	}
	
	function sanitize_positive( $value ) {
		$value = (int)$value;
		if ( $value <= 0 ) {
			$value = 1;
		}
		return $value;
	}
	
	function sanitize_stringlist( $value ) {
		$value = explode( "\n", (string)$value );
		$ret = array();
		$has_mb = function_exists( 'mb_convert_case' );
		$encoding = get_option( 'blog_charset' );
		foreach ( $value as $val ) {
			$val = trim( $val );
			if ( $val != '' ) {
				if ( $has_mb ) {
					$val = mb_convert_case( $val, MB_CASE_LOWER, $encoding );
				}
				if ( !in_array( $val, $ret ) ) {
					$ret[] = $val;
				}
			}
		}
		
		return $ret;
	}
	
	// Get list of post types which are public and supports editor
	function get_post_types() {
		if ( $this->has_wp_30 ) {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			$types = array();
			foreach ( $post_types as $post_type => $post_type_obj ) {
				if ( post_type_supports( $post_type, 'editor' ) ) {
					$types[$post_type] = $post_type_obj->label;
				}
			}
		} else {
			$types = array( 'post' => __('Posts', 'wypiekacz') );
		}
		
		return $types;
	}
	
	// Sanitize list of post types
	function sanitize_post_types( $types ) {
		// For pre-WP 3.0 return predefined value - otherwise WP will save empty array when
		// options are updated, causing mysterious problem after upgrade to WP 3.0+.
		if ( !$this->has_wp_30 ) {
			return array( 'post' );
		}
		
		$post_types = $this->get_post_types();
		$ret = array();
		foreach ( $types as $type ) {
			if ( isset( $post_types[$type] ) ) {
				$ret[] = $type;
			}
		}
		return $ret;
	}
} // End Class

// Add options
add_option( 'wypiekacz_min_len', 1000 ); // Minimum post length (characters)
add_option( 'wypiekacz_min_len_words', 0 ); // Minimum post length (words)
add_option( 'wypiekacz_min_links', 0 ); // Minimum links per post
add_option( 'wypiekacz_max_links', 3 ); // Maximum links per post
add_option( 'wypiekacz_link_after', 0 ); // First link allowed after N characters
add_option( 'wypiekacz_link_after_words', 0 ); // First link allowed after N words
add_option( 'wypiekacz_min_title_len', 5 ); // Minimum title length (characters)
add_option( 'wypiekacz_min_title_len_words', 0 ); // Minimum title length (words)
add_option( 'wypiekacz_max_title_len', 80 ); // Maximum title length (characters); 0 = disable check
add_option( 'wypiekacz_max_title_len_words', 0 ); // Maximum title length (words); 0 = disable check
add_option( 'wypiekacz_use_def_cat', 1 ); // Posts can be added to the default category
add_option( 'wypiekacz_min_cats', 1 ); // Minimum category count
add_option( 'wypiekacz_max_cats', 3 ); // Maximum category count
add_option( 'wypiekacz_min_tags', 1 ); // Minimum tag count
add_option( 'wypiekacz_max_tags', 5 ); // Maximum tag count
add_option( 'wypiekacz_limit_for_all', 1 ); // Check ruled for Editors and Administrators
add_option( 'wypiekacz_def_title', '' ); // Default post title
add_option( 'wypiekacz_def_text', '' ); // Default post text
add_option( 'wypiekacz_new_login_email', 1 ); // Send notification of new login creation to admin
add_option( 'wypiekacz_pass_reset_email', 1 ); // Send notification of password resets to admin
add_option( 'wypiekacz_right_now_stats', 1 ); // Show number of Drafts and Pending Posts in Dashboard
add_option( 'wypiekacz_post_menu_links', 1 ); // Add Drafts/Pending links to Posts menu
add_option( 'wypiekacz_badwords', array() ); // List of forbidden words
add_option( 'wypiekacz_check_badwords_title', 0 ); // Check for forbidden words in title
add_option( 'wypiekacz_check_badwords_content', 0 ); // Check for forbidden words in content
add_option( 'wypiekacz_check_badwords_tags', 0 ); // Check for forbidden words in tags
add_option( 'wypiekacz_goodwords', array() ); // List of allowed words
add_option( 'wypiekacz_post_thumbnail', 0 ); // Check post thumbnail
add_option( 'wypiekacz_enforce_links', 0 ); // Enforce max link count
add_option( 'wypiekacz_enforce_link_positions', 0 ); // Enforce link positions
add_option( 'wypiekacz_enforce_title', 0 ); // Enforce max title length
add_option( 'wypiekacz_enforce_add_dots', 1 ); // Enforce max title length
add_option( 'wypiekacz_enforce_cats', 0 ); // Enforce max category count
add_option( 'wypiekacz_enforce_tags', 0 ); // Enforce max tag count
add_option( 'wypiekacz_allow_skip_rules', 1 ); // Allow Editors and Administrators to skip rule check
add_option( 'wypiekacz_post_types', array( 'post' ) ); // List of post types supported by default
add_option( 'wypiekacz_dont_save_invalid_post', 0 ); // Save post even it has not passed validation
add_option( 'wypiekacz_autosave_interval', 60 ); // Post autosave interval (60 = default, 0 to disable (need special handling))
add_option( 'wypiekacz_post_revisions', -1 ); // Post revisions count (-1/true = default, 0/false to disable)
add_option( 'wypiekacz_empty_trash_days', 30 ); // Empty trash days (30 = default, 0 to disable)
add_option( 'wypiekacz_delete_orphaned_drafts', 0 ); // Delete orphaned_drafts interval (days, 0 = disable)
add_option( 'wypiekacz_force_delete_orphaned_drafts', 0 ); // Force delete orphaned_drafts (otherwise they may end in Trash)
add_option( 'wypiekacz_lock_account', 0 ); // Lock/disable user account when user submits too many invalid posts for review/publish and do not correct them
add_option( 'wypiekacz_lock_account_after', 5 ); // How many invalid posts can be submitted before locking user
add_option( 'wypiekacz_lock_method', 0 ); // 0 - lock account, 1 - disable account
add_option( 'wypiekacz_lock_reason', '' ); // Lock reason
add_option( 'wypiekacz_lock_show_details', 1 ); // Add extra column to User List with details

$wp_wypiekacz = new WyPiekacz();
// TODO: try to call this from 'init' hook (at this point user is authenticated), and do not enforce rules if checking is skipped for user or by option
$wp_wypiekacz->enforce_rules_POST();

// Redefine default WordPress functions in order to get control over sending extra emails to admin
if ( !get_option('wypiekacz_new_login_email') && !function_exists( 'wp_new_user_notification' ) ) {
	// Notify user of his new username and password (skip admin)
	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
		// Allow other plugins to override wp_new_user_notification() too
		if ( !apply_filters( 'wypiekacz_send_email_to_new_user', true, $user_id, $plaintext_pass ) ) {
			return;
		}
		
		if ( empty($plaintext_pass) )
			return;
		
		$user = new WP_User($user_id);
		
		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);
		
		$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
		$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		$message .= wp_login_url() . "\r\n";
		
		@wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);
	}
}

if ( !get_option('wypiekacz_pass_reset_email') && !function_exists( 'wp_password_change_notification' ) )
{
	// Don't send password change notifications to admin
	function wp_password_change_notification(&$user) {
	}
}

// Define some special defines
if ( !defined( 'AUTOSAVE_INTERVAL' ) ) { // 60 = default
	$val = get_option( 'wypiekacz_autosave_interval' );
	if ( $val == 0 ) {
		// Use some big value to make sure autosave will not start if autosave script will be loaded (should not be)
		$val = 999999999;
	}
	define( 'AUTOSAVE_INTERVAL', $val );
}

if ( !defined( 'WP_POST_REVISIONS' ) ) { // -1 = default
	$val = get_option( 'wypiekacz_post_revisions' );
	define( 'WP_POST_REVISIONS', $val );
}

if ( !defined( 'EMPTY_TRASH_DAYS' ) ) { // 30 = default
	$val = get_option( 'wypiekacz_empty_trash_days' );
	define( 'EMPTY_TRASH_DAYS', $val );
}

// Add functions from WP2.8 for previous WP versions
if ( !function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return wp_specialchars( $text );
	}
}
if ( !function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return attribute_escape( $text );
	}
}

// Add functions from WP2.9 for previous WP versions
if ( !function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags($string, $remove_breaks = false) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags($string);
	
		if ( $remove_breaks )
			$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	
		return trim($string);
	}
}

// Add functions from WP3.1 for previous WP versions
if ( !function_exists( 'esc_textarea' ) ) {
	function esc_textarea( $text ) {
		$safe_text = htmlspecialchars( $text, ENT_QUOTES );
		return apply_filters( 'esc_textarea', $safe_text, $text );
	}
}

} // END

?>
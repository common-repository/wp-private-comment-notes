<?php
/**
 * Plugin Name: WP Private Comment Notes
 * Plugin URI: https://www.engagewp.com/
 * Description: Add private notes to WordPress comments with an option to email the original commenter
 * Version: 1.0.0
 * Author: Ren Ventura
 * Author URI: https://www.engagewp.com
 * Text Domain: wp-private-comment-notes
 * Domain Path: /languages/
 *
 * License: GPL 2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

/*
	Copyright 2016  Ren Ventura  (email : mail@engagewp.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Private_Comment_Notes' ) ) :

class WP_Private_Comment_Notes {

	private static $instance;

	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Private_Comment_Notes ) ) {
			
			self::$instance = new WP_Private_Comment_Notes;

			self::$instance->constants();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 *	Constants
	 */
	public function constants() {

		// Plugin version
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_VERSION' ) ) {
			define( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_VERSION', '1.0.0' );
		}

		// Plugin file
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_FILE' ) ) {
			define( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_FILE', __FILE__ );
		}

		// Plugin basename
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_BASENAME' ) ) {
			define( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_BASENAME', plugin_basename( WP_PRIVATE_COMMENT_NOTES_PLUGIN_FILE ) );
		}

		// Plugin directory path
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_PATH' ) ) {
			define( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( WP_PRIVATE_COMMENT_NOTES_PLUGIN_FILE )  ) );
		}

		// Plugin directory URL
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_URL' ) ) {
			define( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( WP_PRIVATE_COMMENT_NOTES_PLUGIN_FILE )  ) );
		}

		// Templates directory
		if ( ! defined( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_TEMPLATES_DIR_PATH' ) ) {
			define ( 'WP_PRIVATE_COMMENT_NOTES_PLUGIN_TEMPLATES_DIR_PATH', WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_PATH . 'templates/' );
		}
	}

	/**
	 *	Action/filter hooks
	 */
	public function hooks() {
		add_action( 'plugins_loaded', array( $this, 'localization' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes_comment', array( $this, 'register_comments_metabox' ) );
		add_action( 'wp_ajax_add_comment_note', array( $this, 'process_note' ) );
		add_action( 'wp_ajax_delete_comment_note', array( $this, 'delete_note' ) );
	}

	/**
	 *	Localization
	 */
	public function localization() {
		load_plugin_textdomain( 'wp-private-comment-notes', false, trailingslashit( WP_LANG_DIR ) . 'plugins/' );
		load_plugin_textdomain( 'wp-private-comment-notes', false, WP_PRIVATE_COMMENT_NOTES_PLUGIN_BASENAME . '/languages/' );
	}

	/**
	 *	Add the scripts
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( $screen->id === 'comment' && $this->can_edit_notes() ) {
			wp_enqueue_script( 'wp-private-comment-notes-script', WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_URL . 'assets/js/notes-ajax.js', array( 'jquery' ), WP_PRIVATE_COMMENT_NOTES_PLUGIN_VERSION, true );
			wp_enqueue_style( 'wp-private-comment-notes-style', WP_PRIVATE_COMMENT_NOTES_PLUGIN_DIR_URL . 'assets/css/notes.css', '', WP_PRIVATE_COMMENT_NOTES_PLUGIN_VERSION );
		}
	}

	/**
	 *	Add meta box to comment.php?action=editcomment
	 *
	 *	@param $comment (object)
	 */
	public function register_comments_metabox( $comment ) {
		if ( $this->can_edit_notes() ) {
			add_meta_box( 'wp-private-comment-notes', __( 'Comment Notes', 'wp-private-comment-notes' ), array( $this, 'comments_metabox' ), 'comment', 'normal', 'high', array( $comment ) );
		}
	}

	/**
	 *	Render the comments meta box
	 *
	 *	@param $comment (object)
	 */
	public function comments_metabox( $comment ) {

		$order = apply_filters( 'wp_private_comment_notes_display_order', 'DESC' );

		$notes = $this->get_notes( $comment->comment_ID, $order );

		if ( empty( $notes ) || ! is_array( $notes ) ) {

			include_once 'templates/no-notes.php';

		} else {

			include_once 'templates/list-notes.php';
		}

		include_once 'templates/note-form.php';
	}

	/**
	 *	Process the incoming comment note (AJAX)
	 *	Sends a JSON response
	 *
	 *	@return void
	 */
	public function process_note() {

		// Security check
		if ( ! $this->can_edit_notes() || ! isset( $_POST['_wp_private_comment_notes_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_private_comment_notes_nonce'], '_wp_private_comment_notes_nonce' ) ) {
			die( __( 'Unauthorized', 'wp-private-comment-notes' ) );
		}

		$response = array();

		// POST info
		$comment_id = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : false;
		$note = isset( $_POST['note'] ) ? strip_tags( $_POST['note'] ) : '';
		
		// Maybe send an email
		$send = false;
		if ( isset( $_POST['send'] ) && $_POST['send'] == 'true' ) {
			$send = true;
		}

		// Comment object
		$comment = get_comment( $comment_id );

		// Bail if comment does not exist
		if ( ! $comment ) {
			die( __( 'No comment exists', 'wp-private-comment-notes' ) );
		}

		$key = time();
		$date = date( get_option( 'date_format' ), $key );
		$user_id = get_current_user_id();

		// Get the notes of the comment
		$notes = $this->get_notes( $comment_id );

		// Prepare new note
		$notes[$key] = array(
			'date' => $date,
			'user_id' => $user_id,
			'note' => $note,
			'email_commenter' => (int) $send
		);

		// Update the comment meta
		$updated = update_comment_meta( $comment_id, 'wp_private_comment_notes', $notes );

		if ( $updated ) { // Success

			if ( apply_filters( 'wp_private_comment_notes_should_send_email', $send ) ) {

				// Send email to original commenter
				$this->send_email( $notes[$key], $comment );
			}

			// Send a formatted note in response
			$this->send_response( 'success', $this->format_note( $key, $notes[$key] ) );

		} else { // Failed

			$this->send_response( 'fail', __( 'Your note could not be saved.', 'wp-private-comment-notes' ) );
		}
	}

	/**
	 *	Delete a note (AJAX request)
	 *
	 *	@return void
	 */
	public function delete_note() {

		// Security check
		if ( ! $this->can_edit_notes() || ! isset( $_POST['_wp_private_comment_notes_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_private_comment_notes_nonce'], '_wp_private_comment_notes_nonce' ) ) {
			die( __( 'Unauthorized', 'wp-private-comment-notes' ) );
		}

		$response = array();

		// POST info
		$comment_id = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : false;
		$note_key = isset( $_POST['note_key'] ) ? strip_tags( $_POST['note_key'] ) : '';

		// Comment object
		$comment = get_comment( $comment_id );

		// Bail if comment does not exist
		if ( ! $comment ) {
			die( __( 'No comment exists', 'wp-private-comment-notes' ) );
		}

		// Get the notes of the comment
		$notes = $this->get_notes( $comment_id );

		// Remove note from array
		unset( $notes[$note_key] );

		if ( count( $notes ) == 0 ) {

			// Delete comment meta if no more notes
			$updated = delete_comment_meta( $comment_id, 'wp_private_comment_notes' );

		} else {

			// Update the comment meta
			$updated = update_comment_meta( $comment_id, 'wp_private_comment_notes', $notes );
		}

		if ( $updated ) {

			// Send a formatted note in response
			$this->send_response( 'success', __( 'Your note was removed.', 'wp-private-comment-notes' ) );

		} else {

			$this->send_response( 'fail', __( 'Your note could not be removed.', 'wp-private-comment-notes' ) );
		}
	}

	/**
	 *	Prepare and send an email to the commenter when triggered
	 *
	 *	@param $note (array)
	 *	@param $comment (object)
	 *	@return void
	 */
	public function send_email( $note, $comment ) {

		// Info about the comment
		$content = $comment->comment_content;
		$link = get_comment_link( $comment );

		// Info about the commenter
		$email = $comment->comment_author_email;
		$author = $comment->comment_author;

		// Post title of comment
		$title = get_the_title( $comment->comment_post_ID );

		// Email subject
		$subject = apply_filters( 'wp_private_comment_notes_email_subject', __( 'Your Comment on ', 'wp-private-comment-notes' ) . $title, $note, $comment );

		// Email message
		ob_start();
		include_once 'templates/email.php';
		$message = apply_filters( 'wp_private_comment_notes_email_message', ob_get_clean(), $note, $comment );

		// "From" info for the email headers
		$from_user = get_userdata( $note['user_id'] );
		$from_email = apply_filters( 'wp_mail_from', $from_user->user_email );
		$from_name = apply_filters( 'wp_mail_from_name', $from_user->display_name );

		// Email headers
		$default_headers = array(
			'Content-type: text/html',
			"From: $from_name <$from_email>"
		);
		$filter_headers = apply_filters( 'wp_private_comment_notes_email_headers', array(), $note, $comment, $from_user );
		$headers = wp_parse_args( $filter_headers, $default_headers );

		// Send the email
		wp_mail( $email, $subject, $message, $headers );
	}

	/**
	 *	Format note for display in the admin (edit comment) (ul>li)
	 *
	 *	@param $note (array) - Note text, user ID and date
	 *	@return (string) - Formatted note in <li> tag
	 */
	public function format_note( $key, $note ) {

		ob_start();
		
		include 'templates/note-li.php';
		
		$output = ob_get_clean();
		
		return $output;
	}

	/**
	 *	Send a JSON response
	 *
	 *	@param $status (string)
	 *	@param $message (string)
	 *	@return void
	 */
	public function send_response( $status, $message ) {
		wp_send_json( array( 'status' => $status, 'message' => $message ) );
	}

	/**
	 *	Get notes for a given comment
	 *
	 *	@param $comment_id (int)
	 *	@param $order (string) - ASC or DESC
	 *	@return (array) - Notes for comment
	 */
	public function get_notes( $comment_id, $order = 'DESC' ) {

		$notes = get_comment_meta( $comment_id, 'wp_private_comment_notes', true );

		if ( ! $notes ) {
			$notes = array();
		}

		switch ( $order ) {
			case 'ASC':
				ksort( $notes );
				break;
			
			case 'DESC':
			default:
				krsort( $notes );
				break;
		}

		return $notes;
	}

	/**
	 *	Check permissions
	 *
	 *	@param $user_id (int) - A user's ID to check for permission
	 *	@return (boolean) - True if user can edit notes, else false
	 */
	public function can_edit_notes( $user_id = null ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$capability = apply_filters( 'wp_private_comment_notes_capability', 'moderate_comments', $user_id );

		return user_can( $user_id, $capability );
	}
}

endif;

/**
 *	Main function
 *	@return object WP_Private_Comment_Notes instance
 */
function WP_Private_Comment_Notes() {
	return WP_Private_Comment_Notes::instance();
}

/**
 *	Kick off!
 */
WP_Private_Comment_Notes();
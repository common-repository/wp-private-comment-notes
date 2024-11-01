<p>
	<label for="wp_private_comment_notes_note"><?php _e( 'Add your note:', 'wp-private-comment-notes' ); ?></label>
	<textarea id="wp_private_comment_notes_note" class="large-text code" rows="10" cols="50"></textarea>
</p>

<p>
	<input type="checkbox" id="wp_private_comment_notes_email_commenter" value="send">
	<label for="wp_private_comment_notes_email_commenter"><?php _e( 'Email this note to the comment author?', 'wp-private-comment-notes' ); ?></label>
</p>

<p>
	<button id="wp_private_comment_notes_submit_button" class="button button-primary"><?php _e( 'Add Note to Comment', 'wp-private-comment-notes' ); ?></button>
	<img src="<?php echo admin_url( '/images/spinner-2x.gif' ); ?>" alt="spinner" id="wp_private_comment_notes_spinner" style="display:none; width:20px;">
	<input type="hidden" id="_wp_private_comment_notes_comment_id" value="<?php echo (int) esc_attr( $comment->comment_ID ); ?>">
	<?php wp_nonce_field( '_wp_private_comment_notes_nonce', '_wp_private_comment_notes_nonce' ); ?>
</p>

<p id="wp_private_comment_notes_error" style="display:none; border-left: 4px solid #dc3232; padding: 12px;"></p>
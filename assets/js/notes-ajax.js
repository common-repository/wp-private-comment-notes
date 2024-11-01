jQuery(document).ready(function($) {
	
	// Add note
	$('#wp-private-comment-notes').on('click', '#wp_private_comment_notes_submit_button', function(e) {

		e.preventDefault();

		var notes_list = $('#wp_private_comment_notes_list'),
			notes_count = notes_list.size(),
			spinner = $('#wp_private_comment_notes_spinner'),
			button = $('#wp_private_comment_notes_submit_button'),
			note_textarea = $('#wp_private_comment_notes_note'),
			error_container = $('#wp_private_comment_notes_error'),
			checkbox = $('#wp_private_comment_notes_email_commenter');

		spinner.show();
		button.attr('disabled', 'disabled');
		
		data = {
			'action': 'add_comment_note',
			'_wp_private_comment_notes_nonce': $('#_wp_private_comment_notes_nonce').val(),
			'comment_id': $('#_wp_private_comment_notes_comment_id').val(),
			'note': note_textarea.val(),
			'send': checkbox.is(':checked') ? 'true' : 'false'
		};

		$.post( ajaxurl, data, function(response){
			
			if ( response.status == 'success' ) {
				
				if ( notes_count <= 1 ) {
					$('#wp_private_comment_notes_no_notes').hide();
					notes_list.show();
				}

				note_textarea.val('');
				checkbox.prop('checked', false);
				notes_list.prepend(response.message);

			} else {

				error_container.text(response.message).fadeIn(function() {

					setTimeout(function(){
						error_container.fadeOut(function(){
							error_container.text('');
						});
					}, 5000 );

				});

			}

			spinner.hide();
			button.removeAttr('disabled');

		});

		return false;

	});

	// Delete note
	$('#wp_private_comment_notes_list').on('click', '.wp-private-comment-note-delete', function(e) {

		e.preventDefault();

		var button = $(this),
			notes_list = $('#wp_private_comment_notes_list'),
			notes_count = notes_list.size(),
			note_key = button.data('note-key'),
			spinner = button.siblings('.delete-note-spinner'),
			error_container = $('#wp_private_comment_notes_error');

		spinner.show();
		button.attr('disabled', 'disabled');
		
		data = {
			'action': 'delete_comment_note',
			'_wp_private_comment_notes_nonce': $('#_wp_private_comment_notes_nonce').val(),
			'comment_id': $('#_wp_private_comment_notes_comment_id').val(),
			'note_key': note_key
		};

		$.post( ajaxurl, data, function(response){

			if ( response.status == 'success' ) {

				button.closest('.wp-private-comment-note').fadeOut(function(){
					$(this).remove();
				});

			} else {

				error_container.text(response.message).fadeIn(function() {

					setTimeout(function(){
						error_container.fadeOut(function(){
							error_container.text('');
						});
					}, 5000 );

				});
			}

			spinner.hide();
			button.removeAttr('disabled');

		});

		return false;

	});

});
<li class="wp-private-comment-note">

	<?php if ( $note['email_commenter'] ) : ?>
		<p class="wp-private-comment-note-emailed"><svg height="32" id="svg4152" version="1.1" viewBox="0 0 32 32" width="32" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg"><defs id="defs4154"/><g id="layer1"><g id="g4534" style="fill:none;stroke:#cccccc;stroke-width:0.74628419;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.1391099,1.1391099,-1.1391099,1.1391099,96.037561,29.825095)"><path d="m -41.2,17.726545 6.8,15.873454 -6.8,-3.00913 -6.8,3.00913 z" id="rect4529" style="opacity:1;fill:none;fill-opacity:1;stroke:#cccccc;stroke-width:0.74628419;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"/><path d="m -41.201027,18.577923 -0.03119,12.066206" id="path4532" style="fill:none;fill-rule:evenodd;stroke:#cccccc;stroke-width:0.74628419;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"/></g></g></svg>
		<span class="screen-reader-text"><?php _e( 'This note was emailed to the commenter.', 'wp-private-comment-notes' ); ?></span></p>
	<?php endif; ?>

	<p class="wp-private-comment-notes-note-header"><?php echo $note['date']; ?> (<?php echo get_userdata( $note['user_id'] )->user_login; ?>):</p>

	<p class="wp-private-comment-notes-note-text"><?php echo nl2br( $note['note'] ); ?></p>

	<button class="button button-secondary wp-private-comment-note-delete" data-note-key="<?php echo $key; ?>"><?php _e( 'Delete note', 'wp-private-comment-notes' ); ?></button>
	<img src="<?php echo admin_url( '/images/spinner-2x.gif' ); ?>" alt="spinner" class="delete-note-spinner" style="display:none; width:20px;">

</li>
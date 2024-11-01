<ul id="wp_private_comment_notes_list">

	<?php

		foreach ( $notes as $key => $note ) {
			echo $this->format_note( $key, $note );
		}
		
	?>

</ul>
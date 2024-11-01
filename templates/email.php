<div>
	<blockquote><?php echo $content; ?></blockquote>
</div>

<div>
	<p><?php echo __( 'Hello ', 'wp-private-comment-notes' )  . $author; ?></p>
	<p><?php echo $note['note']; ?></p>
	<p><a href="<?php echo $link; ?>"><?php echo $title; ?></a></p>
</div>
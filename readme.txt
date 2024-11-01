=== WP Private Comment Notes ===

Contributors: renventura
Tags: comments, notes, comment meta
Tested up to: 5.0
Stable tag: 1.0
License: GPL 2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php

Add private notes to WordPress comments with an option to email the original commenter.

== Description ==

WP Private Comment Notes will let WordPress admins/moderators add and manage private notes for each comment left through the WordPress commenting system. Additionally, each note can be shared with the user who left the original comment.

Comment notes can come in handy when managing popular blogs. WP Private Comment Notes serves as a "note to self" tool for comment moderators, or a private method of communication with the commenter.

== Installation ==

= Automatically =

1. Search for WP Private Comment Notes in the Add New Plugin section of the WordPress admin.
2. Install and activate.

= Manually =

1. Download the zip file, decompress the file and upload the main plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin.

== Screenshots ==

1. The comment.php?action=editcomment (edit comment) page with Comment Notes meta box

== Frequently Asked Questions ==

= How do I use this plugin? =

After activation, a **Comment Notes** meta box is added to the edit page of a comment. From the meta box you can add and remove comment notes. When adding a comment, you have the option to email the contents of the note to the original commenter.

= Who can manage comment notes? =

By default, anyone with the `moderate_comments` capability. This can be changed though a simple filter:

`
add_filter( 'wp_private_comment_notes_capability', 'rv_wp_private_comment_notes_capability', 10, 2 );
/**
 *	Allow only administrators to manage comment notes
 *	@param $capability (string) - WordPress capability
 *	@param $user_id (int) - User ID to check
 *	@return $capability
 */
function rv_wp_private_comment_notes_capability( $capability, $user_id ) {
	$capability = 'manage_options';
	return $capability;
}
`

= Can I customize the email sent to the original commenter? =

Yes. There are a few filters you can use to customize the email's "From Name/Email," subject, message and headers. For more, see the `send_email()` method in wp-private-comment-notes.php.

== Changelog ==

= 1.0 =

* Initial version

== Credits ==

Thanks to [Ana Nirwana](https://www.iconfinder.com/anir) for the cool little message icon.
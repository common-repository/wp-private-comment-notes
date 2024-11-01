# WP Private Comment Notes

WP Private Comment Notes will let WordPress admins/moderators add and manage private notes for each comment left through the WordPress commenting system. Additionally, each note can be shared with the user who left the original comment.

Comment notes can come in handy when managing popular blogs. WP Private Comment Notes serves as a "note to self" tool for comment moderators, or a private method of communication with the commenter.

## Installation ##

__Manually__

1. Download the zip file, unzip it and upload plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions ##

*How do I use this plugin?*

After activation, a **Comment Notes** meta box is added to the edit page of a comment. From the meta box you can add and remove comment notes. When adding a comment, you have the option to email the contents of the note to the original commenter.

*Who can manage comment notes?*

By default, anyone with the `moderate_comments` capability. This can be changed though a simple filter:

```php
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
```

*Can I customize the email sent to the original commenter?*

Yes. There are a few filters you can use to customize the email's "From Name/Email," subject, message and headers. For more, see the `send_email()` method in wp-private-comment-notes.php.


## Bugs ##

If you find an issue, let me know.
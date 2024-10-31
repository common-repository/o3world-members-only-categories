=== O3World Members-Only Categories ===
Contributors: kris-o3world
Tags: categories, users, content, access, assign, public, private, members-only
Requires at least: 3.0.0
Tested up to: 3.3.0
Stable tag: trunk

Designate categories as "members-only" via 'Privacy Settings.' Assign them to users via 'Profile.'

== Description ==

If you'd like to make the content on your site visible only to certain users using categories, look no further!

Designate certain categories as "members-only" via **Privacy Settings**.

An administrator may then assign a user to them via **Profile**.

Only content belonging to "public" categories (any top-level category you do not designate as "members-only"), and categories assigned to the logged-in user, and all subcategories of either, will be visible.

I would like to thank the action hook **pre_get_posts**, without which this plugin would not have been possible.

== Installation ==

1. Upload `o3world-members-only-categories.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the **Plugins** menu in WordPress
1. Within **Settings** -> **Privacy** you'll see a new section titled **Members-Only Categories**. This is a listing of all your top-level categories. Tick the checkbox next to those you would consider "private."
1. To grant a user access to one or more of those categories, tick the checkboxes at the bottom of their **Profile** in a new section also titled **Members-Only Categories**. Only a user who can edit users (administrator) may do this.
1. Log out of WordPress, and log in as that user. The content on your site should now be filtered by "public" and "members-only" categories.

== Frequently Asked Questions ==

= If "User Access Manager" exists, why must this plugin also exist? =

While [**User Access Manager**](http://wordpress.org/extend/plugins/user-access-manager) is a fine plugin, it may be cumbersome to grant access to one or more user groups upon editing each and every post.

By contrast, with this plugin, you need only consider a category as "members-only," and any content created within it will automaticaly be viewable only to those users assigned to it.

If you'd like to redirect a user to a different page upon logging in, instead of **Profile**, you might try [**WordPress Login Redirect**](http://wordpress.org/extend/plugins/wordpress-login-redirect/) or something similar.

== Screenshots ==

1. Within **Settings** -> **Privacy**, Any top-level category may be designated "members-only" .
2. Edit a user's **Profile** to grant access to view content present in any "members-only" category.

== Changelog ==

= 1.03 =
Specify hide_empty argument (set to false) in call to get_categories, so that ALL top-level categories are shown.

= 1.02 =
Added logic to pre_get_posts hook to return $query as-is when $query->query_vars[ 'suppress_filters' ] is set and true.

= 1.01 =
* Add empty callback function 'o3_moc_edit_categories', referenced in add_settings_section( ).

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.03 =
Minor adjustment to show ALL top-level categories in settings panel, even those without posts.

= 1.02 =
Fixed issue with menus, attachments, etc. being affected by pre_get_posts hook.

= 1.01 =
This version addresses a bug with call to add_settings_section( ).

== O3World ==

This plugin was created by kris@o3world.com

We're an online marketing strategy / custom web and mobile development agency in Philadelphia.

Among other things, we love WordPress.

[http://o3world.com](http://o3world.com)


=== Plugin Name ===
Contributors: sirzooro
Tags: admin, Post, spam, plugin, rules, tag, tags, category, categories, title, minimum length, maximum length, content, enforce rules, forbidden, forbidden words, multi-author blog, multiauthor blog, multi-author, multiauthor, wpmu
Requires at least: 2.7
Tested up to: 3.2.9
Stable tag: 2.2

Multi-Author Blog Toolkit - check if posts submitted for review/posted satisfies set of rules, enforce these rules, delete abandoned drafts and more!

== Description ==

This plugin is a Multi-Author Blog Toolkit - it greatly simplifies management of such site. It can check if posts submitted for review/posted satisfies set of rules, enforce these rules, delete abandoned drafts, and more. Below is full list of its features.

WyPiekacz was initially created to check if posts submitted for review and posted satisfies set of rules. Here is full list of rules which it checks now:

* minimum post length (in characters and/or words);
* minimum and maximum number of links;
* position of first link (must be after N initial characters and/or words);
* minimum and maximum length of title (in characters and/or words);
* minimum and maximum number of selected categories;
* WordPress assigns default category if none is selected, so you can forbid this category;
* minimum and maximum number of tags;
* no forbidden words in title, content and/or tags;
* Post thumbnail (Featured image) presence;

When one or more of these rules are not met, appropriate message is displayed and Post Status is changed to Draft.

You can also enable options to enforce following rules:

* maximum link count - extra links over limit will be removed;
* position of links - links inserted too close to the beginning will be removed;
* maximum title length - too long titles will be truncated;
* maximum category count - extra categories will be removed (if you do not allow to use the default category, WyPiekacz will try to remove it first);
* maximum tag count - extra tags will be removed;

By default these rules are checked for everyone. It is possible to disable them for Administrators and Editors. You can also let Administrators and Editors to conditionally disable rule checking for given post.

Rule check results are also displayed on Edit Posts page. This allows to quickly see if post obeys rules or not, and delete ones which violates them without opening.

If you have to deal with lots of post submissions (especially automated ones), you can enable following helpful options:

* do not save post at all (use this option with caution - it can negatively affect user experience. Therefore I recommend to enable it if you have to deal with lots of automated spam only);
* automatically move abandoned Post Drafts to Trash (or delete if Trash is disabled) after given number of days. You can also decide to force deletion without moving to Trash;
* automatically lock or disable user account after he/she will abandon given number of drafts (this function requires the [User Locker](http://wordpress.org/extend/plugins/user-locker/) plugin);

There are also additional features, which are useful for multi-author blogs:

* option to specify default template for post title and contents;
* option to disable emails sent to admin when new user account is created and/or when user resets its password;
* plugin displays number of Drafts and Pending Posts in Dashboard;
* plugin adds links to Drafts and Pending Posts lists to Posts menu, along with Draft/Pending Post counts (like WordPress does for Comments);
* options to configure (and disable too) Post Autosave interval, maximum Post Revisions and empty Trash interval;

If you are using WordPress 3.0 or newer, you can enable rule checking for any post type which supports editor (including custom post types). In earlier WordPress version WyPiekacz supports Posts only.

Plugin authors: you can enhance WyPiekacz by checking custom rules and enforcing them - see FAQ for more details.

At the end I will give you few tips:

* you can use default template of post content to to present most important site rules to your authors;
* add URLs of spammer's sites on Forbidden Words List - their posts will be automatically rejected;

Available translations:

* English
* Polish (pl_PL) - done by me
* Russian (ru_RU) - thanks [M.Comfi](http://www.comfi.com/)
* Belorussian (be_BY) - thanks [ilyuha](http://antsar.info/)
* Dutch (nl_NL) - thanks [Rene](http://wordpresspluginguide.com/)
* Brazilian Portuguese (pt_BR) - thanks [Frank](http://www.mestreseo.com.br/)
* German (de_DE) - thanks [Rian Klijn](http://www.creditriskmanager.com/)
* Italian (it_IT) - thanks [Gianluca Marzaro](http://www.mondonotizie.info/)
* Hebrew (he_IL) - thanks [Sagive SEO](http://www.sagive.co.il/)

[Changelog](http://wordpress.org/extend/plugins/wypiekacz/changelog/)

== Installation ==

1. Upload `wypiekacz` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure and enjoy :)

== Frequently Asked Questions ==

= How to use Forbidden Word List and Allowed Word List? =

WyPiekacz first checks if every word from Forbidden Word List is in text. This check is very simple, so forbidden word *fly* will be matched to *butterfly* word too. In order to avoid this, you should put allowed words on Allowed Word List. WyPiekacz uses the same simple matching method, so you do not have to enter whole words (this is especially useful if your language allows to decline words in many ways). Please make sure only that forbidden words are always contained in words on Allowed Word List (in other words, entering *butterf* will not work - you have to enter *butterfly*).

= Why this plugin is called "WyPiekacz"? =

You may heard about Presell Pages - content pages placed on other sites, with link(s) leading to your site(s). In Poland this idea evolved into separate sites (WordPress is the preferred script), where everyone can post his or her post with links. Of course there are different Presell Pages - some of them accepts every post and are just full of junk, but there are also ones where posts are thoroughly moderated before publishing. WyPiekacz plugin greatly simplifies moderation tasks for the latter ones.

"Presell Page" is not the only name used to call them - another popular name is just "Presell", or similarly pronounced Polish word "Precel" (eng. pretzel). This last name is also a source of other derived names for some tools - they have names referring to pretzels or baking. "WyPiekacz" also follows this convention - it means "baker" - i.e. "tool used for baking".

= How can I integrate my plugin with WyPiekacz? =

WyPiekacz provides few hooks which you can use for integration purposes. Here is the list:

* `wypiekacz_check_post` - this filter is called after all build-in rules have been checked. You can use it to check additional rules. As a first parameter it gets array of errors - each row is an array with language-independent key at index 0 and error message at index 1. There are also additional parameters: post content, post title, and post data (either POST array or Post object);
* `wypiekacz_check_thumbnail` - this filter is called after WyPiekacz has checked Post thumbnail presence. As a first parameter it gets thumbnail check result (bool). There are also additional parameters - Post ID and Post Data (either POST array or Post object);
* `wypiekacz_enforce_rules` - this filter is called after WyPiekacz enforced configured rules. It gets one parameter - array with post data. You can use it to enforce additional rules;

= How can I check if my custom Post thumbnail is present? =

You can use the `wypiekacz_check_thumbnail` filter to do this. Here is example how to do this using the [Get The Image](http://wordpress.org/extend/plugins/get-the-image/) plugin:

`
function check_my_custom_post_thumbnail( $check, $post_id ) {
	if ( $post_id <= 0 ) {
		return false;
	}
	return !empty( get_the_image( array(
		'post_id' => $post_id,
		'attachment' => false,
		'echo' => false,
	) ) );
}
add_filter( 'wypiekacz_check_thumbnail', 'check_my_custom_post_thumbnail', 10, 2);
`

== Screenshots ==

1. Error message after trying to send for review or publish post which does not satisfy the rules.
2. Additional column with post check results on Edit Posts page.

== Changelog ==

= 2.2 =
* Added option to check badwords in tags;
* Added options to check mimimum char and word count before first link;
* Added option to remove links placed too early in post content;
* Added option to check minimum number of links;
* Added options to configure (or disable) Autosave interval, maximum number of Post Revisions and Empty Trash interval;
* Added option to automatically trash/delete abandoned Post Drafts;
* Added option to prevent saving invalid posts (use with caution - enable if you have to deal with lots of automated spam only);
* Added option to lock or disable user account when user submits too many invalid posts for review/publish and do not correct them (this feature requires User Locker plugin);
* Added option to check Post thumbnail (Featured image) presence;
* Added links to Drafts and Pending post lists to Posts menu;
* Added filters and public functions so other plugins can integrate itself with WyPiekacz (see FAQ for more details);
* Increased field size for maximum links count to 4 digits;
* Fix: do not process post revisions and auto-drafts;
* Fix: WyPiekacz stops working for posts after upgrading WordPress 2.9.x or older to WP 3.0+ (if you have experienced this, go to WyPiekacz options and select Posts in Supported post types section);
* Added Hebrew translation (thanks Sagive SEO);
* Marked plugin as tested with WP 3.2

= 2.1.1 =
* Fix: categories count is calculated incorrectly in WP 3.0 (one was added always);
* Fix: Skip rule check option does not work if post has link to attachment;
* Updated Italian translation (thanks Gianluca Marzaro)

= 2.1 =
* Added Italian translation (thanks Gianluca Marzaro);
* Updated to support WP 3.0, marked as compatible with 3.0.x;
* Added support for custom post types

= 2.0.4 =
* Added German translation (thanks Rian Klijn)

= 2.0.3 =
* Code cleanup

= 2.0.2 =
* Added Brazilian Portuguese translation (thanks Frank)

= 2.0.1 =
* Added Dutch translation (thanks Rene);
* Code cleanup

= 2.0 =
* Added options to automatically remove extra categories and tags over limit;
* Added option to show draft and pending post count in Admin Dashboard;
* New filter `wypiekacz_send_email_to_new_user`: allow other plugins to disable sending emails with password to new users;
* Fix: WordPress 2.9 does not display correct message after post status is changed to draft;
* Fix: check rules for pending posts too

= 1.14.2 =
* Marked as compatible with WP 2.9.x

= 1.14.1 =
* Marked as compatible with WP 2.8.5

= 1.14 =
* Clear 'skip rule check' option for post if someone with role lower that Editor saves it;
* Added Allowed Word List;
* Added icon for Ozh' Admin Drop Down Menu plugin

= 1.13 =
* Added option to allow Editors and Administrators to skip rule check for given post

= 1.12.1 =
* Added Belorussian translation (thanks [ilyuha](http://antsar.info/));
* Added FAQ and Screenshots

= 1.12 =
* Added option to check minimum and maximum post's title length in words;
* Added option to configure adding "..." at the end of truncated text

= 1.11.1 =
* Added Russian translation (thanks [M.Comfi](http://www.comfi.com/))

= 1.11 =
* Added option to check minimum post length in words

= 1.10 =
* Added option to automatically remove extra links over limit
* Added option to automatically truncate too long titles

= 1.9.1 =
* One more fix for min/max tags counting

= 1.9 =
* Fix: min/max tag count limits do not work in WP 2.8
* Marked plugin as tested with WP 2.8.1

= 1.8.1 =
* Marked plugin as tested with WP 2.8

= 1.8 =
* Fix: cannot activate plugin when registration and/or reset password emails sending option(s) are disabled

= 1.7 =
* Added check for forbidden words in title and content

= 1.6 =
* Fix: yes/no options (checkboxes) were changed to "no" while saving settings;
* Added maximum title length checking;
* Added options to disable extra emails sent to admin, which informs about new users registrations and passwords resets

= 1.5 =
* Updated admin section code for forward WordPress compatibility

= 1.4 =
* Updated to work with Simple Post Tags plugin

= 1.3 =
* Bug fix - unseen results of post checking may be displayed on Edit Posts page;
* Added new column to the Edit Posts page with check results

= 1.2 =
* Translated plugin to English, moved Polish language to external file;
* Uploaded plugin to wordpress.org repository

= 1.1 =
* Added check for minimum post title length;
* Added check for minimum and maximum categories count;
* Added check for minimum and maximum tags count;
* Added check for default category;
* Option to disable checking for Admins and Editors;
* Option to specify default post template

= 1.0 =
* Initial version

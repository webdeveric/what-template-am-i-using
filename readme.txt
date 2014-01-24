=== What Template Am I Using ===
Contributors: webdeveric
Tags: template, theme development, debug, server information
Requires at least: 3.8.0
Tested up to: 3.8.1
Stable tag: 0.1.4

This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.

== Description ==

This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.

The info is only displayed for users that have the edit_theme_options capability.

Information displayed:

* Current template
* Current post type
* Are you on the "home page" (blog index)
* Are you on the "front page" (real home page of your site)
* Any extra info you want displayed. Just use the <code>wtaiu_data</code> filter.

This plugin uses a cookie to remember if the panel was open and if it was, it will reopen if when the page is reloaded.

**This plugin is intended for use by theme developers and it requires a standards compliant browser. This plugin will not work in IE8 or below.**

== Installation ==

1. Upload `what-template-am-i-using` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit front end of your site.

== Changelog ==

= 0.1.4 =
* This is a complete rewrite to include more functionality and to update the styles.
* The data displayed is now filterable.

= 0.1.3 =
* Added server IP address

= 0.1.2 =
* Added is_home() and is_front_page() output to footer

= 0.1.1 =
* Added current post type

= 0.1 =
* Initial release
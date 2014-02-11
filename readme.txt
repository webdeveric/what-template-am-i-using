=== What Template Am I Using ===
Contributors: webdeveric
Tags: template, theme development, debug, server information
Requires at least: 3.8.0
Tested up to: 3.8.1
Stable tag: 0.1.6

This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.

== Description ==

This plugin is intended for theme developers to use. It shows the current template being used to render the page, current post type, and much more.

The info is only displayed for users that have the edit_theme_options capability.

Information displayed:

* Current template
* General Information (post type, are you on the front page, etc.)
* Additional files used. For example, header.php or footer.php
* What sidebars are being used and what widgets are in them.
* List of enqueued scripts and styles.

This plugin uses a cookie to remember if the panel was open and if it was, it will reopen if when the page is reloaded.

**This plugin is intended for use by theme developers and it requires a standards compliant browser. This plugin will not work in IE8 or below.**

== Installation ==

1. Upload `what-template-am-i-using` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit front end of your site.

== Changelog ==

= 0.1.6 =
* Started using Grunt and SASS/Compass.
* I rewrote how information is displayed in the sidebar panel (removed wtaiu_data filter).
* Added drag and drop re-ordering of panels.
* Added an open/close button to the panel header.
* Added context menu options to open or close all panels. (Only works in Firefox currently)
* Added Sidebar Information panel that shows you what sidebars and widgets are being used on the current URL.
* Added checkbox to user profile page so user can toggle showing the sidebar (if allowed).
* Various style updates.
* Tested with default WordPress themes.

= 0.1.5 =
* Updated CSS: text alignment, panel opening transition.
* I updated the behavior of the close button.
* Fixed PHP warning when WP_DEBUG is true.

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

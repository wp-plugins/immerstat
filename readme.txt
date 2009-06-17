=== ImmerStat ===
Contributors: scompt
Donate link: http://scompt.com/projects/immerstat
Tags: stats, wordpress, admin
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 0.6

ImmerStat places an image in the header of your admin screen with your current WordPress.com pageview statistics.

== Description ==

ImmerStat places an image in the header of your admin screen with your current WordPress.com pageview statistics.  You must also have the [WordPress.com Stats plugin](http://wordpress.org/extend/plugins/stats/) installed and configured.  The graphic is provided using the [Google Charts API](http://code.google.com/apis/chart/).

== Installation ==

1. Upload the `immerstat` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Configuration ==

The number of days displayed in the image is controlled in the Miscellaneous Settings page.  The default value is 30.  The rest of the options can be controlled using the following filter hooks:

= immerstat_colors =

This is an associative array that represents the colors that will be used for the graph.  It has 3 keys: `bg`, `under`, `line`.  The values are guessed for the current admin color scheme in use.

= immerstat_img_link_args =

This is an associative array of arguments that will be sent to the [Google Charts API](http://code.google.com/apis/chart/).  Check out that link for the various options available.

== Frequently Asked Questions ==

= How do I ask a frequently asked question? =

Email [me](mailto:scompt@scompt.com).

== Screenshots ==

1. ImmerStat adds a small image to the header of your admin screen.

== Future Plans ==

* Dunno, any ideas?

== Version History ==

= Version 0.6 =

* Updated for WordPress 2.7/2.8
* Now shows an image that fits into the empty space in the middle of the admin header
* Moved days setting to Settings page
* Won't show warning if there's no data

= Version 0.5 =

* Basic functionality.
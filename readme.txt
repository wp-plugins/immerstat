=== ImmerStat ===
Contributors: scompt
Donate link: http://scompt.com/projects/immerstat
Tags: stats wordpress admin
Requires at least: 2.2
Tested up to: 2.5
Stable tag: 0.5

ImmerStat places a .PNG in the top-right corner of your admin screen with your current WordPress.com pageview statistics.

== Description ==

ImmerStat places a .PNG in the top-right corner of your admin screen with your current WordPress.com pageview statistics.  You must also have the [WordPress.com Stats plugin](http://wordpress.org/extend/plugins/stats/) installed and configured.  The graphic is provided using the [Google Charts API](http://code.google.com/apis/chart/).

It also removes the WordPress.com Stats panel from the dashboard.  If you want to see all the information that WordPress.com Stats provides, you can still go to that subpage.  Your dashboard no longer has any Flash elements on it!

ImmerStat has been tested in WordPress 2.5.  It should work in versions 2.3 and 2.2, but it hasn't been tested there.  Please let [me](mailto:scompt@scompt.com) know if there are any problems.

== Installation ==

1. Upload the `immerstat` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Configuration ==

There's no Options/Settings panel for ImmerStat.  Instead, a number of filter hooks are provided to change the default behavior.  Here's what they are.

= immerstat_days =

This is the number of days that should be displayed.  The default value is `30`.

= immerstat_colors =

This is an associative array that represents the colors that will be used for the graph.  It has 3 keys: `bg`, `under`, `line`.  The values are guessed for the current admin color scheme in use.

= immerstat_img_link_args =

This is an associative array of arguments that will be sent to the [Google Charts API](http://code.google.com/apis/chart/).  Check out that link for the various options available.

== Frequently Asked Questions ==

= How do I ask a frequently asked question? =

Email [me](mailto:scompt@scompt.com).

== Screenshots ==

1. None yet

== Future Plans ==

* Dunno, any ideas?

== Version History ==

= Version 0.5 =

* Basic functionality.
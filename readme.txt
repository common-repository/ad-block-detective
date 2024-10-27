=== Ad Block Detective ===
Contributors: G.o.D
Donate link: http://sourceforge.net/project/project_donations.php?group_id=565093
Tags: ad, block, adblock, detect
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 0.5.1

Detects most ad block users and offers differnt methods to react to them.

== Description ==

Ad Block Detective tries to detect ad block users and offers you one of the following methods
to react:

* Fully block them
* Redirect them to a certain URL
* Show a shareware-a-like nag screen

== Installation ==

1. Uncompress the zip file
1. Upload the `ad-block-detective` directory to the `/wp-content/plugins/` directory
1. Activate the plugin
1. Visit your admin page and go to `Settings / ABDetective` to configure the plugin

== Requirements ==

Requires SEO friendly URL's enabled. To activate this feature, visit your admin page and
go to `Settings / Permalinks` and choose anything other then `Default` under `Common Settings`

You might also need to alter your .htaccess, if wordpress cannot do this for you automatically,
it will tell you what to do when you save the setting.

The plugin currently requires a theme, that has the content in a single `DIV` tag after the `BODY`
tag. If the plugin does not work correctly, check your theme and enclose the whole `BODY` content
in a `DIV`. There is a way to change this in development, but for now this is required.

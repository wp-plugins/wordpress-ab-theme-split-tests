=== Plugin Name ===
Contributors: leewillis77
Donate link: http://www.leewillis.co.uk/wordpress-plugins/?utm_source=wordpress&utm_medium=www&utm_campaign=wordpress-ab-theme-split-tests
Tags: split testing, a/b testing
Requires at least: 3.4
Tested up to: 3.6
Stable tag: 1.3

Split test your wordpress theme, and track test using Google Analytics user defined values.

== Description ==

This plugin lets you set up two different templates with differences that you think might increase conversions, serve these different templates up to users, and track their activity using custom segments in Google Analytics.

Alternatively you can use it to "choose" a particular theme by setting a URL parameter so you can experiment using different themes yourself (Particularly useful when developing your "split" themes!

== Installation ==

*You Must* already be tracking visitors through Google Analytics - if you don't already do this, then we recommend the [Google Analytics For Wordpress plugin](http://wordpress.org/extend/plugins/google-analytics-for-wordpress/) which we know works with this plugin - although any other Google Analytics plugins should also work.

1. Upload the Plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up different themes to test
4. Choose the themes to test in the Plugin settings (Settings &raquo; SES Theme Split Test in the Wordpress admin area)
5. Set up custom segments in Google Analytics

For more info see the [detailed instructions](http://www.leewillis.co.uk/how-to-split-test-wordpress-themes/)

== Frequently Asked Questions ==

= How do I see my site with a particular theme? =

Just go to http://www.yoursite.com/?wp_splittest_force=themefolder

= I forced myself to get a fixed theme - how do I swap back? =

Simple - go to http://www.yoursite.com/?wp_splittest_reset

= Isn't this the same as Google Website Optimizer? =

No - this lets you split test whole themes rather than content changes, e.g. navigation on the let versus navigation on the right

= How can I tell if the tracking is taking place? =

Check the source of your webpage for a line that looks like this:

`<script type="text/javascript">pageTracker._setVar("irresistible");</script>`

= The tracking line isn't being added to my page - why not? =

Make sure all of your themes call wp_footer() in the footer

= Does this work with the new-style Google Analytics code =

Yes, it works with either old-style (pageTracker) or new-style (_gaq) analytics code

== Screenshots ==

1. Screenshot showing wordpress settings screen
2. Screenshot showing resulting Google Analytics data

== Changelog ==

= 1.3 =
* Support for child themes

= 1.2 =
* Minor update - fix some usages of deprecated functions

= 1.1 =
* Fixes for PHP installs where short_open_tag is off

= 0.3 =
* Support new-style google analytics tracking (Thanks to <a href="http://www.viadat.com">Moyo</a>)

= 0.2 =
* Update Wordpress compatability tag

= 0.1 =
* Initial Release

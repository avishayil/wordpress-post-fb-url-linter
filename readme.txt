=== Facebook URL Linter for Posts Plugin ===
Contributors: Avishay Bassa, Geektime
Tags: url linter, url, linter, facebook, debugger, debug, post
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 0.5

== Description ==

This Plugin allows you to send any published or updated post to facebook scraper. This Plugin was developed by Geektime's dev team for internal purposes only, and was shared for your convenience. We do not provide any support services for this plugin.

<strong>Usage</strong>

1. Go to <a href="http://developers.facebook.com/">developers.facebook.com</a> and grab your App ID & App Secret from your app Dashboard, by selecting your Facebook app from the Apps panel.
2. Paste the App ID & App Secret on the plugin settings page inside your WordPress blog.


== Installation ==

1. Upload `facebook-force-lint.zip` onto your local computer.
2. Go to your WordPress Dashboard and select <strong>Plugins >> Add New</strong>.
3. Click on the <strong>Upload</strong> option at the top and select the `facebook-force-lint.zip` you just downloaded.
4. Click on <strong>Install</strong>.
5. Activate the plugin through the 'Plugins' menu in WordPress
6. There should be an additional Facebook URL Linter sub-menu under the Settings option of your Dashboard.
7. Clicking on the Facebook URL Linter sub-menu will cause `fb-linter.php` to be executed. 

== Frequently Asked Questions ==

<strong>Why do I need this plugin</strong>

The plugin allows the users to send their new & updated WordPress blog posts to the Facebook scraper automatically.
It helps users to avoid unneccesary Facebook errors, and keep facebook updated with every update on old posts.

<strong>How this plugin Works</strong>

Each time a user is posting new article or updating existing post, the plugin sends the permalink url to the <a href="https://developers.facebook.com/tools/debug/">Facebook URL Linter / Debugger</a> and forces Facebook to re-fetch post information.

This plugin is utilizing <a href="https://github.com/facebookarchive/facebook-php-sdk">Facebook PHP SDK (v.3.2.3)</a> for websites running PHP 5.3 and up. You can find more information about how to use the SDK <a href="https://developers.facebook.com/docs/php/gettingstarted/4.0.0">here</a>.
It uses WordPress <a href="http://codex.wordpress.org/Plugin_API/Action_Reference/publish_post">publish_post</a> & <a href="http://codex.wordpress.org/Plugin_API/Action_Reference/post_updated">post_updated</a> hooks in order to catch the neccesary events.

== Screenshots ==

1. Use the admin panel to configure Facebook App ID & Secret

== Changelog ==

<strong>V0.5</strong> - Added email notifications support

<strong>V0.4</strong> - Added scheduled posts support

<strong>V0.3.1</strong> - Changed the events where the request is occuring to be more specific.

<strong>V0.3</strong> - Updated the facebook request to be more reliable, even when error is returned form the debugger.

<strong>V0.2</strong> - Organize libraries and improve Active mechanism for safety.

<strong>V0.1</strong> - First version.
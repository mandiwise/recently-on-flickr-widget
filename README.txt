=== Recently on Flickr Widget ===

Contributors: mandiwise
Tags: widget, flickr, photos
Requires at least: 3.5
Tested up to: 3.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A basic Flickr widget that allows you to display a specified number of your most recent Flickr photos in your sidebar.

== Description ==

The Recently on Flickr Widget is a light-weight plugin that allows you to display your most recent Flickr photos in your sidebar, either as small or large square thumbnails.

It has just enough CSS to show the photos as a basic grid in your sidebar, so you can easily enhance their display by adding your own styls on top of that.

The plugin uses a Flickr API key instead of RSS to fetch your photos, so you won't be limited to displaying only 20 photos at once.

Props: This widget was inspired by a tutorial that originally appeared on [Wptuts+](http://wp.tutsplus.com/tutorials/create-a-basic-flickr-widget-using-the-widget-api/) a few years ago. It has been extensively forked to be theme-independent using Tomm McFarlin's [WordPress Widget Boilerplate](https://github.com/tommcfarlin/WordPress-Widget-Boilerplate).

== Installation ==

1. Extract the `recently-on-flickr-widget-master.zip` and remove `-master` from the extracted directory name.
2. Upload the `recently-on-flickr-widget` folder and its contents to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the Appearance > Widget page, place the widget in your sidebar, and adjust the settings as needed.

== Frequently Asked Questions ==

= Why do I need to register for the Flickr API to use this plugin? =

Take this under good authority: it's better to be in control of your own API key. If you rely on a plugin developer to supply an API key for you, and that developer then closes their Flickr account or otherwise vanishes into thin air, then the Flickr feed(s) on your website will stop working.

Don't worry, registering for the Flickr API isn't as onerous as it sounds (and you don't need to to know anything about web development to do it).

= How do I register for the Flickr API and get an API key? =

Follow these simple steps to register for the Flickr API and create a API key to use with this plugin on your WordPress site:

1. Go to [www.flickr.com/services/apps/create/apply/](http://www.flickr.com/services/apps/create/apply/) and login to your Flickr account.
2. Apply for an API key. You'll probably need a non-commercial one, unless you plan on making money off of whatever you're doing with this widget.
3. Once you've created your "app" you can use its API key in your Recently on Flickr Widget settings.

= Why doesn't my photo feed refresh as soon as I've uploaded new photos to my Flickr account? =

Making calls to an API can cost you a lot in terms of page load speed, so this widget caches your feed for a duration of time that you specify in the widget options. Flickr also imposes limits on how many queries you can make using your API key every hour. If you have a busy site, then caching is a must.

You can refresh the cache as often as every five minutes, or as infrequently as once per day. The choice is yours, but do keep user experience in mind.

= Is it localized? =

Yes, but no translations are available quite yet.

== Screenshots ==

1. Widget options in the WordPress admin area

== Changelog ==

= 1.0 =
* Initial release.

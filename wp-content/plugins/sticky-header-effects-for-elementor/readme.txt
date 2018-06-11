=== Sticky Header Effects for Elementor ===

Contributors: rwattner, dgovea 
Tags: Elementor, Elementor Page Builder, Elements, Elementor Addons, Add-ons, Page Builder, Widgets, Briefcasewp
Requires at least: 4.5.9
Tested up to: 4.9.4
Requires PHP: 5.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Options and features that extend Elementor Pro's sticky header capabilities.

== Description ==

Sticky Header Effects for Elementor adds the features and functionality to Elementor Pro Page Builder's sticky header feature. Giving users the option to change the background color and height when the visitor starts scrolling down the page. This allows for a "transparent" menu effect that can become any color, semi-transparent or solid, once the visitor begins to scroll. 

This plugin is cross browser compatible and fully responsive. Meaning it will work on all browsers as well as tablets and mobile devices. 

This plugin is meant to be an add-on to Elementor Pro page builder as it's not a standalone plugin.

### Features

* Options panel built-in to Elementor Pro's advanced section options. - Settings are right where they should be without cluttering up your workspace.
* Section background color options. - Full HEX, RGBA, and Color Name support.
* Apply options on scrolling. - The scrolling distance is adjustable for the best results in any situation.
* Section Height. - Fully responsive and allows for a "shrink" effect to maximize space and achieve a slim style without losing functionality. (Please note that the "shrink" effect is limmited by the height and padding of the header content. See the F.A.Q.)

### Pro features

Coming very soon. Stay tuned!

== Installation ==

= Minimum Requirements =
* WordPress 4.9 or greater
* PHP version 5.4 or greater
* MySQL version 5.0 or greater

= Installation Instructions =
- Make sure that you have installed Elementor Pro Page Builder. This is not a stand-alone plugin and ONLY works with Elementor Pro.
- Install the plugin through the WordPress plugins screen directly or download the plugin and upload it to the plugin folder: /wp-content/plugins/.
- Activate the plugin through the installation screen or the "Plugins" screen in WordPress
- You can find Sticky Header Options for Elementor under a sections "Advanced" tab, directly under "Sticky Effect".

== Frequently Asked Questions ==

= Is this a standalone Plugin? =

No. You cannot use Sticky Header Effects for Elementor by itself. It is a plugin for Elementor Pro.

= Does it work with any WordPress theme? =

Yes. It will work with any WordPress theme that is using Elementor Pro as a page builder.

= Will this plugin slow down my website speed? =

Sticky Header Options for Elementor is light-weight and you can also use only the options you want to use on your website for faster performance.

= Why isn't the "shrink" effect not working? =

The "shrink" effect is restricted by the height of the section's sontent. This includes logos, or other images, and padding for the sections, columns, and elements in the header.

To get the best "shrink" effect use these settings:
* Set the top and bottom padding to 0px on your sticky header section, column, and elements inside of it.
* Set a custom logo and other image height(you can leave the width blank for "auto").
* Set the header section height to "min height" and adjust it to your desired height.

Basically what happens is that the content of the header is "too tall" to shrink down anymore.

= Why can I see the page background when scrolling back up? =
This is because the user reaches the top of the page before the animation is done. There are 2 ways to solve this.
Method 1:
* Open Elementor page settings by clicking the gear icon in the bottom left corner of the MAIN content page(Not the sticky header).
* Click the style tab in the top right and change the background color to match either the top section background or the sticky header background.
Method 2:
*Open the sticky header in Elementor and click on "Edit Section"
*Click the advanced tab and add NEGATIVE bottom margin that matches the height of the section. Ex: min height = 150px, bottom margin = -150px
== Screenshots ==

1. Settings built-in to Elementor Pro.
2. Main settings panel.
3. HEX, RGBA, and Color Name support.

== Changelog == 

= 1.0.0 =

- Initial stable release

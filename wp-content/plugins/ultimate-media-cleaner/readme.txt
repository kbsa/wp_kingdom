=== Ultimate media cleaner ===
Contributors: nicearma
Tags: media clean, image clean, attachment clean, delete, image not used, image unused, files unused, files not used, delete unused, delete not used image, clean up image, clean image, clean images, clean, clean wp, clean wordpress
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 1.1

Search image from the database and delete all unused images making space in your server and clean up the database from all them
 
== Description ==

UMC (<b>U</b>ltimate <b>M</b>edia <b>C</b>Cleaner) help you to find unused media files and give you the posibility to delete them

Is important to understand that the plugin will <b>TRY</b> to find unused media files on every Post and Page. 

Why TRY?

Wordpress is a master peace of software and is used for more than à blog cms,
  you will install every kind of plugin to modify/add feature, and this can change how media files work.

This plugin use:

* Angular 5 
* Angular UI 
* Bootstrap

Github at <a href="https://github.com/nicearma/ultimate-media-cleaner-js">UMC JS</a>
Github at <a href="https://github.com/nicearma/ultimate-media-cleaner-php">UMC PHP</a>

== Changelog ==

= Version 1.1 =

Change PHP 7 to PHP 5.6 compatibility

= Version 1.0 =

Rewrite of DNUI and CUF in one plugin

* Find and delete media files from the database
* Find and delete media files from folders
* Page of option
* Tunnel for first person using the plugin

== Installation ==

The easy way :

1. Download this plugin direct from the page of plugin in your wordpress site.

The hard way :

1. Download the zip.
2. Connect to your server and upload the `DNUI` folder to your `/wp-content/plugins/` directory
2. Activate the plugin using the `Plugins` menu in WordPress


== Frequently Asked Questions ==

= Why i have to do Backup? =

This plugin will delete media files and information's in your server and the database, so you have to do one BACKUP every time you want
to use this plugin.

= Is the backup system from the UMC plugin enough? =

 The plugin have one simple backup system, but is not this main purpose of the plugin make backups and neither to wide use.

= Is really not used / unused? =

The plugin THINK that the files is unused, because any physical reference has been found in one post/page, BUT IS REALLY UNUSED? HARD TO SAY, YOU HAVE TO VERIFY BY YOUR OWN

= How to fix the false 'not used' label? =

This question can be hard to answer <br>
I build this plugin for help you to fix this problem, you have somes options:

1.  Use the Ignore Size Option
2.  You can dev your own chekkerImage[Plugin].php code, and add this to Checkers (you can send me the code and i will put this in the Free version)
3.  Ask me to do it this plugin compatible with the X Plugin (Only for Pro version)


moodle-local_contactform
=======================

Moodle plugin which displays contact forms.


Requirements
------------

This plugin requires Moodle 2.8+


Changes
-------

* 2015-09-18 - Initial version


Installation
------------

Install the plugin like any other plugin to folder
/local/contactform

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

The local_contactform plugin is designed to fetch a static HTML document from disk, enrich it with Moodle navigation and theme and deliver it as a standard Moodle page which exists outside any course. After installing local_staticpage, the plugin should be configured.
To configure the plugin and its behaviour, please visit Plugins -> Local plugins -> Static pages.



Create forms
----------------

Now, you have to create the html documents and put them in the document directory.
For each static page you want to serve, create a HTML document in the document directory, named as [pagename].html


Multiple language support
-------------------------

You can use local_staticpage completely without multilanguage support. But when you need multilanguage support, please create one html document for every language you want to provide (because Moodle multilanguage tags are not supported in your HTML code).

When local_staticpage checks the Document directory for valid static pages files, it will take every file with a .html filename extension as explained above.

After finding a file with a .html filename extension, local_staticpage takes a second look at the filename to see if it can find another filename extension. If there is one, it is used as language of the static page.

Example:

The document directory /var/www/files/moodledata/staticpage contains the files imprint.en.html, impressum.de.html and faq.html. local_staticpage looks at the filename extensions and serves the static pages as follows:

* File imprint.en.html will be served as static page with the page name "imprint", but only when the current language of the user is english.
* File impressum.de.html will be served as static page with the page name "impressum", but only when the current language of the user is german.
* File faq.html will be served as static page with the page name "faq" regardless of the current language of the user.

local_staticpage doesn't know anything about connections with regard to contents between document files. If you want to serve static pages which are translated into multiple languages and which should be switchable with the Moodle language switcher, you are welcome to create symbolic links in your document directory. This has been tested on Unix-like servers, see http://en.wikipedia.org/wiki/Symbolic_link#POSIX_and_Unix-like_operating_systems for details.

Example:

-rw-r-----.  1 root   apache 1700 Jan  9 12:20 impressum.de.html
-rw-r-----.  1 root   apache   15 Jan 23 22:16 impressum.en.html -> imprint.en.html
-rw-r-----.  1 root   apache   17 Jan 23 22:15 imprint.de.html -> impressum.de.html
-rw-r-----.  1 root   apache 1658 Jan  9 12:20 imprint.en.html

What you see here is a directory listing with two documents and two symbolic links. local_staticpage will serve the imprint page in the language of the user, regardless of the pagename with which it has been called.




Apache mod_rewrite
------------------

### Using mod_rewrite

local_staticpage is able to use Apache's mod_rewrite module to provide static pages on a clean and understandable URL.

Please add the following to your Apache configuration or your .htaccess file in the Moodle directory:

RewriteEngine On
RewriteRule ^/static/(.*)\.html$ /local/staticpage/view.php?page=$1&%{QUERY_STRING} [L]

Now, the static pages from the above example are available on
http://www.yourmoodle.com/static/imprint.html
http://www.yourmoodle.com/static/impressum.html
http://www.yourmoodle.com/static/faq.html


If you are running Moodle in a subdirectory on your webserver, please add the following to your Apache configuration or your .htaccess file in the Moodle directory:

RewriteEngine On
RewriteRule ^/yoursubdirectory/static/(.*)\.html$ /yoursubdirectory/local/staticpage/view.php?page=$1&%{QUERY_STRING} [L]

Now, the static pages from the above example are available on
http://www.yourmoodle.com/yoursubdirectory/static/imprint.html
http://www.yourmoodle.com/yoursubdirectory/static/impressum.html
http://www.yourmoodle.com/yoursubdirectory/static/faq.html


You can now create links to these URLs in a Moodle HTML Block, in your Moodle theme footer and so on.


### Not using mod_rewrite

If you don't want or are unable to use Apache's mod_rewrite, local_staticpage will still work.

The static pages from the above example are available on
http://www.yourmoodle.com/local/staticpage/view.php?page=imprint
http://www.yourmoodle.com/local/staticpage/view.php?page=impressum
http://www.yourmoodle.com/local/staticpage/view.php?page=faq

These URLs aren't as catchy as with mod_rewrite, but they work in exactly the same manner.

You can now create links to these URLs in a Moodle HTML Block, in your Moodle theme footer and so on.


Theme
-----

The local_staticpage plugin uses the "standard" pagelayout of your theme by default for creating the Moodle pages. For most themes, this works well.

If you want to style static pages in any special way, you could use a CSS cascade to style static pages content in some special way:

If you are using Apache mod_rewrite URLs, you can use this CSS selector:
body.path-static ... { }

If you are not using Apache mod_rewrite URLs, you can use this CSS selector:
body.path-local-staticpage ... { }


Add blocks to static pages
--------------------------

The local_staticpage plugin was not intended to show blocks on the static pages. However, it is possible. You have to enable page editing somewhere else in Moodle (on your MyMoodle page or on a course page, for example) and go to your static page. Now you see the standard "Add block" menu and can add blocks to the static page. Additionally, if you click on the block's gear icon, you can control if the block is shown only on the static page the block was added to or on all static pages.


Security considerations
-----------------------

local_staticpage does NOT check the static HTML documents for any malicious code, neither for malicious HTML code which will be delivered directly to the user's browser, nor for malicious PHP code which could break DOM parsing when processing the HTML document on the server.

Therefore, please make sure that your HTML code is well-formed and that only authorized and briefed users have write access to the document directory.


Motivation for this plugin
--------------------------

I have seen Moodle installations where there was a need for displaying static information like an imprint, a faq or a contact page and this information couldn't be added everything to the frontpage. As Moodle doesn't have a "page" concept, admins started to create courses, place their information within these courses, open guest access to the course and link to this course from HTML blocks or the custom menu.

I thought that this course overhead doesn't make sense, so I created local_staticpage. It is not meant as a fully features content management solution, especially as you have to work with raw HTML, but it is quite handy for experienced admins for creating some few static pages within Moodle.


Further information
-------------------

local_staticpage is found in the Moodle Plugins repository: https://moodle.org/plugins/view/local_staticpage

Report a bug or suggest an improvement: https://github.com/moodleuulm/moodle-local_staticpage/issues


Moodle release support
----------------------

Due to limited ressources, local_staticpage is only maintained for the most recent major release of Moodle. However, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that local_staticpage still works with a new major relase - please let us know on https://github.com/moodleuulm/moodle-local_staticpage/issues


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send me a pull request on
github with modifications.


Copyright
---------

PromWebSoft
Ing Pablo A Pico
Colombia

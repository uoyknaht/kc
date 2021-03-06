=== Polylang ===
Contributors: Chouby
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CCWWYUUQV8F4E
Tags: multilingual, bilingual, translate, translation, language, multilanguage, i18n, international, l10n, localization
Requires at least: 3.1
Tested up to: 3.5
Stable tag: 0.9.8
License: GPLv2 or later

Polylang adds multilingual content management support to WordPress.

== Description ==

= Features  =

You write posts, pages and create categories and post tags as usual, and then define the language for each of them. The translation is optional. The plugin does not integrate automatic or professional translation.

* You can have as many languages as you want. RTL languages are supported. WordPress languages files are automatically downloaded and updated.
* You can translate posts, pages, media, categories, post tags, menus, widgets... Custom post types, custom taxonomies, sticky posts and post formats, RSS feeds and all default WordPress widgets are supported.
* Categories, post tags as well as some other metas are automatically copied when adding a new post or page translation
* Support for multisite, pretty permalinks and static page used as front page
* A language switcher is provided as a widget or in the nav menu
* As a bonus, each user can set the WordPress admin language in its profile

The plugin admin interface is currently available in 19 languages: English, French, German contributed by [Christian Ries](http://www.singbyfoot.lu), Russian contributed by [yoyurec](http://yoyurec.in.ua), Greek contributed by [theodotos](http://www.ubuntucy.org), Dutch contributed by [AlbertGn](http://wordpress.org/support/profile/albertgn), Hebrew contributed by [ArielK](http://www.arielk.net), Polish contributed by [Peter Paciorkiewicz](http://www.paciorkiewicz.pl), Latvian contributed by [@AndyDeGroo](http://twitter.com/AndyDeGroo), Italian contributed by [Luca Barbetti](http://wordpress.org/support/profile/lucabarbetti), Danish contributed by [Compute](http://wordpress.org/support/profile/compute), Spanish contributed by Curro, Portuguese contributed by [Vitor Carvalho](http://vcarvalho.com/), Lithuanian contributed by [Naglis Jonaitis](http://najo.lt/), Turkish contributed by [darchws](http://darch.ws/), Finnish contributed by [Jani Alha](http://www.wysiwyg.fi), Bulgarian contributed by [pavelsof](http://wordpress.org/support/profile/pavelsof), Belarusian contributed by [Alexander Markevitch](http://fourfeathers.by/), Afrikaans contributed by [Kobus Joubert](http://translate3d.com/)


Other [contributions](http://wordpress.org/extend/plugins/polylang/other_notes/) are welcome ! 

= Feedback or ideas =

Don't hesitate to [give your feedback](http://wordpress.org/support/plugin/polylang). It will help making the plugin better. Don't hesitate to rate the plugin too.

== Installation ==

1. Make sure you are using WordPress 3.1 or later and that your server is running PHP5 (if you are using WordPress 3.2 or newer, it does !)
1. If you tried other multilingual plugins, deactivate them before activating Polylang, otherwise, you may get unexpected results !
1. Download the plugin
1. Extract all the files.
1. Upload everything (keeping the directory structure) to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the languages settings page and create the languages you need
1. Add the 'language switcher' widget to let your visitors switch the language.
1. Take care that your theme must come with the corresponding .mo files (Polylang downloads them for Twenty Ten and Twenty Eleven). If your theme is not internationalized yet, please refer to the [codex](http://codex.wordpress.org/I18n_for_WordPress_Developers#I18n_for_theme_and_plugin_developers) or ask the theme author to internationalize it.

== Frequently Asked Questions ==

= Where to find help ? =

* Read the [documentation](http://plugins.svn.wordpress.org/polylang/trunk/doc/documentation-en.pdf) supplied whith the plugin (in the doc directory). It includes guidelines to start working with Polylang, a much bigger FAQ than here and the API documentation for programmers.
* Search the [support forum](http://wordpress.org/support/plugin/polylang). I know that searching in the WordPress forum is not very convenient, but please give it a try. You can use generic search engines such as Google too as the WordPress forum SEO is very good. You will most probably find your answer here.
* If you still have a problem, open a new thread in the [support forum](http://wordpress.org/support/plugin/polylang).

= Is Polylang compatible with multisite ? =

Yes. You can either activate it at network or site level.

= Can I use my own flags for the language switcher ? =

Yes. You have to use PNG or JPG files and name them with the WordPress locale corresponding to the language. For example, en_US.png for English. Then upload these files in the `wp-content/polylang` directory. Don't use the `wp-content/polylang/flags` directory as your files would be removed when updating the plugin.

== Screenshots ==

1. The Polylang languages admin panel in WordPress 3.3.1

== Contribute ==

If you wonder how you can help Polylang, here are some ideas. As you will read, there is no need to be a PHP developper!

= Help other users of Polylang =

The [support forum](http://wordpress.org/support/plugin/polylang) is here so that users of the plugin can get help when they need it. However, I will not always be available to answer questions. You don't need to be a developer to help out. Very often similar questions have been answered in the past.

= Report bugs =

Don't hesitate to report bugs on the [support forum](http://wordpress.org/support/plugin/polylang).

= Test new versions =

You can subscribe to the tag ['polylang-dev'](http://wordpress.org/tags/polylang-dev) that I use to announce development versions and then, test the new versions and report bugs before the final release. It helps a lot!

= Translate the admin interface =

Polylang is already available in 19 languages. It's very easy to add a new one ! Download [poedit](http://www.poedit.net/download.php) (available for Windows, Mac OS X and Linux). Rename the file polylang.pot found in the polylang/languages directory into something like polylang-your_locale.po. Open the file with poedit and start translating (keeping strange codes such as %s, %1$s as is). Once done, just save and you will get two files polylang-your_locale.po and polylang-your_locale.mo that you can send to the author. The translation will be included with the next release.

= Communicate =

If you like Polylang, you can spread the word... Rating the plugin is very easy, you can write some words about the plugin, make a link to the plugin page...

= What else ? =

Every suggestions are welcome.

== Changelog ==

= 0.9.8 (2012-12-05) =

* Bug correction: ajax on frontend does not work when adding the language code to all urls
* Bug correction: search forms using the get_search_form filter do not work

= 0.9.7 (2012-12-04) =

* Bug correction: the admin language filter does filter non translatable post types
* Bug correction: again the canonical redirection
* Bug correction: fatal error when Polylang is used together with 'Author Avatars List'
* Bug correction: widget titles uselessly appear in the strings translations table when the widget is set for only one language
* Tests done with WordPress 3.5 beta RC3 and Twenty Twelve

= 0.9.6 (2012-11-26) =

* It is now possible to query the terms by language using the WordPress function 'get_terms'
* Bug correction: search for empty string in default language displays posts in all languages when hiding the URL language information for default language 
* Bug correction: completely reworked the canonical redirection introduced in 0.9.5 which created more problems than it solved
* Bug correction: ajax for media translations does not work
* Started tests with WordPress 3.5 beta RC1 and Twenty Twelve

= 0.9.5 (2012-11-13) =

* The user can now choose the number of languages and strings translations to display
* Add compatibility with the 'icl_object_id' function and ICL_LANGUAGE_CODE and ICL_LANGUAGE_NAME constants from the WPML API
* Add 17 languages to the predefined list (automatic download and update of language files won't work)
* Bug correction: post preview does not work when adding the language code to all urls
* Bug correction: redirect to front page in default language when posting a comment on static front page
* Bug correction: impossible to create terms with the same name in different languages
* Bug correction: query string added by other plugins is erased when adding the language code to all urls
* Bug correction: redirect erase 'POST' variables on homepage when adding the language code to all urls
* Bug correction: English (en_US) loads rtl style when using a localized WordPress package with an rtl language
* Bug correction: on some installation strings translations do not work with some special characters
* Bug correction: incoming links are not redirected to canonical url when adding the language code to all urls and hiding the code for the default language
* Bug correction: search form does not work in non default language when using permalinks without trailing slash

= 0.9.4 (2012-10-23) =

* Add Afrikaans translation contributed by [Kobus Joubert](http://translate3d.com/)
* Add Belarusian translation contributed by [Alexander Markevitch](http://fourfeathers.by/)
* Add Afrikaans (af) and Belarusian (be_BY) to predefined languages list (automatic download and update of language files won't work)
* Add the possibility to translate the date format and time format
* Add compatibility with the 'icl_get_home_url' function from the WPML API
* Bug correction: still some issues with string translations
* Bug correction: search is not filtered by the (default) language when the language is set by content and the language code is hidden for the default language
* Bug correction: posts & pages preview urls are broken when adding the language code to all urls
* Bug correction: automatically added new top-level pages to menus are not filtered by language
* Bug correction: the admin language filter messes the categories languages when editing a post and the parent dropdown list when editing a category
* Bug correction: search form does not work when using a static front page (introduced in 0.9.2)
* Bug correction: can't set languages for categories and post tags on blogs created after polylang has been activated at network level
* Bug correction: menus don't work with catch box theme ('has_nav_menu' not correctly filtered)

= 0.9.3 (2012-10-08) =

* Add Bulgarian translation contributed by [pavelsof](http://wordpress.org/support/profile/pavelsof)
* Add compatibility with WPML API for strings translations
* Bug correction: dates are not translated (introduced in 0.9.2)
* Bug correction: the language is lost when keeping - No change - for language in bulk edit
* Bug correction: categories and tags are duplicate (when default language is set automatically to existing content and categories and tags share the same name)

= 0.9.2 (2012-09-30) =

* Support new WordPress (WP 3.5+) convention for js and css files naming
* Improve performance, mainly on frontend
* Bug correction: the category language is not set when creating it in the post editor (introduced in 0.9)
* Bug correction: unable to add a query string when using a static front page 
* Bug correction: ajax tag suggestion in "edit post" conflicts with the admin content language filter
* Bug correction: ugly notices when trying to access a static front page which has not been translated
* Bug correction: the language code is added to custom post types and taxonomies permalinks even if they are not translatable
* Bug correction: some arrays in wp_locale mix English and other language
* Bug correction: the media language is not correctly set when uploading from post if the post has not been saved after choosing the language 

= 0.9.1 (2012-09-20) =

* Add Finnish translation contributed by [Jani Alha](http://www.wysiwyg.fi)
* Bug correction: improve the robustness of the admin content language filter
* Bug correction: the language switcher displays languages which have no posts or pages (introduced in 0.9)
* Bug correction: wrong default language when adding a new media
* Bug correction: the dropdown language switcher does not switch language when there is no post translation
* Bug correction: issue with translations when using category quick edit
* Bug correction: home redirects to 404 when combining static front page + force_lang = 1 + hide_default = 0

= 0.9 (2012-09-12) =

* Add Turkish translation contributed by [darchws](http://darch.ws/)
* Add media translation support
* Add a persistant content language filter on admin side (WP 3.2+ required)
* Add biographical info translation
* Add multiline support for string translations
* Add the possibility to clean the strings translation database
* Add quick edit and bulk edit support for posts and pages
* Add quick edit support for categories and tags
* The language is now loaded with 'setup_theme' action instead of 'wp' action when always adding language information url
* Search form now does use javascript only for searchform.php when pretty permalinks are not used
* Add the option PLL_SEARCH_FORM_JS to disable the js code used to modify the search form
* Suppress the option PLL_SYNC, replaced by an option in the language settings ui
* Suppress the PLL_DISPLAY_ALL option
* Suppress the template tag 'the_languages' (replaced by 'pll_the_languages' since v0.5)
* Suppress the function 'pll_is_front_page' (useless since 0.8.2)
* Bug correction: the browser language is sometimes not correctly detected by Android
* Bug correction: the rtl text direction is not correct when editing an existing language
* Bug correction: rss feed does not work if translated site title or tagline contains special characters
* Bug correction: post types and taxonomies labels are not translated on frontend
* Bug correction: the filter 'pll_copy_post_metas' does not work for metas with multiple values
* Bug correction: translations table for post and terms are uselessly serialized two times
* Bug correction: attempt to suppress conflict with themes which hardcode the name of nav menus (but do define a theme location)
* Bug correction: homepage displays all posts when the front page displays a static page and no page is selected for front page (but one is selected for posts page)
* Bug correction: widgets disappear when Polylang is enabled

= 0.8.10 (2012-08-06) =

* Add Lithuanian (lt_LT) to predefined languages list (automatic download and update of language files won't work)
* Add Lithuanian translation contributed by [Naglis Jonaitis](http://najo.lt/)
* Bug correction: empty string translation issue
* Bug correction: 'wp_list_pages' does not filter custom post types
* Bug correction: warning if posts are queried before the action 'wp_loaded' has been fired
* Bug correction: notice in twentyten when resquesting a date archive with no posts

= 0.8.9 (2012-07-20) =

* Add Portuguese translation contributed by [Vitor Carvalho](http://vcarvalho.com/)

= 0.8.8 (2012-07-18) =

* Validation improvement thanks to kg69design
* Bug correction: custom post types rewrite rules are broken when registered with query_var=>false
* Bug correction: user admin language not deleted when uninstalling the plugin
* Bug correction: pll_current_language('name') returns locale instead of language name
* Bug correction: ajax on frontend does not work
* Bug correction: homepage pagination broken when redirecting the language page to a static front page
* Bug correction: taxonomies conflicts on custom post types
* Bug correction: the admin language is not updated when edited by other users

= 0.8.7 (2012-06-10) =

* Add the possibility to load Polylang API for ajax requests on frontend
* Bug correction: search form is broken when using a static front page
* Bug correction: admin bar search does not work
* Tests done with WordPress 3.4 RC2

= 0.8.6 (2012-05-23) =

* Add the possibility to use a local config file to set options
* Improve robustness (less PHP notices)
* Bug correction: Menus not showing in preview mode
* Bug correction: fatal error when customizing a theme in WP 3.4 beta 4
* Bug correction: second page of search results returns 404 when using pretty permalinks

= 0.8.5 (2012-05-14) =

* Bug correction : sites using static front page are messed in v0.8.4

= 0.8.4 (2012-05-13) =

* Add a new argument 'post_id' to the function pll_the_languages to display posts translations within the loop
* Bug correction: every posts in every languages are shown on the homepage when requesting the wrong one with or without 'www.'
* Bug correction: every posts in every languages are shown when requesting /?p=string
* Bug correction: the language is not correctly set for wp-signup.php and wp-activate.php
* Bug correction: wrong home links when using permalinks with front with WP 3.3 and older
* Bug correction: wrong redirection after posting a comment when adding the language information to all urls
* Bug correction: term language may be lost in some situations
* Bug correction: post language is set to default if updated outside the edit post page
* Bug correction: javascript error in WP 3.1
* Bug correction: can't toggle visibility of tags metabox in edit post panel
* Tests done with WordPress 3.4 beta 4

= 0.8.3 (2012-04-10) =

* Add Danish translation contributed by [Compute](http://wordpress.org/support/profile/compute)
* Add Spanish translation contributed by Curro
* Add the possibility to add a content in a different language than the current one by setting explicitely the lang parameter in the secondary query
* Add support of PATHINFO permalinks
* Bug correction: secondary queries not correctly filtered by language
* Bug correction: wrong archives links when using permalinks with front
* Bug correction: wrong homepage link when keeping 'language' in permalinks with front
* Bug correction: flush_rewrite_rules notice when setting up a static front page (introduced in 0.8.2)
* Bug correction: every posts in every languages are shown when hitting the homepage with a query string unknown to WP (thanks to Gonçalo Peres)
* Bug correction: every posts in every languages are shown on the homepage when PHP adds index.php to the url
* Tests done with WordPress 3.4 beta 1


= 0.8.2 (2012-03-20) =

* Add Italian translation contributed by [Luca Barbetti](http://wordpress.org/support/profile/lucabarbetti)
* Improve performance on admin side
* Comment status and ping status are now copied when adding a new translation
* Deprecated API function 'pll_is_front_page' as it is now useless
* Bug correction: Wrong translation url for taxonomies when adding the language information to all urls
* Bug correction: "translation" of search page does not work if the site is only made of pages
* Bug correction: wrong language permalink structure introduced in 0.8.1
* Bug correction: wrong language set when clicking on "add new" translation in edit category and edit tags panels
* Bug correction: site does not display if no languages are set
* Bug correction: get_author_posts_url is 404
* Bug correction: homepage is 404 when using a static front page and adding the language information to all urls

= 0.8.1 (2012-03-11) =

* Add Latvian translation contributed by [@AndyDeGroo](http://twitter.com/AndyDeGroo)
* It is now possible to synchronize multiple values for custom fields
* Add new API function pll_current_language
* Add the pll_rewrite_rules filter allowing plugins to filter rewrite rules by language
* WP 3.4 preparation: disable the menu section in the customize theme admin panel (unusable with Polylang)
* Bug correction: removing 'language' in permalinks does not work in WP 3.4 alpha
* Bug correction: problems with custom post type archives when 'has_archive' is set (thanks to AndyDeGroo)
* Bug correction: 404 error when combining %postname% permastructure with "Add language information to all URL" option
* Bug correction: translated custom strings are duplicated if registered several times
* Bug correction: queries with an array of post types are not correctly filtered
* Bug correction: wp-login.php always in English

= 0.8 (2012-02-29) =

* Sticky posts are now filtered by language
* It is now possible to use the language page as home page
* Add an "About Polylang" metabox on the languages admin page
* Add the pll_the_languages filter allowing to filter the whole output of the language switcher
* Add a new argument 'display_names_as' to the function pll_the_languages
* Add pll_get_post_types & pll_get_taxonomies filters allowing to enable / disable the language filter for post types & taxonomies
* Add ckb to predefined languages list
* Completely reworked the string translation storage in the database
* Some performance improvements on admin side
* Improve compatibility with other plugins broken by the home url filter
* Add an option to disable the home url filter
* Add an option to disable synchronization of metas between translations
* Bug correction: body class 'home' is not set on translated homepage
* Bug correction: robots.txt is broken when adding the language code to all urls (including default language)
* Bug correction: bad name for the Czech flag
* Bug correction: bad language information in rss feed for WP < 3.4
* Bug correction: signup broken on multisite
* Bug correction: the translation url is set to self when using a static front page and no page for posts and there is no translation
* Bug correction: problems with custom post type archive titles
* Bug correction: problems with custom post type if rewrite slug is different from post_type (thanks to AndyDeGroo)
* Bug correction: quick edit still breaks translation linking of pages (thanks to AndyDeGroo)
* Bug correction: bad rewrite rules for feeds (introduced in 0.7.2)
* Bug correction: the order is not saved when creating a language
* Bug correction: the categories list is not updated when adding a new category (ajax broken)

= 0.7.2 (2012-02-15) =

* Add Polish translation contributed by [Peter Paciorkiewicz](http://www.paciorkiewicz.pl)
* Add 5 new languages to predefined list
* completely reworked rewrite rules
* WP 3.4 preparation: add new WordPress languages files to download when creating a new language 
* Bug correction: custom nav menus do not work in Artisteer generated themes
* Bug correction: having a single language causes multiple warnings while saving post/page.
* Bug correction: custom nav menu broken on archives pages
* Bug correction: the language switcher does not link to translated post type archive when using pretty permalinks
* Bug correction: the tags are not saved in the right language when translated tags have the same name
* Bug correction: bad link in post preview when adding language code to all urls
* Bug correction: feed not filtered by language when adding language code to all urls
* Bug correction: duplicate canonical link when used together with WordPress SEO by Yoast
* Bug correction: the all posts admin page is messed if another plugin adds a column
* Bug correction: 404 error on static front page when adding language code to all urls (including default language)

= 0.7.1 (2012-02-06) =

* Allow using 3 characters languages codes (ISO 639-2 or 639-3)
* The predefined languages dropdown list now displays the locale to help differentiate some languages
* Add 5 new languages to predefined list
* Bug correction: the filter 'pll_copy_post_metas' does not work
* Bug correction: impossible to add a tag in the edit post panel
* Bug correction: rewrite rules not correct
* Bug correction: cache issue with css and js files

= 0.7 (2012-01-30) =

* Add Hebrew translation contributed by [ArielK](http://www.arielk.net)
* Add support for RTL languages for both frontend and admin
* Twenty Ten and Twenty Eleven languages files are now automatically downloaded when creating a new langage
* Improve filtering tags by language in the edit post panel
* Category parent dropdown list is now filtered by language
* Category parents are now synchronized between translations
* Add the possibility to have the language information in all URL
* Add support for post formats
* Add option allowing not to show the current language in the language switcher (for both menu and widget)
* Add a title attribute (and the possibility to personalize it with a filter) to flags
* pll_get_post and pll_get_term second parameter is now optional and defaults to current language
* Add pll_the_language_link filter allowing to filter translation links outputed by the language switcher
* The option PLL_DISPLAY_ALL is no longer supported
* Bug correction: Autosave reset to default language
* Bug correction: blog info not translated in feeds
* Bug correction: post comments feed always in default language
* Bug correction: undefined index notice when setting up a custom menu widget
* Bug correction: rewrite rules are not correctly reset when deactivating the plugin
* Bug correction: is_home not correctly set on pages 2, 3...
* Bug correction: avoid naming conflicts (in sql queries) with other themes / plugins
* Bug correction: bad language detection and url rewriting of custom post types archives

= 0.6.1 (2012-01-12) =

* Add Dutch translation contributed by [AlbertGn](http://wordpress.org/support/profile/albertgn)
* Disable everything except the languages management panel while no language has been created
* Bug correction: can't have the same featured image in translated posts
* Bug correction: parent page dropdown does appear only after the page has been saved
* Bug correction: archives widget not working anymore
* Bug correction: string translations does not work for WP < 3.3
* Bug correction: fix fatal error in string translations caused by widgets using the old API
* Bug correction: the strings translation panel is unable to translate strings with special characters
* Bug correction: Polylang "is_front_page" returns true on archives pages

= 0.6 (2012-01-07) =

* Add Greek translation contributed by [theodotos](http://www.ubuntucy.org)
* WordPress languages files are now automatically downloaded when creating a new langage (and updated when updating WordPress)
* Add the possibility to change the order of the languages in the language switcher
* Add the possibility to translate the site title, tagline and widgets titles
* Categories, post tags, featured image, page parent, page template and menu order are now copied when adding a new translation
* Translations are now accessibles in the "Posts", "Pages", "Categories" and "Post tags" admin panels
* Improve the dropdown language switcher widget (sends now to translated page or home page based on options)
* Move custom flags from polylang/local_flags to wp_content/polylang
* Add two options to "pll_the_languages" ('hide_if_no_translation' and 'hide_current'). *The function does not output ul tag anymore*
* Improve API
* Bug correction: Twenty eleven custom Header problem with v0.5.1
* Bug correction: front-page.php not loaded for translated front page

= 0.5.1 (2011-12-18) =

* Improved German translation contributed by [Christian Ries](http://www.singbyfoot.lu)
* Bug correction: translated homepage not recognized as home page when it displays posts
* Bug correction: predefined language list does not work on IE8
* Bug correction: on some installations, "Add New" post doesn't keep intended language
* Bug correction: fatal error when Polylang is used together with the plugin Tabbed Widgets
* Bug correction: language Switcher points sometimes to wrong places

= 0.5 (2011-12-07) =

* Add multisite support
* Rework the Polylang admin panel. There is now a set of predefined languages
* Improve categories and tags language filter in the edit post panel
* Categories and tags created in the edit post panel are now created with the same language as the post
* The language switcher can now force the link to the front page instead of the translated page
* The nav menus can now display a language switcher
* Improved performance
* Optimized the calendar widget (less code and sql queries executed)
* Added the possibility to display posts and terms with no language set (see the documentation to know how to enable this functionnality)
* Started the creation of a small API for theme and plugin programmers
* Bug correction: when using a static front page, the page for posts does not work when using the default permalink settings
* Bug correction: the search form does not work if a static front page is used
* Bug correction: quick edit breaks translations
* Bug correction: categories and post tags translations don't work for more than 2 languages
* Bug correction: the output of wp_page_menu is not correct for non default languages

= 0.4.4 (2011-11-28) =

* Bug correction: When using a static front page, the translated home page displays posts instead of the translated page
* Bug correction: Automatic language setting of existing categories and post tags does not work correctly

= 0.4.3 (2011-11-19) =

* Add Russian translation contributed by [yoyurec](http://yoyurec.in.ua)
* Bug correction: impossible to suppress the language name in the language switcher widget settings
* Bug correction: post's page does not work when using a static front page
* Bug correction: flags in local_flags directory are removed after an automatic upgrade (now works for an upgrade from 0.4.3+ to a higher version)
* Bug correction: switching to default language displays a 404 Error when hiding the default language in url and displaying the language switcher as dropdown
* Other minor bug corrections
* Tests done with WordPress 3.3 beta 3

= 0.4.2 (2011-11-16) =

* Bug correction: language settings page is broken in v0.4.1

= 0.4.1 (2011-11-16) =

* Bug correction: flags shows even when you set doesn't to show
* Bug correction: custom taxonomies do not work
* Bug correction: some users get the fatal error: call to undefined function wp_get_current_user() in /wp-includes/user.php on line 227

= 0.4 (2011-11-10) =

* Add a documentation (in English only)
* Add the possibility to hide the url language information for the default language
* Add the possibility to set the admin language in the user profile
* Add the possibilty to fill existing posts, pages, categories & tags with the default language
* Add support for custom post types and custom taxonomies
* Add the possibility to display flags in the language switcher
* Add CSS classes to customize rendering of the language switcher
* Add the possibility to display the language switcher as a dropdown list
* Add support for calendar widget
* Improve performance: less sql queries
* Improve data validation when creating or updating languages
* Bug correction: 'wp_list_pages' page order is ignored when the plugin is enabled
* Bug correction: when using 'edit' or 'add new' (translation) for posts, the categories appear in the wrong language
* Bug correction: pages are not included in language post count
* Bug correction: the language switcher does not display languages if there are only pages
* Bug correction: the widget filter does not allow to come back to 'all languages' once a language has been set
* Other minor bug corrections

= 0.3.2 (2011-10-20) =

* Bug correction: authors pages are not filtered by language
* Bug correction: language pages use the archive template
* Bug correction: database error for comments on posts and pages
* Bug correction: "Add new" translation for pages creates a post instead of a page
* Bug correction: the search query does not look into pages

= 0.3.1 (2011-10-16) =

* Bug correction: the widget settings cannot be saved when activating Polylang
* Bug correction: the archives widget does not display any links
* Bug correction: ajax form for translations not working in the 'Categories' and 'Post tags' admin panels

= 0.3 (2011-10-07) =

* Add language filter for widgets
* Improved performance for filtering pages by language
* Improved security
* Minor bug correction with versions management

= 0.2 (2011-10-05) =

* Add language filter for nav menus
* Add German translation
* Add language filter for recent comments
* Add ajax to term edit form
* Add ajax to post metabox
* Improved performance for filtering terms by language
* Bugs correction

= 0.1 (2011-09-22) =
* Initial release

<?php

// functions common to all admin panels
class Polylang_Admin_Base extends Polylang_Base {
	function __construct() {
		parent::__construct();

		// filter admin language for users
		add_filter('locale', array(&$this, 'get_locale'));

		// set user preferences
		add_action('admin_init',  array(&$this, 'admin_init_base'));

		// adds the link to the languages panel in the wordpress admin menu
		add_action('admin_menu', array(&$this, 'add_menus'));

		// setup js scripts and css styles
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
	}

	// returns the locale based on user preference
	function get_locale($locale) {
		// get_current_user_id uses wp_get_current_user which may not be available the first time(s) get_locale is called
		return function_exists('wp_get_current_user') && ($loc = get_user_meta(get_current_user_id(), 'user_lang', 'true')) ? $loc : $locale;
	}

	// set user preferences
	function admin_init_base() {
		if (!$this->get_languages_list())
			return;

		// set text direction if the user set its own language
		global $wpdb, $wp_locale;
		$lang_id = $wpdb->get_var($wpdb->prepare("SELECT t.term_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
			WHERE tt.taxonomy = 'language' AND tt.description = %s LIMIT 1", get_locale())); // no function exists to get term by description
		if ($lang_id)
			$wp_locale->text_direction = get_metadata('term', $lang_id, '_rtl', true) ? 'rtl' : 'ltr';

		// set user meta when choosing to filter content by language
 		// $_GET[lang] is used in ajax 'tag suggest' and is numeric when editing a language
		if (!defined('DOING_AJAX') && isset($_GET['lang']) && $_GET['lang'] && !is_numeric($_GET['lang']))
			update_user_meta(get_current_user_id(), 'pll_filter_content', ($lang = $this->get_language($_GET['lang'])) ? $lang->slug : '');

		// adds the languages in admin bar
		// FIXME: OK for WP 3.2 and newer (the admin bar is not displayed on admin side for WP 3.1)
		add_action('admin_bar_menu', array(&$this, 'admin_bar_menu'), 100); // 100 determines the position
	}

	// adds the link to the languages panel in the wordpress admin menu
	function add_menus() {
		add_submenu_page('options-general.php', __('Languages', 'polylang'), __('Languages', 'polylang'), 'manage_options', 'mlang',  array(&$this, 'languages_page'));
	}

	// setup js scripts & css styles (only on the relevant pages)
	function admin_enqueue_scripts() {
		$screen = get_current_screen();
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// for each script:
		// 0 => the pages on which to load the script
		// 1 => the scripts it needs to work
		// 2 => 1 if loaded even if languages have not been defined yet, 0 otherwise
		$scripts = array(
			'admin' => array( array('settings_page_mlang'), array('jquery', 'wp-ajax-response', 'postbox'), 1 ),
			'post'  => array( array('post', 'media', 'async-upload', 'edit'),  array('jquery', 'wp-ajax-response'), 0 ),
			'term'  => array( array('edit-tags'), array('jquery', 'wp-ajax-response'), 0 ),
			'user'  => array( array('profile', 'user-edit'), array('jquery'), 0 ),
		);

		foreach ($scripts as $script => $v)
			if (in_array($screen->base, $v[0]) && ($v[2] || $this->get_languages_list()))
				wp_enqueue_script('pll_'.$script, POLYLANG_URL .'/js/'.$script.$suffix.'.js', $v[1], POLYLANG_VERSION);
			
		if (in_array($screen->base, array('settings_page_mlang', 'post', 'edit-tags', 'edit', 'upload', 'media')))
			wp_enqueue_style('polylang_admin', POLYLANG_URL .'/css/admin'.$suffix.'.css', array(), POLYLANG_VERSION);
	}

	// downloads mofiles
	function download_mo($locale, $upgrade = false) {
		global $wp_version;
		$mofile = WP_LANG_DIR."/$locale.mo";

		// does file exists ?
		if ((file_exists($mofile) && !$upgrade) || $locale == 'en_US')
			return true;

		// does language directory exists ?
		if (!is_dir(WP_LANG_DIR)) {
			if (!@mkdir(WP_LANG_DIR))
				return false;
		}

		// will first look in tags/ (most languages) then in branches/ (only Greek ?)
		$base = 'http://svn.automattic.com/wordpress-i18n/'.$locale;
		$bases = array($base.'/tags/', $base.'/branches/');

		foreach ($bases as $base) {
			// get all the versions available in the subdirectory
			$resp = wp_remote_get($base);
			if (is_wp_error($resp) || 200 != $resp['response']['code'])
				continue;

			preg_match_all('#>([0-9\.]+)\/#', $resp['body'], $matches);
			if (empty($matches[1]))
				continue;

			rsort($matches[1]); // sort from newest to oldest
			$versions = $matches[1];

			$newest = $upgrade ? $upgrade : $wp_version;
			foreach ($versions as $key=>$version) {
				// will not try to download a too recent mofile
				if (version_compare($version, $newest, '>'))
					unset($versions[$key]);
				// will not download an older version if we are upgrading
				if ($upgrade && version_compare($version, $wp_version, '<='))
					unset($versions[$key]);
			}

			$versions = array_splice($versions, 0, 5); // reduce the number of versions to test to 5
			$args = array('timeout' => 30, 'stream' => true);

			// try to download the file
			foreach ($versions as $version) {
				$resp = wp_remote_get($base."$version/messages/$locale.mo", $args + array('filename' => $mofile));
				if (is_wp_error($resp) || 200 != $resp['response']['code'])
					continue;

				// try to download ms and continents-cities files if exist (will not return false if failed)
				// with new files introduced in WP 3.4
				foreach (array("ms", "continent-cities", "admin", "admin-network") as $file)
					wp_remote_get($base."$version/messages/$file-$locale.mo", $args + array('filename' => WP_LANG_DIR."/$file-$locale.mo"));

				// try to download theme files if exist (will not return false if failed)
				// FIXME not updated when the theme is updated outside a core update
				foreach (array("twentyten", "twentyeleven", "twentytwelve") as $theme)
					wp_remote_get($base."$version/messages/$theme/$locale.mo", $args + array('filename' => get_theme_root()."/$theme/languages/$locale.mo"));

				return true;
			}
		}
		// we did not succeeded to download a file :(
		return false;
	}

	// returns options available for the language switcher (menu or widget)
	// FIXME do not include the dropdown in menu yet since I need to work on js
	function get_switcher_options($type = 'widget', $key ='string') {
		$options = array(
			'show_names' => array('string' => __('Displays language names', 'polylang'), 'default' => 1),
			'show_flags' => array('string' => __('Displays flags', 'polylang'), 'default' => 0),
			'force_home' => array('string' => __('Forces link to front page', 'polylang'), 'default' => 0),
			'hide_current' => array('string' => __('Hides the current language', 'polylang'), 'default' => 0),
		);
		$menu_options = array('switcher' => array('string' => __('Displays a language switcher at the end of the menu', 'polylang'), 'default' => 0));
		$widget_options = array('dropdown' => array('string' => __('Displays as dropdown', 'polylang'), 'default' => 0));
		$options = ($type == 'menu') ? array_merge($menu_options, $options) : array_merge($options, $widget_options);
		return array_map(create_function('$v', "return \$v['$key'];"), $options);
	}

	// register strings for translation making sure it is not duplicate or empty
	function register_string($name, $string, $multiline = false) {
		$to_register = array('name'=> $name, 'string' => $string, 'multiline' => $multiline);
		if (!in_array($to_register, $this->strings) && $to_register['string'])
			$this->strings[] = $to_register;
	}

	// adds the languages in admin bar
	function admin_bar_menu($wp_admin_bar) {
		$url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// $_GET['lang'] is numeric when editing a language, not when selecting a new language in the filter
		$selected = isset($_GET['lang']) && $_GET['lang'] && !is_numeric($_GET['lang']) && ($lang = $this->get_language($_GET['lang'])) ? $lang->slug : 
			(($lg = get_user_meta(get_current_user_id(), 'pll_filter_content', true)) ? $lg : 'all');

		$wp_admin_bar->add_menu(array(
			'id'     => 'languages',
			'title'  => __('Languages', 'polylang'),
			'meta'  => array('title' => __('Filters content by language', 'polylang')),
		));

		$wp_admin_bar->add_menu(array(
			'parent' => 'languages',
			'id'     => 'all',
			'title'  => sprintf('<input name="all" type="radio" value="1" %s /> %s',
				$selected == 'all' ? 'checked="checked"' : '', __('Show all languages', 'polylang')),
			'href'   => add_query_arg('lang', 'all', $url),
		));

		foreach ($this->get_languages_list() as $lang) {
			$wp_admin_bar->add_menu(array(
				'parent' => 'languages',
				'id'     => $lang->slug,
				'title'  => sprintf('<input name="%s" type="radio" value="1" %s /> %s',
					$lang->slug, $selected == $lang->slug ? 'checked="checked"' : '', $lang->name),
				'href'   => add_query_arg('lang', $lang->slug, $url),
			));			
		}
	}

} // class Polylang_Admin_Base

?>

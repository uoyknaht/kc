<?php

// setups basic functions used for admin and frontend
abstract class Polylang_Base {
	protected $options;
	protected $home;
	protected $strings = array(); // strings to translate
	protected $post_types; // post types to filter by language
	protected $taxonomies; // taxonomies to filter by language

	// used to cache results
	private $languages_list = array();
	private $language = array();
	private $links = array();

	function __construct() {
		// init options often needed
		$this->options = get_option('polylang');
		$this->home = get_option('home');

		add_action('wp_loaded', array(&$this, 'add_post_types_taxonomies')); // must come late to be sure that all post types and taxonomies are registered
	}

	// init post types and taxonomies to filter by language
	function add_post_types_taxonomies() {
		$post_types = array_merge(PLL_MEDIA_SUPPORT ? array('attachment' => 'attachment') : array(), get_post_types(array('show_ui' => true)));
		$this->post_types = apply_filters('pll_get_post_types', $post_types);
		$this->taxonomies = apply_filters('pll_get_taxonomies', get_taxonomies(array('show_ui'=>true)));
	}

	// returns the list of available languages
	function get_languages_list($args = array()) {
		// although get_terms is cached, it is efficient to add our own cache
		if (isset($this->languages_list[$cache_key = md5(serialize($args))]))
			return $this->languages_list[$cache_key];

		$defaults = array('hide_empty' => false, 'orderby'=> 'term_group');
		$args = wp_parse_args($args, $defaults);		
		return $this->languages_list[$cache_key] = get_terms('language', $args);
	}

	// retrieves the dropdown list of the languages
	function dropdown_languages($args = array()) {
		$args = apply_filters('pll_dropdown_language_args', $args);
		$defaults = array('name' => 'lang_choice', 'class' => '', 'add_options' => array(), 'hide_empty' => false, 'value' => 'slug', 'selected' => '');
		extract(wp_parse_args($args, $defaults));

		// sort $add_options by value
		uasort($add_options, create_function('$a, $b', "return \$a['value'] > \$b['value'];" ));

		$out = sprintf('<select name="%1$s" id="%1$s"%2$s>'."\n", esc_attr($name), $class ? ' class="'.esc_attr($class).'"' : '');
		foreach ($add_options as $option)
			$out .= "<option value='" . $option['value'] . "'>" . $option['text'] . "</option>\n";
		foreach ($this->get_languages_list($args) as $language) {
			$out .= sprintf("<option value='%s'%s>%s</option>\n",
				esc_attr($language->$value),
				$language->$value == $selected ? ' selected="selected"' : '',
				esc_html($language->name)
			);
		}
		$out .= "</select>\n";
		return $out;
	}

	// returns the language by its id or its slug
	// Note: it seems that get_term_by slug is not cached (3.2.1)
	function get_language($value) {
		if (is_object($value))
			return $value;

		if (isset($this->language[$value]))
			return $this->language[$value];

		$lang = (is_numeric($value) || (int) $value) ? get_term((int) $value, 'language') :
			(is_string($value) ? get_term_by('slug', $value , 'language') : false);
		return isset($lang) && $lang && !is_wp_error($lang) ? ($this->language[$value] = $lang) : false;
	}

	// saves translations for posts or terms
	// the underscore in '_lang' hides the post meta in the Custom Fields metabox in the Edit Post screen
	// $type: either 'post' or 'term'
	// $id: post id or term id
	// $translations: an associative array of translations with language code as key and translation id as value
	function save_translations($type, $id, $translations) {
		$lang = call_user_func(array(&$this, 'get_'.$type.'_language'), $id);
		if (!$lang)
			return;

		if (isset($translations) && is_array($translations)) {
			$translations = array_merge(array($lang->slug => $id), $translations);
			foreach($translations as $key=>$p)
				update_metadata($type, (int) $p, '_translations', $translations);
		}
	}

	// deletes a translation of a post or term
	function delete_translation($type, $id) {
		$translations = $this->get_translations($type, $id);
		if (is_array($translations)) {
			$slug = array_search($id, $translations);
			unset($translations[$slug]);
			foreach($translations as $p)
				update_metadata($type, (int) $p, '_translations', $translations);
			delete_metadata($type, $id, '_translations');
		}
	}

	// returns the id of the translation of a post or term
	// $type: either 'post' or 'term'
	// $id: post id or term id
	// $lang: object or slug (in the order of preference latest to avoid)
	function get_translation($type, $id, $lang) {
		$translations = $this->get_translations($type, $id);
		$slug = $this->get_language($lang)->slug;
		return isset($translations[$slug]) ? (int) $translations[$slug] : false;
	}

	// returns an array of translations of a post or term
	function get_translations($type, $id) {
		$type = ($type == 'post' || in_array($type, $this->post_types)) ? 'post' : (($type == 'term' || in_array($type, $this->taxonomies)) ? 'term' : false);
		// maybe_unserialize due to useless serialization in versions < 0.9
		return $type ? maybe_unserialize(get_metadata($type, $id, '_translations', true)) : array(); 
	}

	// store the post language in the database
	function set_post_language($post_id, $lang) {
		wp_set_post_terms($post_id, $lang ? $this->get_language($lang)->slug : '', 'language' );
	}

	// returns the language of a post
	function get_post_language($post_id) {
		$lang = get_the_terms($post_id, 'language' );
		return ($lang) ? reset($lang) : false; // there's only one language per post : first element of the array returned
	}

	// among the post and its translations, returns the id of the post which is in $lang
	function get_post($post_id, $lang) {
		$post_lang = $this->get_post_language($post_id);
		if (!$lang || !$post_lang)
			return false;

		$lang = $this->get_language($lang);
		return $post_lang->term_id == $lang->term_id ? $post_id : $this->get_translation('post', $post_id, $lang);
	}

	// store the term language in the database
	function set_term_language($term_id, $lang) {
		update_metadata('term', $term_id, '_language', $lang ? $this->get_language($lang)->term_id : 0);
	}

	// remove the term language in the database
	function delete_term_language($term_id) {
		delete_metadata('term', $term_id, '_language');
	}

	// returns the language of a term
	function get_term_language($value, $taxonomy = '') {
		if (is_numeric($value))
			$term_id = $value;
		elseif (is_string($value) && $taxonomy)
			$term_id = get_term_by('slug', $value , $taxonomy)->term_id;
		return isset($term_id) && $term_id ? $this->get_language(get_metadata('term', $term_id, '_language', true)) : false;
	}

	// among the term and its translations, returns the id of the term which is in $lang
	function get_term($term_id, $lang) {
		$lg = $this->get_term_language($term_id);
		if (!$lang || !$lg)
			return false;

		$lang = $this->get_language($lang);
		return $lg->term_id == $lang->term_id ? $term_id : $this->get_translation('term', $term_id, $lang);
	}

	// adds language information to a link when using pretty permalinks
	function add_language_to_link($url, $lang) {
		if (!isset($lang) || !$lang)
			return $url;

		global $wp_rewrite;
		if ($wp_rewrite->using_permalinks()) {
			$base = $this->options['rewrite'] ? '' : 'language/';
			$slug = $this->options['default_lang'] == $lang->slug && $this->options['hide_default'] ? '' : $base.$lang->slug.'/';
			return esc_url(str_replace($this->home.'/'.$wp_rewrite->root, $this->home.'/'.$wp_rewrite->root.$slug, $url));
		}

		// special case for pages which do not accept adding the lang parameter
		elseif ('_get_page_link' != current_filter())
			return add_query_arg( 'lang', $lang->slug, $url );

		return $url;
	}

	// optionally rewrite posts, pages links to filter them by language
	// rewrite post format (and optionally categories and post tags) archives links to filter them by language
	function add_post_term_link_filters() {
		if ($this->options['force_lang'] && $GLOBALS['wp_rewrite']->using_permalinks()) {
			foreach (array('post_link', '_get_page_link', 'post_type_link') as $filter)
				add_filter($filter, array(&$this, 'post_link'), 10, 2);
		}

		add_filter('term_link', array(&$this, 'term_link'), 10, 3);
	}

	// modifies post & page links
	function post_link($link, $post) {
		if (isset($this->links[$link]))
			return $this->links[$link];

		if ('post_type_link' == current_filter() && !in_array($post->post_type, $this->post_types))
			return $this->links[$link] = $link;

		if ('_get_page_link' == current_filter()) // this filter uses the ID instead of the post object
			$post = get_post($post);

		// /!\ when post_status in not "publish", WP does not use pretty permalinks
		return $this->links[$link] = $post->post_status != 'publish' ? $link : $this->add_language_to_link($link, $this->get_post_language($post->ID));
	}

	// modifies term link
	function term_link($link, $term, $tax) {
		if (isset($this->links[$link]))
			return $this->links[$link];

		$ok = $tax == 'post_format' || ($this->options['force_lang'] && $GLOBALS['wp_rewrite']->using_permalinks() && in_array($tax, $this->taxonomies));
		return $this->links[$link] = $ok ? $this->add_language_to_link($link, $this->get_term_language($term->term_id)) : $link;
	}

	// returns the html link to the flag if exists
	// $lang: object
	function get_flag($lang) {
		if (file_exists(POLYLANG_DIR.($file = '/flags/'.$lang->description.'.png')))
			$url = POLYLANG_URL.$file;

		// overwrite with custom flags
		if (!is_admin() && ( // never use custom flags on admin side
			file_exists(PLL_LOCAL_DIR.($file = '/'.$lang->description.'.png')) ||
			file_exists(PLL_LOCAL_DIR.($file = '/'.$lang->description.'.jpg')) ))
			$url = PLL_LOCAL_URL.$file;

		$title = apply_filters('pll_flag_title', $lang->name, $lang->slug, $lang->description);
		return isset($url) ? '<img src="'.esc_url($url).'" title="'.esc_attr($title).'" alt="'.esc_attr($lang->name).'" />' : '';
	}

	// adds clauses to comments query - used in both frontend and admin
	function _comments_clauses($clauses, $lang) {
		global $wpdb;

		// if this clause is not already added by WP
		if (!strpos($clauses['join'], '.ID'))
			$clauses['join'] .= " JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";

		$clauses['join'] .= " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = ID";
		$clauses['where'] .= $wpdb->prepare(" AND pll_tr.term_taxonomy_id = %d", $lang->term_taxonomy_id);

		return $clauses;
	}

	// adds terms clauses to get_terms - used in both frontend and admin
	function _terms_clauses($clauses, $lang) {
		global $wpdb;
		if (isset($lang) && $lang) {
			// the query is coming from Polylang and the $lang is an object
			if (is_object($lang))
				$languages = esc_sql($lang->term_id);

			// the query is coming from outside with 'lang' parameter and $lang is a comma separated list of slugs (or an array of slugs)
			else {
				$languages = is_array($lang) ? $lang : explode(',', $lang);
				$languages = "'" . implode("','", array_map( 'sanitize_title_for_query', $languages)) . "'";
				$languages = $wpdb->get_col("SELECT $wpdb->term_taxonomy.term_id FROM $wpdb->term_taxonomy
					INNER JOIN $wpdb->terms USING (term_id) WHERE taxonomy = 'language' AND $wpdb->terms.slug IN ($languages)"); // get ids from slugs
				$languages = esc_sql(implode(',', $languages));
			}

			$clauses['join'] .= " LEFT JOIN $wpdb->termmeta AS pll_tm ON t.term_id = pll_tm.term_id";
			$clauses['where'] .= " AND pll_tm.meta_key = '_language' AND pll_tm.meta_value IN ($languages)";
		}
		return $clauses;
	}

	// returns all page ids *not in* language defined by $lang_id
	function exclude_pages($lang_id) {
		$q = array(
			'numberposts' => -1,
			'post_type'   => array_intersect(get_post_types(array('hierarchical' => 1)), $this->post_types),
			'fields'      => 'ids',
			'tax_query'   => array(array(
				'taxonomy' => 'language',
				'fields'   => 'id',
				'terms'    => $lang_id,
				'operator' => 'NOT IN'
			))
		);
		return get_posts($q);
	}

	// export a mo object in options
	function mo_export($mo, $lang) {
		$strings = array();
		foreach ($mo->entries as $entry)
			$strings[] = array($entry->singular, $mo->translate($entry->singular));
		update_option('polylang_mo'.$lang->term_id, $strings);
	}

	// import a mo object from options
	function mo_import($lang) {
		$mo = new MO();
		if ($strings = get_option('polylang_mo'.$lang->term_id)) {
			foreach ($strings as $msg)
				$mo->add_entry($mo->make_entry($msg[0], $msg[1]));
		}
 		return $mo;
	}

} //class Polylang_Base
?>

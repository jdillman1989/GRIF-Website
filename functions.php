<?php

// Load core functionality.
include 'core/core-init.php';

add_theme_support('post-thumbnails');

// Set up an ACF options page.
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}

register_nav_menu('primary', 'Primary');
register_nav_menu('social', 'Social');

function two_level_nav($nav_array){
	$nav_hierarchy = array();
	$i = -1;
	$separator = ':';
	$parent = 0;
	foreach ($nav_array as $nav) {
		if (intval($nav->menu_item_parent)) {
			$nav_hierarchy[$i.$separator.$nav->menu_item_parent]['sub'][] = array(
				'title' => $nav->title,
				'url' => $nav->url,
			);
		}
		else{
			$i++;
			$nav_hierarchy[$i.$separator.$nav->ID] = array(
				'title' => $nav->title,
				'url' => $nav->url,
				'sub' => array(), 
			);
		}
	}
	return $nav_hierarchy;
}

function grif_social($white=false){
	$social_nav = wp_get_nav_menu_items('Social');
	$return = '';
	foreach ($social_nav as $item) {
		$svg = explode('.', $item->url);
		$svg_name = $svg[1];
		if ($white) {
			$svg_name = $svg[1].'-white';
		}
		ob_start();
		echo '<a href="'.$item->url.'" title="'.$item->title.'" target="_blank">';
		include 'svg/'.$svg_name.'.svg';
		echo '</a>';
		$this_return = ob_get_contents();
		ob_end_clean();
		$return .= $this_return;
	}
	return $return;
}

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' => 'Global Site Settings',
		'menu_title' => 'Global Site Settings',
		'menu_slug' => 'global-site-settings'
	));
}

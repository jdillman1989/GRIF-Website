<?php
class Header extends Core_Template {

	public function template_file() {
		return 'views/partials/header.twig';
	}

	public function logo_nav() {
		$return = array(
			'home' => _blogURL,
		);
		return $return;
	}

	public function main_nav() {
		$nav_array = wp_get_nav_menu_items('Primary');
		$nav = two_level_nav($nav_array);
		return $nav;
	}

	public function social_nav() {
		grif_social();
	}
}
new Header;

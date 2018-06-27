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
		include 'assets/svg/'.$svg_name.'.svg';
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

function grif_address($atts, $content = null) {

	$address = get_field('address', 'option');
	$phone = get_field('main_phone', 'option');
	$email = get_field('main_email', 'option');
	$address = str_replace(PHP_EOL,"<br>",$address);

    $a = shortcode_atts(array(
        'phone' => false,
        'email' => false,
    ),$atts);

	$return = $address;
	if ($a['phone']) {
		$return .= '<br>'.$phone;
	}
	if ($a['email']) {
		$return .= ' // '.$email;
	}
	return $return;
}

add_shortcode('grif_address', 'grif_address');

function grif_button($atts, $content = null) {

    $a = shortcode_atts(array(
        'color' => '',
        'url' => '/',
    ),$atts);

	$return = '<a class="grif-button '.$a['color'].'" href="'.$a['url'].'">'.$content.'</a>';
	return $return;
}

add_shortcode('grif_button', 'grif_button');

///////////////////
// CPT Filtering //
///////////////////

add_action( 'wp_ajax_grif_data', 'grif_get_data' );
add_action( 'wp_ajax_nopriv_grif_data', 'grif_get_data' );

function grif_get_data() {
	
	$cpt_data_args = array(
		'post_type' => $_GET['type'],
		'orderby' => 'meta_value',
		'meta_key' => 'display_date',
	);

	$cpt_data_args['paged'] = 1;
	if (isset($_GET['next'])) {
		$cpt_data_args['paged'] = $_GET['next'];
	}

	if (isset($_GET['s'])) {
		$cpt_data_args['posts_per_page'] = -1;
		$cpt_data_args['s'] = $_GET['s'];
	}
	else{
		$cpt_data_args['posts_per_page'] = $_GET['ppp'];
	}

	if (isset($_GET['tax'])) {
		foreach ($_GET['tax'] as $tax => $term) {
			if ($term != 'all' && $term != array('all') ) {
				$cpt_data_args['tax_query']['relation'] = 'AND';
				$cpt_data_args['tax_query'][] = array(
		            'taxonomy' => $tax,
		            'field' => 'slug',
		            'terms' => $term
		        );
			}
		}
	}

	$cpt_data = new WP_Query($cpt_data_args);

	$cpt_results = array();
	$cpt_results['max'] = $cpt_data->max_num_pages;
	if( $cpt_data->have_posts() ) {
		while($cpt_data->have_posts()) {
			$cpt_data->the_post();
			$cpt_data_id = get_the_ID();

			$cpt_results['data'][] = post_type_meta_data($cpt_data_id, $_GET['type']);
		}
		wp_reset_postdata();
	}
	else{
		$cpt_results = print_r($cpt_data_args);
	}

	$response = json_encode($cpt_results);

    echo $response;
    
    wp_die();
}

function post_type_meta_data($cpt_data_id, $type){
	switch ($type) {

		case 'sermons':
		case 'events':
			$get_title = get_the_title();
			$title = html_entity_decode($get_title);
			$permalink = get_permalink($cpt_data_id);

			$fallback = '';
			switch ($type) {
				case 'sermons':
					$fallback = 'sermon-series';
					break;
				case 'events':
					$fallback = 'event-categories';
					break;
			}

			$terms = wp_get_post_terms($cpt_data_id, $fallback);
			$image = get_the_post_thumbnail_url($cpt_data_id, 'large');
			if (!$image) {
				$image = get_field('graphic', $terms[0]);
				$image = $image['url'];
			}

			$date = get_field('display_date', $cpt_data_id);
			$ts_date = strtotime($date);

			$return = array(
				'title' => $title,
				'image' => $image,
				'permalink' => $permalink,
				'date' => date('M j, Y', $ts_date),
			);
			break;

		default:
			$return = 'default reached';
			break;
	}
	return $return;
}

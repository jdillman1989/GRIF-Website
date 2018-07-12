<?php

// Load core functionality.
include 'core/core-init.php';
require_once('breeze.php');
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

class Default_Page extends Core_Template {

	public function single() {
		$type = get_post_type($this->obj_id);
		$return = array();
		if ($type == 'page' || $type == 'post') {
			$return['is'] = false;
		}
		else{

			$date = get_field('display_date', $this->obj_id);
			$ts_date = strtotime($date);

			$return = array(
				'is' => true,
				'background_image' => get_field('single_background_image', $this->obj_id),
				'title' => get_the_title($this->obj_id),
				'display_date' => date('M j, Y', $ts_date),
				'description' => get_field('single_description', $this->obj_id),
				'archive' => false,
				'file' => false,
				'podcast' => false,
			);

			if ($type == 'sermons') {

				$archives = get_posts(array(
					'post_type' => 'page',
					'meta_key' => '_wp_page_template',
					'meta_value' => 'page-sermon-archive.php'
				));

				ob_start();
				include 'assets/svg/itunes.svg';
				$itunes_button = ob_get_contents();
				ob_end_clean();

				ob_start();
				include 'assets/svg/google-play.svg';
				$google_button = ob_get_contents();
				ob_end_clean();

				$prev_id = get_adjacent_sermon_id(true, $this->obj_id);
				$next_id = get_adjacent_sermon_id(false, $this->obj_id);
				$next_button = '<a href="#" class="grif-button white disabled">Previous Service &rarr;</a>';
				$prev_button = '<a href="#" class="grif-button white disabled">&larr; Next Service</a>';

				if ($next_id) {
					$next_permalink = get_permalink($next_id);
					$next_button = '<a href="'.$next_permalink.'" class="grif-button white">Previous Service &rarr;</a>';
				}
				if ($prev_id) {
					$prev_permalink = get_permalink($prev_id);
					$prev_button = '<a href="'.$prev_permalink.'" class="grif-button white">&larr; Next Service</a>';
				}

				$return['file'] = get_field('sermon_audio', $this->obj_id);
				$return['archive'] = array(
					'all' => get_page_link($archives[0]->ID),
					'next' => $prev_button,
					'previous' => $next_button,
				);
				$return['podcast'] = array(
					'google_button' => $google_button,
					'itunes_button' => $itunes_button,
				);
			}
		}

		return $return;
	}

	public function fields() {

		$fields = get_field('builder_blocks', $this->obj_id);
		$return = array();

		if ($fields) {
			foreach ($fields as $field) {
				switch($field['acf_fc_layout']){
					case 'site_cta':

						$title = get_field('cta_title', 'option');
						$description = get_field('cta_description', 'option');

						$return[] = array(
							'acf_fc_layout' => $field['acf_fc_layout'],
							'title' => $title,
							'description' => $description,
						);
						break;
					case 'current_sermon_series':

						$sermon_args = array(
							'post_type' => 'sermons',
							'numberposts' => 1,
							'orderby' => 'meta_value',
							'meta_key' => 'display_date',
						);
						$sermon = get_posts($sermon_args);
						$series = wp_get_post_terms($sermon[0]->ID, 'sermon-series');

						$archives = get_posts(array(
							'post_type' => 'page',
							'meta_key' => '_wp_page_template',
							'meta_value' => 'page-sermon-archive.php'
						));
						$sermon_archive_url = get_page_link($archives[0]->ID);

						$text_color = '0C0C0C';
						$button_color = 'green';
						if ($field['background_color'] == '0C0C0C') {
							$text_color = 'FFF';
							$button_color = 'white';
						}

						$return[] = array(
							'acf_fc_layout' => $field['acf_fc_layout'],
							'archive' => $sermon_archive_url,
							'slug' => $series[0]->slug,
							'image' => get_field('graphic', $series[0]),
							'bg_color' => $field['background_color'],
							'text_color' => $text_color,
							'button_color' => $button_color,
						);
						break;
					case 'latest_archive':
						$args = array(
							'post_type' => $field['content_type'],
							'numberposts' => $field['amount_of_posts'],
							'orderby' => 'meta_value',
							'meta_key' => 'display_date',
						);
						$posts = get_posts($args);

						$fallback = '';
						switch ($field['content_type']) {
							case 'sermons':
								$fallback = 'sermon-series';
								break;
							case 'events':
								$fallback = 'event-categories';
								break;
						}

						$recent_posts = array();
						foreach ($posts as $this_post) {

							$terms = wp_get_post_terms($this_post->ID, $fallback);
							$image = get_the_post_thumbnail_url($this_post->ID, 'large');
							if (!$image) {
								$image = get_field('graphic', $terms[0]);
								$image = $image['url'];
							}

							$date = get_field('display_date', $this_post->ID);
							$ts_date = strtotime($date);

							$recent_posts[] = array(
								'image' => $image,
								'title' => $this_post->post_title,
								'date' => date('M j, Y', $ts_date),
								'permalink' => get_permalink($this_post->ID),
							);
						}

						$return[] = array(
							'acf_fc_layout' => $field['acf_fc_layout'],
							'bg_color' => $field['background_color'],
							'heading' => $field['heading'],
							'posts' => $recent_posts,
						);
						break;

					case 'podcast_promo':
						ob_start();
						include 'assets/svg/itunes.svg';
						$itunes_button = ob_get_contents();
						ob_end_clean();

						ob_start();
						include 'assets/svg/google-play.svg';
						$google_button = ob_get_contents();
						ob_end_clean();

						$return[] = array(
							'acf_fc_layout' => $field['acf_fc_layout'],
							'google_button' => $google_button,
							'itunes_button' => $itunes_button,
						);
						break;

					case 'full_archive':
						$taxonomies = get_object_taxonomies($field['content_type'], 'objects');

						$tax_nav = '';
						$tax_pag = array();
						foreach ($taxonomies as $tax) {
							$tax_pag[$tax->name] = 'all';
							$tax_nav .= '<div class="news-tax">'
											.'<p class="news-tax-label">'.$tax->label.'</p>'
											.'<ul class="news-tax-list">'
												.'<li><a href="#" data-type="'.$field['content_type'].'" data-tax="'.$tax->name.'" data-term="all" data-all="'.$tax->label.'">All</a></li>';
							$terms = get_terms(array('taxonomy' => $tax->name, 'hide_empty' => true));
							foreach ($terms as $term) {
								$term_link = get_term_link($term);
								if(is_wp_error($term_link)) {
									continue;
								}
								$tax_nav .= '<li><a href="#" data-type="'.$field['content_type'].'" data-tax="'.$tax->name.'" class="'.$term->slug.'" data-term="'.$term->slug.'">'.$term->name.'</a></li>';
							}
							$tax_nav .= '</ul></div>';
						}

						$tax_pag_data = json_encode($tax_pag);

						$ppp = $field['amount_of_posts'];
						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
						$resources_args = array(
							'post_type' => $field['content_type'],
							'posts_per_page' => $ppp,
							'paged' => $paged,
							'orderby' => 'meta_value',
							'meta_key' => 'display_date',
						);

						$resources = new WP_Query($resources_args);

						$fallback = '';
						switch ($field['content_type']) {
							case 'sermons':
								$fallback = 'sermon-series';
								break;
							case 'events':
								$fallback = 'event-categories';
								break;
						}

						$next = $paged + 1;
						$next_link_disabled = '';
						if ($next > $resources->max_num_pages) {
							$next_link_disabled = 'disabled';
						}

						$admin_ajax = admin_url('admin-ajax.php');

						$next_button = '<a href=""'
											.'data-next="'.$next.'" '
											.'data-ppp="'.$ppp.'" '
											.'data-max="'.$resources->max_num_pages.'" '
											.'data-tax="'.$tax_pag_data.'" '
											.'data-type="'.$field['content_type'].'" '
											.'data-action="'.$admin_ajax.'?action=grif_data" '
											.'class="next-link grif-button '.$next_link_disabled.'">'
											.'view more'
										.'</a>';

						$archive_posts = array();
						if( $resources->have_posts() ) {
							while($resources->have_posts()) {
								$resources->the_post();
								$resource_id = get_the_ID();

								$title = get_the_title();
								$permalink = get_permalink();
								$date = get_field('display_date', $resource_id);
								$ts_date = strtotime($date);

								$terms = wp_get_post_terms($resource_id, $fallback);
								$image = get_the_post_thumbnail_url($resource_id, 'large');
								if (!$image) {
									$image = get_field('graphic', $terms[0]);
									$image = $image['url'];
								}

								$archive_posts[] = array(
									'image' => $image,
									'title' => $title,
									'date' => date('M j, Y', $ts_date),
									'permalink' => $permalink,
								);
							}
							wp_reset_postdata();
						}

						$return[] = array(
							'acf_fc_layout' => $field['acf_fc_layout'],
							'bg_color' => $field['background_color'],
							'heading' => $field['heading'],
							'filter_ui' => $tax_nav,
							'next_button' => $next_button,
							'type' => $field['content_type'],
							'posts' => $archive_posts,
						);
						break;

					default:
						$return[] = $field;
						break;
				}
			}
		}

		return $return;
	}
}

add_action('init', 'init_podcast');
function init_podcast(){
	add_feed('podcast', 'podcast_rss');
}
function podcast_rss(){
	get_template_part('podcast', 'podcast');
}

function all_sermons() {
	$sermon_args = array(
		'post_type' => 'sermons',
		'numberposts' => -1,
		'orderby' => 'meta_value',
		'meta_key' => 'display_date',
		'fields' => 'ids',
	);
	$sermons = get_posts($sermon_args);
	set_transient('sermon_posts', $sermons);
}
function grif_content_save($post_id, $post=[], $update=[]){
	if(get_post_type($post_id) == 'sermons'){
		all_sermons();
	}
	if(get_post_type($post_id) == 'events'){

		$title = get_the_title($post_id);
		$date = get_field('display_date', $post_id);
		$ts_date = strtotime($date);

		var_dump('https://grif.breezechms.com/api/events/add?name='.urlencode($title).'&starts_on='.$ts_date);

		$api_key = get_field('breeze_api_key', 'option');
		$breeze = new Breeze($api_key);
		$breeze_event = $breeze->url('https://grif.breezechms.com/api/events/add?name='.urlencode($title).'&starts_on='.$ts_date);

		$event_data = json_decode($breeze_event);
		update_field('breeze_id', $event_data->id, $post_id);
	}
}
add_action('save_post', 'grif_content_save');

function grif_content_delete($post_id, $post=[], $update=[]){
	if(get_post_type($post_id) == 'sermons'){
		all_sermons();
	}
	if(get_post_type($post_id) == 'events'){
		$event_id = get_field('breeze_id', $post_id);

		$api_key = get_field('breeze_api_key', 'option');
		$breeze = new Breeze($api_key);

		$delete_breeze = $breeze->url('https://grif.breezechms.com/api/events/delete?instance_id='.$event_id);
	}
}
add_action('delete_post', 'grif_content_delete');

function get_adjacent_sermon_id($previous, $this_sermon){
	$sermons = get_transient('sermon_posts');
	if( false === $sermons ) {
		all_sermons();
		$sermons = get_transient( 'sermon_posts' );
	}
	$pos = array_search( $this_sermon, $sermons );
	if($previous) {
		$new_pos = $pos - 1;
	} 
	else {
		$new_pos = $pos + 1;
	}
	if($sermons[$new_pos]){
		return $sermons[$new_pos];
	}
	else{
		return false;
	}
}


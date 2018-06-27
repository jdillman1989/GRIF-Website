<?php
class Default_Page extends Core_Template {

	public function fields() {

		$fields = get_field('builder_blocks', $this->obj_id);
		$return = array();

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

		return $return;
	}
}
global $post;
new Default_Page($post->ID);

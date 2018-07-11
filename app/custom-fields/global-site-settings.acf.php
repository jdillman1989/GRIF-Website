<?php

$group_args = [
	'title' => 'Global Site Settings',
	'location_is' => [ 'options_page', 'global-site-settings' ],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['tab', 'General Info', ['placement' => 'left']],
	['message', '', ['message' => 'Displays in the footer and anywhere the [grif_address] shortcode is used']],
	['textarea', 'Address'],
	['text', 'Main Email'],
	['text', 'Main Phone'],

	['tab', 'Header Info', ['placement' => 'left']],
	['text', 'Announcement', ['instructions' => 'Prominent message that can display above the main menu (ex: weather alerts)']],

	['tab', 'Site CTA', ['placement' => 'left']],
	// ['message', '', ['message' => 'Appears wherever the CTA content block is used']],
	['text', 'CTA Title'],
	['text', 'CTA Description'],
];

$field_group = core_register_field_group('global-site-settings', $group_args, $fields);


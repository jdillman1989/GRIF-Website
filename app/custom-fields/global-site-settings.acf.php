<?php

$group_args = [
	'title' => 'Global Site Settings',
	'location_is' => [ 'options_page', 'global-site-settings' ],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['tab', 'General Info', ['placement' => 'left']],
	['textarea', 'Address', ['instructions' => 'Displays in the footer']],
	['text', 'Main Email'],
	['text', 'Main Phone'],

	['tab', 'Header Info', ['placement' => 'left']],
	['text', 'Announcement', ['instructions' => 'Prominent message that can display above the main menu (ex: weather alerts)']],

	['tab', 'Site CTA', ['placement' => 'left']],
	['text', 'CTA Title', ['instructions' => 'Appears wherever the CTA content block is used']],
	['text', 'CTA Description'],
];

$field_group = core_register_field_group('global-site-settings', $group_args, $fields);


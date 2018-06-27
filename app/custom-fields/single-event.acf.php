<?php

$group_args = [
	'title' => 'Single Event',
	'location_is' => ['post_type', 'events'],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['tab', 'Meta Data', ['placement' => 'left']],
	['date_picker', 'Display Date', ['display_format' => 'M j, Y', 'return_format' => 'Ymd',]],
];

$field_group = core_register_field_group('single-event', $group_args, $fields);

<?php

$group_args = [
	'title' => 'Event Post',
	'location' => [
	    [
	        [
	            'param' => 'post_type',
	            'operator' => '==',
	            'value' => 'events'
	        ],
	    ],
	],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['text', 'Breeze ID', [
		'instructions' => 'Do not edit. This is set directly by Breeze.',
	]],
];

$field_group = core_register_field_group('event-post', $group_args, $fields);

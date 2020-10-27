<?php

$group_args = [
	'title' => 'Post Fields',
	'location' => [
	    [
	        [
	            'param' => 'post_type',
	            'operator' => '==',
	            'value' => 'sermons'
	        ],
	    ],
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
	['image', 'Single Background Image', ['return_format' => 'array']],
	['text', 'Single Description'],
	['date_picker', 'Display Date', ['display_format' => 'M j, Y', 'return_format' => 'Ymd',]],
];

$field_group = core_register_field_group('post-fields', $group_args, $fields);

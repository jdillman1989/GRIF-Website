<?php

$group_args = [
	'title' => 'Sermon Series',
	'location' => [
	    [
	        [
	            'param' => 'taxonomy',
	            'operator' => '==',
	            'value' => 'sermon-series'
	        ],
	    ],
	],
];

$fields = [
	['image', 'Graphic', ['return_format' => 'array']],
];

$field_group = core_register_field_group('sermon_series', $group_args, $fields);

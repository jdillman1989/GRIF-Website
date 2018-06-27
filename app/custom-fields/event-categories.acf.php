<?php

$group_args = [
	'title' => 'Event Categories',
	'location' => [
	    [
	        [
	            'param' => 'taxonomy',
	            'operator' => '==',
	            'value' => 'event-categories'
	        ],
	    ],
	],
];

$fields = [
	['image', 'Graphic', ['return_format' => 'array']],
];

$field_group = core_register_field_group('event-categories', $group_args, $fields);

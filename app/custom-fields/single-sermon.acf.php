<?php

$group_args = [
	'title' => 'Sermon Post',
	'location' => [
	    [
	        [
	            'param' => 'post_type',
	            'operator' => '==',
	            'value' => 'sermons'
	        ],
	    ],
	],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['file', 'Sermon Audio', [
		'instructions' => 'MP3 Church Service Recording',
		'return_format' => 'url',
		'mime_types' => 'mp3',
	]],
];

$field_group = core_register_field_group('sermon-post', $group_args, $fields);

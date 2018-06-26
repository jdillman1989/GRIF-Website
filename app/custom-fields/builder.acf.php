<?php

$group_args = [
	'title' => 'Page Builder',
	'location' => [
	    [
	        [
	            'param' => 'page_template',
	            'operator' => '==',
	            'value' => 'default'
	        ],
	    ],
	],
	'hide_on_screen' => ['the_content']
];

$fields = [
	['flexible_content', 'Builder Blocks',
		[
			'button_label' => 'Add Block',
			'layouts' => [
				[
					'Hero',
					[
						'layout' => 'block',
						'sub_fields' => [
							['text', 'Heading'],
							['text', 'Subheading'],
							['image', 'Background Image', ['return_format' => 'array']],
						]
					] 
				],
				[
					'Basic Text',
					[
						'layout' => 'block',
						'sub_fields' => [
							['wysiwyg', 'Text Content'],
							['select', 'Background Color', 
								[
									'choices' => [
										'FFF' => 'White',
										'E9EBEC' => 'Light Gray',
									],
								]
							],
						]
					]
				],
			]
		]
	]
];

$field_group = core_register_field_group('builder', $group_args, $fields);

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
							['number', 'Content Width', [
								'instructions' => 'Maximum width of the content for this block in pixels (0 = 100%)',
								'min' => 0,
								'max' => 1500,
								'step' => 1,
							]],
						]
					]
				],
				[
					'Two Column Text',
					[
						'layout' => 'block',
						'sub_fields' => [
							['wysiwyg', 'Left Text Content'],
							['wysiwyg', 'Right Text Content'],
							['select', 'Background Color', 
								[
									'choices' => [
										'FFF' => 'White',
										'E9EBEC' => 'Light Gray',
									],
								]
							],
							['number', 'Content Width', [
								'instructions' => 'Maximum width of the content for this block in pixels (0 = 100%)',
								'min' => 0,
								'max' => 1500,
								'step' => 1,
							]],
						]
					]
				],
				[
					'Alternating Text and Images',
					[
						'layout' => 'block',
						'sub_fields' => [
							['text', 'Heading'],
							['repeater', 'Alternating Content', [
								'sub_fields' => [
									['wysiwyg', 'Text Content'],
									['image', 'Image', ['return_format' => 'array']]
								],
								'min' => 1,
								'max' => 25,
								'layout' => 'block',
								'button_label' => 'Add Content'
							]],
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
				[
					'Image Nav',
					[
						'layout' => 'block',
						'sub_fields' => [
							['repeater', 'Nav Items', [
								'sub_fields' => [
									['text', 'Label'],
									['image', 'Image', ['return_format' => 'array']],
									['text', 'URL'],
								],
								'min' => 4,
								'max' => 12,
								'layout' => 'block',
								'button_label' => 'Add Image Nav'
							]],
						]
					]
				],
				[
					'Text and Image',
					[
						'layout' => 'block',
						'sub_fields' => [
							['wysiwyg', 'Text Content'],
							['image', 'Image', ['return_format' => 'array']],
							['true_false', 'Flip Order'],
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
				[
					'Site CTA',
					[
						'layout' => 'block',
						'sub_fields' => [
							['message', '', ['message' => 'The content for this section stays the same throughout the site. You can configure the content at Global Site Settings -> Site CTA']],
						]
					]
				],
				[
					'Latest Archive',
					[
						'layout' => 'block',
						'sub_fields' => [
							['message', '', ['message' => 'This automatically displays the most recent number of a content type.']],
							['select', 'Content Type', 
								[
									'choices' => [
										'sermons' => 'Sermons',
										'events' => 'Events',
									],
								]
							],
							['text', 'Heading'],
							['number', 'Amount of posts', [
								'instructions' => 'How many of the latest posts to display?',
								'min' => 4,
								'max' => 12,
								'step' => 1,
							]],
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
				[
					'Full Archive',
					[
						'layout' => 'block',
						'sub_fields' => [
							['message', '', ['message' => 'This automatically displays an archive of a content type and a filtering UI.']],
							['select', 'Content Type', 
								[
									'choices' => [
										'sermons' => 'Sermons',
										'events' => 'Events',
									],
								]
							],
							['text', 'Heading'],
							['number', 'Amount of posts', [
								'instructions' => 'How many of the latest posts to display?',
								'min' => 4,
								'max' => 12,
								'step' => 1,
							]],
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
				[
					'Current Sermon Series',
					[
						'layout' => 'block',
						'sub_fields' => [
							['message', '', ['message' => 'This automatically displays the graphic and archive button for the sermon series of the most recent sermon post.']],
							['select', 'Background Color', 
								[
									'choices' => [
										'FFF' => 'White',
										'E9EBEC' => 'Light Gray',
										'0C0C0C' => 'Dark Gray',
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

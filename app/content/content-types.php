<?php
core_post_type('Sermons', [
		'slug' => 'sermons',
		'supports' => ['title', 'editor', 'thumbnail']
	]
);
core_taxonomy('Sermon Series', 'sermons');
core_taxonomy('Sermon Categories', 'sermons');


core_post_type('Events', [
		'slug' => 'events',
		'supports' => ['title', 'editor', 'thumbnail']
	]
);
core_taxonomy('Event Categories', 'events');


core_post_type('Emails', [
		'slug' => 'emails',
		'supports' => ['title', 'editor', 'thumbnail']
	]
);

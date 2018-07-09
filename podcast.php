<?php
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);

$title = get_bloginfo('description');
$link = get_bloginfo('url');
$subtitle = get_bloginfo('name');
$root = get_template_directory_uri();

$args = array(
	'post_type' => 'sermons',
	'numberposts' => 10,
	'orderby' => 'meta_value',
	'meta_key' => 'display_date',
);
$posts = get_posts($args);

$episodes = '';
foreach ($posts as $this_post) {

	$date = get_field('display_date', $this_post->ID);
	$ts_date = strtotime($date);

	$description = get_field('single_description', $this_post->ID);
	$audio = get_field('sermon_audio', $this_post->ID);
	$permalink = get_permalink($this_post->ID);

	$episodes .= '<item>'
					.'<title>'.$this_post->post_title.'</title>'
					.'<itunes:summary>'.$description.'</itunes:summary>'
					.'<description>'.$description.'</description>'
					.'<link>'.$permalink.'</link>'
					.'<enclosure url="'.$audio.'" type="audio/mpeg" length="1024"></enclosure>'
					.'<pubDate>'.date('r', $ts_date).'</pubDate>'
					.'<itunes:author>'.$title.'</itunes:author>'
					.'<itunes:duration>01:00:00</itunes:duration>'
					.'<itunes:explicit>no</itunes:explicit>'
					.'<guid>'.$this_post->ID.'</guid>'
				.'</item>';
}
?>
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
	<channel>
		<title><?php echo $title; ?> Sermon Series</title>
		<link><?php echo $link; ?></link>
		<language>en-us</language>
		<itunes:subtitle><?php echo $subtitle; ?></itunes:subtitle>
		<itunes:author><?php echo $title; ?></itunes:author>
		<itunes:summary>Sunday Sermons from <?php echo $subtitle; ?></itunes:summary>
		<description>Sunday Sermons from <?php echo $subtitle; ?></description>
		<itunes:owner>
			<itunes:name><?php echo $title; ?></itunes:name>
			<itunes:email>jesse@jdillman.com</itunes:email>
		</itunes:owner>
		<itunes:explicit>no</itunes:explicit>
		<itunes:image href="<?php echo $root; ?>/assets/images/grif-podcast.jpg"></itunes:image>
		<itunes:category text="Religion &amp; Spirituality"></itunes:category>
		<?php echo $episodes; ?>
	</channel>
</rss>

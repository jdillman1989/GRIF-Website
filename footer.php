<?php
class Footer extends Core_Template {

	public function template_file() {
		return 'views/partials/footer.twig';
	}

	public function footer_content() {
		$address = get_field('address', 'option');
		$phone = get_field('main_phone', 'option');
		$email = get_field('main_email', 'option');
		$return = array(
			'name' => get_bloginfo('name'), 
			'address' => str_replace(PHP_EOL,"<br>",$address),
			'phone' => $phone,
			'email' => $email,
			'year' => date("Y"),
		);
		return $return;
	}

	public function social_nav() {
		grif_social(true);
	}
}
new Footer;

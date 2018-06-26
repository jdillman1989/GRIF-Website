<?php
class Default_Page extends Core_Template {

	public function fields() {

		return core_get_fields( $this->obj_id, '', array(
			'builder_blocks'
		), true );
	}
}
global $post;
new Default_Page($post->ID);

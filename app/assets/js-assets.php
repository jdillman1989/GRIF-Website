<?php
$default_url = _templateURL . '/assets/js/min/';

wp_deregister_script( 'jquery' );

load_js( '//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', array(
	'handle' => 'jquery'
) );

load_js( $default_url . '/application.js',  array('handle' => 'app', ) );

wp_localize_script('app', 'ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));

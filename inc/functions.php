<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

function callweb_load_styles() {
	wp_enqueue_style('callweb-admin-styles', plugin_dir_url(__FILE__) . '../assets/styles/style.css', array(), '1.0.0');
}

add_action('admin_enqueue_scripts', 'callweb_load_styles');

function callweb_add_widget_script() {
	if (get_option('callweb-widget-key', '') == '' || (int)get_option('callweb-widget-is-active', 0) !== 1) return;

	wp_enqueue_script(
		'callweb-widget-script',
		'//widget.callcontact.eu/loader.js',
		array(),
		'1.0.0',
		true
	);
	$inline_script = "
	var _callweb = _callweb || {
			key : '" . esc_js(get_option('callweb-widget-key', '')) . "',
	};
	";
	wp_add_inline_script('callweb-widget-script', $inline_script);
}

add_action('wp_enqueue_scripts', 'callweb_add_widget_script');
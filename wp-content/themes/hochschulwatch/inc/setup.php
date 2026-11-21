<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function enqueue_rtl_stylesheet() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'generatepress-rtl', trailingslashit( get_template_directory_uri() ) . 'rtl.css' );
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_rtl_stylesheet', 100 );

// Die versionierten Feldgruppen werden nur geladen. ACF speichert standardmäßig in acf-json.
add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = dirname( __DIR__ ) . '/acf-fields';
	return $paths;
} );

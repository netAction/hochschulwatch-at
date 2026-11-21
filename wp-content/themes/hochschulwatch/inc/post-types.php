<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {
	register_post_type( 'bundeslaender',
		array(
			'supports' => array(
				'title',
				'revisions',
				'author',
			),
			'labels' => array(
				'name' => 'Bundesländer',
				'singular_name' => 'Bundesland',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'bundeslaender'),
			'show_in_rest' => true,
		)
	);


	register_post_type( 'foerderer',
		array(
			'supports' => array(
				'title',
				'revisions',
				'author',
			),
			'labels' => array(
				'name' => 'Förderer',
				'singular_name' => 'Förderer',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'foerderer'),
			'show_in_rest' => true,
		)
	);


	register_post_type( 'hochschulen',
		array(
			'supports' => array(
				'title',
				'revisions',
				'author',
			),
			'labels' => array(
				'name' => 'Hochschulen',
				'singular_name' => 'Hochschule',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'hochschulen'),
			'show_in_rest' => true,
		)
	);


	register_post_type( 'foerderungen',
		array(
			'supports' => array(
				'title',
				'revisions',
				'author',
			),
			'labels' => array(
				'name' => 'Förderungen',
				'singular_name' => 'Förderung',
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'foerderungen'),
			'show_in_rest' => true,
		)
	);
});

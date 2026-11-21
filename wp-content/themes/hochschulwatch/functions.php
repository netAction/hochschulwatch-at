<?php
/**
 * Hochschulwatch Child Theme
 * 2022 von Thomas Netaction Schmidt
 * The MIT License
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
function generatepress_child_enqueue_scripts() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'generatepress-rtl', trailingslashit( get_template_directory_uri() ) . 'rtl.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'generatepress_child_enqueue_scripts', 100 );

// Die versionierten Feldgruppen werden nur geladen. ACF speichert standardmäßig in acf-json.
add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = __DIR__ . '/acf-fields';
	return $paths;
} );




// Hooking up our function to theme setup
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



// Templates für Hochschulen und Förderer
add_filter( 'the_content', function($content) {
	if ( ! function_exists( 'get_field' ) ) {
		return $content;
	}

	// Hochschule-Seite
	if ((is_single()) && ((get_post_type() == 'hochschulen') || (get_post_type() == 'foerderer'))) {
		require_once('hochschule-foerderer-template.php');
	}
	if ((is_single()) && (get_post_type() == 'bundeslaender')) {
		require_once('bundesland-template.php');
	}

	// Das, was eh schon auf der Seite steht z.B. bei einer Themenseite
	return $content;
});


// In Suchergebnissen die Hochschulen zuerst anzeigen
add_filter( 'posts_search_orderby', function( $search_orderby ) {
	if (!is_admin()) {
        global $wpdb;
        return "{$wpdb->posts}.post_type LIKE 'hochschulen' DESC, {$search_orderby}";
	}
});
// In Suchergebnissen nur Hochschulen und Förderer anzeigen
add_filter('pre_get_posts',function ($query) {
    if ($query->is_search && !is_admin() )
        $query->set('post_type',array('hochschulen','foerderer'));
    return $query;
});

// Text "Archive:" entfernen bei Hochschulen und Förderern
add_filter( 'get_the_archive_title', function( $title ) {
	if ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}
	return $title;
});

// Archive alphabetisch sortieren
// Von Bauhaus-Uni Weimar bis WWU Münster
// Besonders relevant bei Hochschulen und Förderern
add_action( 'pre_get_posts', function ($query) {
	if(is_archive()):
		//Set the order ASC or DESC
		$query->set( 'order', 'ASC' );
		//Set the orderby
		$query->set( 'orderby', 'title' );
	endif;
});



// Menüpunkt im Adminbereich unter Einstellungen hinzufügen
add_action( 'admin_menu', function () {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	add_options_page( 'Feherhafte Förderer und Förderungen', 'Feherhafte Förderer und Förderungen', 'manage_options', 'hochschulwatch-fehleranzeige', 'hochschulwatch_fehleranzeige_render' );
});

function hochschulwatch_fehleranzeige_render() {
	$loop = new WP_Query( array(
		'post_type' => 'foerderungen',
		'posts_per_page' => -1,
	));

	echo '<br><br><h2>Förderungen ohne zugeordneter Uni oder Förderer</h2>';
	// Die Förderer für später merken
	$foerdererids = [];
	if ($loop->have_posts()) {
		while ( $loop->have_posts() ) : $loop->the_post();
			// Fehlerhafte Post-Objekt-Felder suchen
			if (!get_field('hochschule') ) {
				// Diese Förderung hat keine Uni
				echo 'Hochschule fehlt: ';
				edit_post_link( '✍', '', ' ', get_the_ID(), '' );
				echo get_the_title() .'<br>';
			}
			$foerderer = get_field('foerderer');
			if (!$foerderer) {
				// Diese Förderung hat keinen Förderer
				echo 'Förderer fehlt: ';
				edit_post_link( '✍', '', ' ', get_the_ID(), '' );
				echo get_the_title() .'<br>';
			} else {
				// Diesen Förderer merken
				array_push($foerdererids, $foerderer->ID);
			}
		endwhile;
		wp_reset_query();
	} else {
		echo '✓ Alle Förderungen sind stimmig.';
	}
	echo '<br><br>';



	// Förderer suchen
	echo '<br><br><h2>Förderer ohne Förderung können gelöscht werden</h2>';
	$loop = new WP_Query( array(
		'post_type' => 'foerderer',
		'posts_per_page' => -1,
	));

	if ($loop->have_posts()) {
		while ( $loop->have_posts() ) : $loop->the_post();
			if (!in_array(get_the_ID(), $foerdererids)) {
				// Dieser Förderer hat keine Förderungen
				echo 'Förderer hat keine Förderung: ';
				edit_post_link( '✍', '', ' ', get_the_ID(), '' );
				echo get_the_title() .'<br>';
			}
		endwhile;
	}
	wp_reset_query();
	echo '<br><br>';

}


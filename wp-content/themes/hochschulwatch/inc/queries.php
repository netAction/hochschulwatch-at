<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// In den Suchergebnissen der Website die Hochschulen zuerst anzeigen.
add_filter( 'posts_search_orderby', function ( $search_orderby, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return $search_orderby;
	}

	global $wpdb;
	$priority = "{$wpdb->posts}.post_type = 'hochschulen' DESC";
	return $search_orderby ? $priority . ', ' . $search_orderby : $priority;
}, 10, 2 );

// Text "Archive:" entfernen bei Hochschulen und Förderern
add_filter( 'get_the_archive_title', function( $title ) {
	if ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}
	return $title;
});

// Die Website-Suche einschränken und Archive alphabetisch sortieren.
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_search() ) {
		$query->set( 'post_type', array( 'hochschulen', 'foerderer' ) );
	}

	if ( $query->is_archive() ) {
		$query->set( 'order', 'ASC' );
		$query->set( 'orderby', 'title' );
	}
} );

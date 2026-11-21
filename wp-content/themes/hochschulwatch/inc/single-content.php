<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Detailinformationen nur im Hauptinhalt der Einzelansicht ergänzen.
add_filter( 'the_content', function ( $content ) {
	if ( ! function_exists( 'get_field' )
		|| ! is_singular( array( 'hochschulen', 'foerderer', 'bundeslaender' ) )
		|| ! in_the_loop()
		|| ! is_main_query()
		|| get_the_ID() !== get_queried_object_id() ) {
		return $content;
	}

	$beitrag_id = get_the_ID();
	ob_start();
	if ( 'bundeslaender' === get_post_type( $beitrag_id ) ) {
		render_bundesland( $beitrag_id );
	} else {
		render_hochschule_foerderer( $beitrag_id );
	}
	return ob_get_clean() . $content;
} );

function render_bundesland( $beitrag_id ) {
	$wappen = get_field( 'wappen', $beitrag_id );
	$abfrage = new \WP_Query( array(
		'post_type' => 'hochschulen',
		'posts_per_page' => -1,
		'meta_key' => 'bundesland',
		'meta_value' => $beitrag_id,
		'orderby' => 'title',
		'order' => 'ASC',
	) );

	get_template_part( 'template-parts/single/bundesland', null, array(
		'wappen' => $wappen,
		'hochschulen' => $abfrage->posts,
	) );
}

function render_hochschule_foerderer( $beitrag_id ) {
	$ist_hochschule = 'hochschulen' === get_post_type( $beitrag_id );
	if ( $ist_hochschule ) {
		render_hochschulkopf( $beitrag_id );
		$jahreswerte = get_field( 'drittmittel', $beitrag_id );
		if ( $jahreswerte ) {
			get_template_part( 'template-parts/single/drittmittel', null, array(
				'jahreswerte' => $jahreswerte,
				'anteil' => drittmittelanteil( $jahreswerte ),
			) );
		}
	}

	$text = get_field( 'text', $beitrag_id );
	$feld = $ist_hochschule ? 'hochschule' : 'foerderer';
	$partnerfeld = $ist_hochschule ? 'foerderer' : 'hochschule';
	get_template_part( 'template-parts/single/foerderungen', null, array(
		'text' => $text,
		'gruppen' => foerderungen_nach_typ( $feld, $beitrag_id, $partnerfeld ),
	) );
}

function render_hochschulkopf( $beitrag_id ) {
	$bundesland = get_field( 'bundesland', $beitrag_id );
	get_template_part( 'template-parts/single/hochschulkopf', null, array(
		'bundesland' => $bundesland,
		'adresse' => get_field( 'adresse', $beitrag_id ),
		'hochschulart' => get_field( 'hochschulart', $beitrag_id ),
		'aktualisiert' => get_field( 'date_update', $beitrag_id ),
		'wappen' => $bundesland ? get_field( 'wappen', $bundesland->ID ) : '',
		'infos' => $bundesland ? get_field( 'infos', $bundesland->ID ) : array(),
	) );
}

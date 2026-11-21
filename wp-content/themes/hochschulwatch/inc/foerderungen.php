<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function foerderungstyp( $beitrag_id ) {
	return strtolower( trim( (string) get_post_meta( $beitrag_id, 'typ', true ) ) );
}

function foerderer_einer_foerderung( $beitrag_id, $typ ) {
	if ( 'stiftungsprofessur' === $typ ) {
		$zeilen = get_field( 'field_hsw_foerderer_liste', $beitrag_id ) ?: array();
		return array_column( $zeilen, 'foerderer' );
	}
	return array( get_field( 'foerderer', $beitrag_id ) );
}

function foerderungen_nach_typ( $beitrag_id, $ist_hochschule ) {
	$zuordnung = $ist_hochschule
		? array( array( 'key' => 'hochschule', 'value' => $beitrag_id ) )
		: array(
			'relation' => 'OR',
			array( 'key' => 'foerderer', 'value' => $beitrag_id ),
			array( 'key' => '^foerderer_liste_[0-9]+_foerderer$', 'compare_key' => 'REGEXP', 'value' => $beitrag_id ),
		);
	$abfrage = new \WP_Query( array(
		'post_type' => 'foerderungen',
		'posts_per_page' => -1,
		'no_found_rows' => true,
		'meta_query' => $zuordnung,
	) );

	$felder_nach_typ = array(
		'hochschulgremium' => array( 'funktion' ),
		'sponsoring' => array( 'leistung', 'period', 'amount' ),
		'stiftungsprofessur' => array( 'startdate', 'period', 'amount' ),
		'sonstiges' => array( 'amount' ),
	);

	$gruppen = array();
	foreach ( $abfrage->posts as $foerderung ) {
		$typ = foerderungstyp( $foerderung->ID );
		if ( ! isset( $felder_nach_typ[ $typ ] ) ) {
			continue;
		}

		$foerderer = foerderer_einer_foerderung( $foerderung->ID, $typ );
		// Beim Typwechsel kann ACF Werte der ausgeblendeten Felder behalten.
		if ( ! $ist_hochschule && ! in_array( (int) $beitrag_id, array_column( $foerderer, 'ID' ), true ) ) {
			continue;
		}

		$quellen = get_field( 'field_hsw_quellen', $foerderung->ID ) ?: array();
		$eintrag = array(
			'beitrag' => $foerderung,
			'partner' => $ist_hochschule ? $foerderer : array( get_field( 'hochschule', $foerderung->ID ) ),
			'foerderer' => $foerderer,
			'quellen' => array_column( $quellen, 'url' ),
		);
		foreach ( $felder_nach_typ[ $typ ] as $name ) {
			$eintrag[ $name ] = get_field( $name, $foerderung->ID );
		}
		$gruppen[ $typ ][] = $eintrag;
	}
	return $gruppen;
}

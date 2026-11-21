<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function foerderungen_nach_typ( $feld, $beitrag_id, $partnerfeld ) {
	$abfrage = new \WP_Query( array(
		'post_type' => 'foerderungen',
		'posts_per_page' => -1,
		'no_found_rows' => true,
		'meta_key' => $feld,
		'meta_value' => $beitrag_id,
	) );

	$felder_nach_typ = array(
		'hochschulgremium' => array( 'funktion' ),
		'sponsoring' => array( 'leistung', 'period', 'amount' ),
		'stiftungsprofessur' => array( 'startdate', 'period', 'amount' ),
		'sonstiges' => array( 'amount' ),
	);

	$gruppen = array();
	foreach ( $abfrage->posts as $foerderung ) {
		$typ = strtolower( trim( (string) get_post_meta( $foerderung->ID, 'typ', true ) ) );
		if ( ! isset( $felder_nach_typ[ $typ ] ) ) {
			continue;
		}

		$eintrag = array(
			'beitrag' => $foerderung,
			'partner' => get_field( $partnerfeld, $foerderung->ID ),
			'sourceurl' => get_field( 'sourceurl', $foerderung->ID ),
		);
		foreach ( $felder_nach_typ[ $typ ] as $name ) {
			$eintrag[ $name ] = get_field( $name, $foerderung->ID );
		}
		$gruppen[ $typ ][] = $eintrag;
	}
	return $gruppen;
}

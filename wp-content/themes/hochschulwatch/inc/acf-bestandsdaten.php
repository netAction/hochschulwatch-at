<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Vorhandene Kategorien im neuen Feld auch im Editor korrekt vorauswählen.
add_filter( 'acf/load_value/key=field_61576dfa54db6', function ( $wert ) {
	$alte_werte = array(
		'Universität' => 'universitaet',
		'Universitäten' => 'universitaet',
		'Uniklinik' => 'uniklinik',
		'Unikliniken' => 'uniklinik',
		'Öffentliche Hochschule' => 'oeffentliche_hochschule',
		'Fachhochschule' => 'fachhochschule',
		'Pädagogische Hochschule' => 'paedagogische_hochschule',
		'Privatuniversität' => 'privathochschule',
		'Privathochschule' => 'privathochschule',
		'Privathochschulen und Privatuniversitäten' => 'privathochschule',
	);
	return $alte_werte[ trim( (string) $wert ) ] ?? $wert;
} );

add_filter( 'acf/load_value/key=field_616c44cc48f9f', function ( $wert ) {
	$wert = trim( (string) $wert );
	foreach ( array( 'Ymd', 'Y-m-d', 'd.m.Y' ) as $format ) {
		$datum = \DateTimeImmutable::createFromFormat( '!' . $format, $wert );
		if ( $datum && $datum->format( $format ) === $wert ) {
			return $datum->format( 'Ymd' );
		}
	}
	return '';
} );

// Eine erstmals geladene Liste übernimmt den früheren Einzelwert.
// Eine gespeicherte leere Liste soll den alten Wert nicht wiederherstellen.
function lade_alte_repeater_zeile( $wert, $beitrag_id, $feld ) {
	if ( ! is_numeric( $beitrag_id ) || metadata_exists( 'post', (int) $beitrag_id, $feld['name'] ) ) {
		return $wert;
	}

	$metaschluessel = 'quellen' === $feld['name'] ? 'sourceurl' : 'foerderer';
	$alter_wert = trim( (string) get_post_meta( (int) $beitrag_id, $metaschluessel, true ) );
	if ( '' === $alter_wert ) {
		return $wert;
	}
	return array( array( $feld['sub_fields'][0]['key'] => $alter_wert ) );
}
add_filter( 'acf/load_value/key=field_hsw_quellen', __NAMESPACE__ . '\lade_alte_repeater_zeile', 10, 3 );
add_filter( 'acf/load_value/key=field_hsw_foerderer_liste', __NAMESPACE__ . '\lade_alte_repeater_zeile', 10, 3 );

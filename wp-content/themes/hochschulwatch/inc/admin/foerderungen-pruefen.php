<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	add_options_page( 'Förderer und Förderungen prüfen', 'Förderer und Förderungen prüfen', 'manage_options', 'hochschulwatch-fehleranzeige', __NAMESPACE__ . '\render_foerderungspruefung' );
});

function pruefe_foerderungen() {
	$status = array( 'publish', 'private', 'future', 'draft', 'pending' );
	$foerderungen = get_posts( array(
		'post_type' => 'foerderungen',
		'post_status' => $status,
		'posts_per_page' => -1,
	) );
	$foerderer = get_posts( array(
		'post_type' => 'foerderer',
		'post_status' => $status,
		'posts_per_page' => -1,
	) );

	$fehlende_zuordnungen = array();
	$zugeordnete_foerderer = array();
	foreach ( $foerderungen as $foerderung ) {
		$hochschule = get_field( 'hochschule', $foerderung->ID );
		if ( ! $hochschule || 'hochschulen' !== $hochschule->post_type || ! in_array( $hochschule->post_status, $status, true ) ) {
			$fehlende_zuordnungen[] = array( 'beitrag' => $foerderung, 'feld' => 'Hochschule' );
		}

		$partnerliste = foerderer_einer_foerderung( $foerderung->ID, foerderungstyp( $foerderung->ID ) );
		$gueltige_partner = 0;
		foreach ( $partnerliste as $partner ) {
			if ( $partner && 'foerderer' === $partner->post_type && in_array( $partner->post_status, $status, true ) ) {
				$zugeordnete_foerderer[ $partner->ID ] = true;
				++$gueltige_partner;
			}
		}
		if ( 0 === $gueltige_partner || $gueltige_partner < count( $partnerliste ) ) {
			$fehlende_zuordnungen[] = array( 'beitrag' => $foerderung, 'feld' => 'Förderer' );
		}
	}

	$foerderer_ohne_zuordnung = array();
	foreach ( $foerderer as $partner ) {
		if ( ! isset( $zugeordnete_foerderer[ $partner->ID ] ) ) {
			$foerderer_ohne_zuordnung[] = $partner;
		}
	}
	return array( $fehlende_zuordnungen, $foerderer_ohne_zuordnung );
}

function render_foerderungspruefung() {
	list( $fehlende_zuordnungen, $foerderer_ohne_zuordnung ) = pruefe_foerderungen();
	get_template_part( 'template-parts/admin/foerderungen-pruefen', null, array(
		'fehlende_zuordnungen' => $fehlende_zuordnungen,
		'foerderer_ohne_zuordnung' => $foerderer_ohne_zuordnung,
	) );
}

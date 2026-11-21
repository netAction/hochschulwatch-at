<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function drittmittelanteil( $jahreswerte ) {
	$neuestes_jahr = 0;
	$anteil = null;
	foreach ( $jahreswerte as $zeile ) {
		$jahr = (int) ( $zeile['jahr'] ?? 0 );
		$wirtschaft = $zeile['wirtschaft'] ?? null;
		$gesamt = $zeile['absolut'] ?? null;

		// Bei mehreren gültigen Zeilen desselben Jahres zählt die letzte.
		if ( $jahr <= 0 || $jahr < $neuestes_jahr || ! is_numeric( $wirtschaft ) || ! is_numeric( $gesamt )
			|| $gesamt <= 0 || $wirtschaft < 0 || $wirtschaft > $gesamt ) {
			continue;
		}

		$neuestes_jahr = $jahr;
		$anteil = (int) round( $wirtschaft * 100 / $gesamt );
	}
	return $anteil;
}

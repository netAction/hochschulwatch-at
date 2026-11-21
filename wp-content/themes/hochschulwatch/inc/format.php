<?php

namespace Hochschulwatch\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function eurobetrag( $betrag ) {
	return number_format( (int) $betrag, 0, ',', '&#8239;' ) . '&#8239;€';
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


$currenttype = '';
if (get_post_type() == 'hochschulen') {
	// Hochschule
	show_hochschulkopf();
	$currenttype = 'hochschule';
} else {
	// Förderer
	$currenttype = 'foerderer';
}


function eurobetrag($number) {
	$number = intval($number);
	$number = number_format($number, 0, ',', '&#8239;');
	$number = $number.'&#8239;€';
	return $number;
}

function show_hochschulkopf() {
	echo '<div class="table__wrapper alignright"><table class="table" style="max-width: 300px;">';
	$wert = get_field('bundesland');
	if ($wert) {
		echo '<tr><td>Bundesland</td><td>';
		echo '<img src="'.esc_url(get_field('wappen', $wert)).'" class="bundeslandwappen">';
		echo '<a href="' . esc_url( get_permalink( $wert ) ) . '">';
		echo esc_html( get_the_title( $wert ) );
		echo '</a>';
		echo '</td></tr>'. "\n";
	}

	$wert = trim(get_field('adresse'));
	if ($wert) {
		echo '<tr><td>Adresse</td><td>';
		echo '<a href="https://www.google.de/maps?q='.urlencode($wert).'" target="_blank">';
		echo esc_html( $wert );
		echo '</a>'."\n";
		echo '</td></tr>'. "\n";
	}

	$wert = trim(get_field('hochschulart'));
	if ($wert) {
		echo '<tr><td>Hochschulart</td><td>';
		echo esc_html( $wert );
		echo '</td></tr>'. "\n";
	}

	if (get_field('date_update')) $wert = trim(get_field('date_update')); else $wert = '';
	if ($wert) {
		echo '<tr><td>letzte Aktualisierung</td><td>';
		echo esc_html( $wert );
		echo '</td></tr>'. "\n";
	}
	echo '</table></div>';


	if( have_rows('drittmittel') ):
		$drittmitteltabelle = '<div class="table__wrapper"><table style="max-width: 400px;" class="table">';
		$drittmitteltabelle.= '<thead><tr><th class="text-left">Jahr</th><th class="text-right">Wirtschaft</th><th class="text-right">Absolut</th></tr></thead>'."\n";
		$drittmitteltabelle.= '<tbody>'."\n";
		$last_wirtschaft = 0;
		$last_absolut = 0;
		while( have_rows('drittmittel') ) : the_row();
			// Load sub field value.
			$drittmitteltabelle.= '<tr><td>';
			$drittmitteltabelle.= intval( get_sub_field('jahr') );
			$drittmitteltabelle.= '</td><td class="text-right">';
			$last_wirtschaft = get_sub_field('wirtschaft');
			$drittmitteltabelle.= eurobetrag($last_wirtschaft). "\n";
			$drittmitteltabelle.= '</td><td class="text-right">';
			$last_absolut = get_sub_field('absolut');
			$drittmitteltabelle.= eurobetrag($last_absolut). "\n";
			$drittmitteltabelle.= '</td></tr>'. "\n";
		endwhile;
		$drittmitteltabelle.= '</tbody></table></div>';

		echo '<h2>Anteil der gewerblichen Wirtschaft an den Drittmitteln';
		if ($last_absolut && ($last_wirtschaft < $last_absolut)) {
			echo ': <strong>';
			echo round( $last_wirtschaft * 100 / $last_absolut).'&#8239;%';
			echo '</strong>';
		}
		echo '</h2>';

		echo $drittmitteltabelle;
		echo '<br><br>';
	endif;

}


function get_foerderungen( $parent, $typ ) {
	$loop = new WP_Query( array(
		'post_type' => 'foerderungen',
		'posts_per_page' => -1,
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'	 	=> $parent, // hochschule oder foerderer
				'value'	  	=> get_the_ID(),
				'compare' 	=> '=',
			),
			array(
				'key'	  	=> 'typ',
				'value'	  	=> $typ, // Hochschulgremium Sonstiges Sponsoring Stiftungsprofessur
				'compare' 	=> '=',
			),
		),
	));

	return $loop;
}


function the_partnerlink( $currenttype ) {
	// Partner-Post
	if ($currenttype === 'hochschule') {
		$partner = get_field('foerderer');
		// Evtl schneller:
		// get_post(get_post_meta(get_the_ID(), 'foerderer'));
	} else {
		$partner = get_field('hochschule');
		// Evtl schneller:
		// get_post(get_post_meta(get_the_ID(), 'hochschule'));
	}
	echo '<a href="' . esc_url( get_permalink( $partner ) ) . '">'.
	esc_html( get_the_title( $partner ) ).
	'</a>';	
}



function show_hochschulgremium( $loop, $currenttype ) {
	if ($loop->have_posts()) {
		echo '<h2>Hochschulgremien <a href="/hochschulgremien/" class="button button--small button--super">Infos</a></h2>'."\n";

		echo '<div class="table__wrapper"><table class="table"><tbody>'."\n";

		while ( $loop->have_posts() ) : $loop->the_post();
			echo '<tr><td>'."\n";
			// Name der Person
			edit_post_link( '✍', '', ' ', $loop->ID, '' );
			echo esc_html( get_the_title() );

			$funktion = trim(get_field('funktion'));
			if ($funktion) {
				echo ' ('.esc_html($funktion).')';
			}

			$quelle = trim(get_field('sourceurl'));
			if ($quelle) {
				echo ' <a href="'.esc_url($quelle).'" target="_blank" class="button button--small button--gray">Quelle</a>';
			}

			echo '</td><td>'."\n";
			the_partnerlink( $currenttype );

			echo '</td></tr>'."\n";
		endwhile;
		echo '</tbody></table></div><br><br>'."\n";
		wp_reset_query();
	}
}



function show_sponsoring( $loop, $currenttype ) {
	if ($loop->have_posts()) {
		echo '<h2>Sponsoring <a href="/sponsoring/" class="button button--small button--super">Infos</a></h2>'."\n";

		echo '<div class="table__wrapper"><table class="table"><thead>'."\n";
		echo '<tr><th>Geber</th><th class="text-right">Leistung</th><th></th></tr>'. "\n";
		echo '</thead><tbody>'."\n";

		while ( $loop->have_posts() ) : $loop->the_post();
			echo '<tr><td>'."\n";

			the_partnerlink( $currenttype );

			echo '</td><td class="text-right">'."\n";
			// Leistung
			echo esc_html( trim(get_field('leistung') ) );

			// Zeitraum
			$wert = trim(get_field('period') );
			if ($wert) {
				echo '<br>Zeit: ';
				echo esc_html( $wert );
			}


			// Wert
			$wert = get_field('amount');
			if ($wert) {
				echo '<br>';
				echo eurobetrag(get_field('amount'))."\n";
			}

			echo '</td><td>';
			edit_post_link( '✍', '', ' ', $loop->ID, '' );
			echo esc_html( get_the_title() );

			$quelle = trim(get_field('sourceurl'));
			if ($quelle) {
				echo ' <a href="'.esc_url($quelle).'" target="_blank" class="button button--small button--gray">Quelle</a>';
			}


			echo '</td></tr>'."\n";
		endwhile;
		echo '</tbody></table></div><br><br>'."\n";
		wp_reset_query();
	}
}




function show_stiftungsprofessur( $loop, $currenttype ) {
	if ($loop->have_posts()) {
		echo '<h2>Stiftungsprofessuren <a href="/stiftungsprofessuren/" class="button button--small button--super">Infos</a></h2>'."\n";

		while ( $loop->have_posts() ) : $loop->the_post();

			echo '<h3>';
			edit_post_link( '✍', '', ' ', $loop->ID, '' );
			echo esc_html( get_the_title() );

			$wert = trim(get_field('sourceurl'));
			if ($wert) {
				echo ' <a href="'.esc_url($wert).'" target="_blank" class="button button--small button--super button--gray">Quelle</a><br>'."\n";
			}
			echo '</h3>'."\n";

			echo 'Stifter: ';
			the_partnerlink( $currenttype );
			echo "<br>\n";

			$wert = trim(get_field('startdate'));
			if ($wert) {
				echo 'Startzeitpunkt: '.esc_html($wert)."<br>\n";
			}
			$wert = trim(get_field('period'));
			if ($wert) {
				echo 'Laufzeit: '.esc_html($wert)."<br>\n";
			}
			$wert = get_field('amount');
			if ($wert) {
				echo 'Fördersumme insgesamt: '.eurobetrag($wert)."<br>\n";
			}
			echo '<br>'."\n";
		endwhile;
		echo '<br><br>';
		wp_reset_query();
	}
}


function show_sonstiges( $loop, $currenttype ) {
	if ($loop->have_posts()) {
		echo '<h2>Sonstiges <a href="/sonstige-foerderungen/" class="button button--small button--super">Infos</a></h2>'."\n";

		echo '<div class="table__wrapper"><table class="table"><tbody>'."\n";
		while ( $loop->have_posts() ) : $loop->the_post();

			echo '<tr><td>'."\n";
				the_partnerlink( $currenttype );

			echo '</td><td>'."\n";
			// Titel
			edit_post_link( '✍', '', ' ', $loop->ID, '' );
			echo esc_html( get_the_title() );

			// Quelle
			$wert = trim(get_field('sourceurl'));
			if ($wert) {
				echo ' <a href="'.esc_url($wert).'" target="_blank" class="button button--small button--gray">Quelle</a>';
			}

			// Wert
			$wert = get_field('amount');
			if ($wert > 0) {
				echo '<br><em>Wert: '.eurobetrag($wert)."</em>\n";
			}

			echo '</td></tr>'."\n";

		endwhile;
		echo '</tbody></table></div><br><br>'."\n";
		wp_reset_query();
	}
}



$text = get_field('text');
if ($text) {
	echo '<div class="graybox">';
	echo wp_kses_post( $text );
	echo '</div>';
}

show_hochschulgremium( get_foerderungen($currenttype, 'hochschulgremium'), $currenttype );
show_sponsoring( get_foerderungen($currenttype, 'sponsoring'), $currenttype );
show_stiftungsprofessur( get_foerderungen($currenttype, 'stiftungsprofessur'), $currenttype );
show_sonstiges( get_foerderungen($currenttype, 'sonstiges'), $currenttype );


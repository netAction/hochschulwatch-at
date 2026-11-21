<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fehlende_zuordnungen = $args['fehlende_zuordnungen'];
$foerderer_ohne_zuordnung = $args['foerderer_ohne_zuordnung'];
?>
<div class="wrap">
	<h1>Förderer und Förderungen prüfen</h1>
	<p>Die Prüfung berücksichtigt veröffentlichte, private und geplante Beiträge sowie Entwürfe und Beiträge, die zur Prüfung vorliegen.</p>
	<h2>Förderungen mit fehlenden Zuordnungen</h2>
	<?php if ( $fehlende_zuordnungen ) : ?>
		<ul class="ul-disc">
			<?php foreach ( $fehlende_zuordnungen as $fall ) : ?>
				<li>
					<?php
					$beitrag = $fall['beitrag'];
					echo esc_html( $fall['feld'] . ' fehlt: ' . get_the_title( $beitrag ) );
					edit_post_link( 'Bearbeiten', ' ', '', $beitrag->ID );
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p>Keine fehlenden Zuordnungen gefunden.</p>
	<?php endif; ?>

	<h2>Förderer ohne zugeordnete Förderung</h2>
	<?php if ( $foerderer_ohne_zuordnung ) : ?>
		<ul class="ul-disc">
			<?php foreach ( $foerderer_ohne_zuordnung as $partner ) : ?>
				<li>
					<?php
					echo esc_html( get_the_title( $partner ) );
					edit_post_link( 'Bearbeiten', ' ', '', $partner->ID );
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p>Keine Förderer ohne Zuordnung gefunden.</p>
	<?php endif; ?>
</div>

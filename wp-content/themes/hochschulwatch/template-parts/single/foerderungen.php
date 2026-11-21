<?php

use function Hochschulwatch\Theme\eurobetrag;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$partnerlink = static function ( $partner ) {
	if ( $partner ) {
		echo '<a href="' . esc_url( get_permalink( $partner ) ) . '">' . esc_html( get_the_title( $partner ) ) . '</a>';
	}
};

$quellenlink = static function ( $url, $oberhalb = false ) {
	$quelle = trim( (string) $url );
	if ( $quelle ) {
		$klasse = $oberhalb ? 'button button--small button--super button--gray' : 'button button--small button--gray';
		echo ' <a href="' . esc_url( $quelle ) . '" target="_blank" class="' . esc_attr( $klasse ) . '">Quelle</a>';
		if ( $oberhalb ) {
			echo '<br>';
		}
	}
};

$gruppen = $args['gruppen'];
?>
<?php if ( $args['text'] ) : ?>
	<div class="graybox"><?php echo wp_kses_post( $args['text'] ); ?></div>
<?php endif; ?>
<?php if ( ! empty( $gruppen['hochschulgremium'] ) ) : ?>
	<h2>Hochschulgremien <a href="/hochschulgremien/" class="button button--small button--super">Infos</a></h2>
	<div class="table__wrapper"><table class="table"><tbody>
		<?php foreach ( $gruppen['hochschulgremium'] as $eintrag ) : ?>
			<?php $foerderung = $eintrag['beitrag']; ?>
			<tr>
				<td>
					<?php
					edit_post_link( '✍', '', ' ', $foerderung->ID, '' );
					echo esc_html( get_the_title( $foerderung ) );
					$funktion = trim( (string) $eintrag['funktion'] );
					if ( $funktion ) {
						echo ' (' . esc_html( $funktion ) . ')';
					}
					$quellenlink( $eintrag['sourceurl'] );
					?>
				</td>
				<td><?php $partnerlink( $eintrag['partner'] ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody></table></div><br><br>
<?php endif; ?>

<?php if ( ! empty( $gruppen['sponsoring'] ) ) : ?>
	<h2>Sponsoring <a href="/sponsoring/" class="button button--small button--super">Infos</a></h2>
	<div class="table__wrapper"><table class="table">
		<thead><tr><th>Geber</th><th class="text-right">Leistung</th><th></th></tr></thead>
		<tbody>
			<?php foreach ( $gruppen['sponsoring'] as $eintrag ) : ?>
				<?php $foerderung = $eintrag['beitrag']; ?>
				<tr>
					<td><?php $partnerlink( $eintrag['partner'] ); ?></td>
					<td class="text-right">
						<?php
						echo esc_html( trim( (string) $eintrag['leistung'] ) );
						$zeitraum = trim( (string) $eintrag['period'] );
						if ( $zeitraum ) {
							echo '<br>Zeit: ' . esc_html( $zeitraum );
						}
						$betrag = $eintrag['amount'];
						if ( $betrag ) {
							echo '<br>' . eurobetrag( $betrag );
						}
						?>
					</td>
					<td>
						<?php
						edit_post_link( '✍', '', ' ', $foerderung->ID, '' );
						echo esc_html( get_the_title( $foerderung ) );
						$quellenlink( $eintrag['sourceurl'] );
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table></div><br><br>
<?php endif; ?>

<?php if ( ! empty( $gruppen['stiftungsprofessur'] ) ) : ?>
	<h2>Stiftungsprofessuren <a href="/stiftungsprofessuren/" class="button button--small button--super">Infos</a></h2>
	<?php foreach ( $gruppen['stiftungsprofessur'] as $eintrag ) : ?>
		<?php $foerderung = $eintrag['beitrag']; ?>
		<h3>
			<?php
			edit_post_link( '✍', '', ' ', $foerderung->ID, '' );
			echo esc_html( get_the_title( $foerderung ) );
			$quellenlink( $eintrag['sourceurl'], true );
			?>
		</h3>
		Stifter: <?php $partnerlink( $eintrag['partner'] ); ?><br>
		<?php
		$beginn = trim( (string) $eintrag['startdate'] );
		if ( $beginn ) {
			echo 'Startzeitpunkt: ' . esc_html( $beginn ) . '<br>';
		}
		$zeitraum = trim( (string) $eintrag['period'] );
		if ( $zeitraum ) {
			echo 'Laufzeit: ' . esc_html( $zeitraum ) . '<br>';
		}
		$betrag = $eintrag['amount'];
		if ( $betrag ) {
			echo 'Fördersumme insgesamt: ' . eurobetrag( $betrag ) . '<br>';
		}
		?>
		<br>
	<?php endforeach; ?>
	<br><br>
<?php endif; ?>

<?php if ( ! empty( $gruppen['sonstiges'] ) ) : ?>
	<h2>Sonstiges <a href="/sonstige-foerderungen/" class="button button--small button--super">Infos</a></h2>
	<div class="table__wrapper"><table class="table"><tbody>
		<?php foreach ( $gruppen['sonstiges'] as $eintrag ) : ?>
			<?php $foerderung = $eintrag['beitrag']; ?>
			<tr>
				<td><?php $partnerlink( $eintrag['partner'] ); ?></td>
				<td>
					<?php
					edit_post_link( '✍', '', ' ', $foerderung->ID, '' );
					echo esc_html( get_the_title( $foerderung ) );
					$quellenlink( $eintrag['sourceurl'] );
					$betrag = $eintrag['amount'];
					if ( $betrag > 0 ) {
						echo '<br><em>Wert: ' . eurobetrag( $betrag ) . '</em>';
					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody></table></div><br><br>
<?php endif; ?>

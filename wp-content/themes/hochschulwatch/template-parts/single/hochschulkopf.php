<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bundesland = $args['bundesland'];
$adresse = trim( (string) $args['adresse'] );
$hochschulart = trim( (string) $args['hochschulart'] );
$aktualisiert = trim( (string) $args['aktualisiert'] );
?>
<div class="table__wrapper alignright">
	<table class="table" style="max-width: 300px;">
		<?php if ( $bundesland ) : ?>
			<tr>
				<td>Bundesland</td>
				<td>
					<img src="<?php echo esc_url( $args['wappen'] ); ?>" class="bundeslandwappen">
					<a href="<?php echo esc_url( get_permalink( $bundesland ) ); ?>"><?php echo esc_html( get_the_title( $bundesland ) ); ?></a>
				</td>
			</tr>
			<?php foreach ( $args['infos'] ?: array() as $info ) : ?>
				<?php
				$name = trim( $info['name'] ?? '' );
				$wert = trim( $info['wert'] ?? '' );
				if ( '' === $name && '' === $wert ) {
					continue;
				}
				?>
				<tr><td><?php echo esc_html( $name ); ?></td><td><?php echo esc_html( $wert ); ?></td></tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ( $adresse ) : ?>
			<tr>
				<td>Adresse</td>
				<td><a href="<?php echo esc_url( 'https://www.google.de/maps?q=' . urlencode( $adresse ) ); ?>" target="_blank"><?php echo esc_html( $adresse ); ?></a></td>
			</tr>
		<?php endif; ?>
		<?php if ( $hochschulart ) : ?>
			<tr><td>Hochschulart</td><td><?php echo esc_html( $hochschulart ); ?></td></tr>
		<?php endif; ?>
		<?php if ( $aktualisiert ) : ?>
			<tr><td>letzte Aktualisierung</td><td><?php echo esc_html( $aktualisiert ); ?></td></tr>
		<?php endif; ?>
	</table>
</div>

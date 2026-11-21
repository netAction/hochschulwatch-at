<?php

use function Hochschulwatch\Theme\eurobetrag;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$anteil = $args['anteil'];
$jahreswerte = $args['jahreswerte'];
?>
<h2>Anteil der privaten Wirtschaft an den Drittmitteln<?php if ( null !== $anteil ) : ?>: <strong><?php echo (int) $anteil; ?>&#8239;%</strong><?php endif; ?></h2>
<div class="table__wrapper">
	<table style="max-width: 640px;" class="table">
		<thead>
			<tr>
				<th class="text-left">Jahr</th>
				<th class="text-right">Private Wirtschaft Euro</th>
				<th class="text-right">Gesamte Drittmittel Euro</th>
				<th class="text-right">Gesamte Einnahmen Euro</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $jahreswerte as $zeile ) : ?>
				<tr>
					<td><?php echo (int) $zeile['jahr']; ?></td>
					<td class="text-right"><?php echo eurobetrag( $zeile['wirtschaft'] ); ?></td>
					<td class="text-right"><?php echo eurobetrag( $zeile['absolut'] ); ?></td>
					<td class="text-right"><?php echo is_numeric( $zeile['einnahmen'] ?? null ) ? eurobetrag( $zeile['einnahmen'] ) : '—'; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div><br><br>

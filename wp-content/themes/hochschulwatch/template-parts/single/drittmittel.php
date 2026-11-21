<?php

use function Hochschulwatch\Theme\eurobetrag;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$anteil = $args['anteil'];
$jahreswerte = $args['jahreswerte'];
?>
<h2>Anteil der gewerblichen Wirtschaft an den Drittmitteln<?php if ( null !== $anteil ) : ?>: <strong><?php echo (int) $anteil; ?>&#8239;%</strong><?php endif; ?></h2>
<div class="table__wrapper">
	<table style="max-width: 400px;" class="table">
		<thead><tr><th class="text-left">Jahr</th><th class="text-right">Wirtschaft</th><th class="text-right">Absolut</th></tr></thead>
		<tbody>
			<?php foreach ( $jahreswerte as $zeile ) : ?>
				<tr>
					<td><?php echo (int) $zeile['jahr']; ?></td>
					<td class="text-right"><?php echo eurobetrag( $zeile['wirtschaft'] ); ?></td>
					<td class="text-right"><?php echo eurobetrag( $zeile['absolut'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div><br><br>

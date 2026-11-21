<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<img src="<?php echo esc_url( $args['wappen'] ); ?>" class="bundeslandwappen"><br>
<?php if ( $args['hochschulen'] ) : ?>
	<?php foreach ( $args['hochschulen'] as $hochschule ) : ?>
		<h2 class="entry-title"><a href="<?php echo esc_url( get_permalink( $hochschule ) ); ?>"><?php echo esc_html( get_the_title( $hochschule ) ); ?></a></h2>
		<br>
	<?php endforeach; ?>
<?php else : ?>
	<p>Keine Hochschulen in diesem Bundesland.</p>
<?php endif; ?>

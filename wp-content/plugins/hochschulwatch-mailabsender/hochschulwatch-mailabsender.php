<?php
/**
 * Plugin Name: Hochschulwatch Mailabsender
 */

add_filter( 'wp_mail_from', static fn () => 'schmidt@netaction.de' );

add_action( 'phpmailer_init', static function ( $phpmailer ) {
	$phpmailer->Sender = $phpmailer->From;
} );

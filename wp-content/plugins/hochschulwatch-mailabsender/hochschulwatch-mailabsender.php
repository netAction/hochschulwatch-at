<?php
/**
 * Plugin Name: Hochschulwatch Mailabsender
 */

add_filter( 'wp_mail_from', static fn () => 'schmidt@netaction.de' );

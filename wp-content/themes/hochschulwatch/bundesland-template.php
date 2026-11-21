<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '<img src="'.esc_url(get_field('wappen')).'" class="bundeslandwappen"><br>';

$loop = new WP_Query( array(
	'post_type' => 'hochschulen',
	'posts_per_page' => -1,
	'meta_query'	=> array(
		'relation'		=> 'AND',
		array(
			'key'	 	=> 'bundesland',
			'value'	  	=> get_the_ID(),
			'compare' 	=> '=',
		),
	),
	'orderby' => 'title',
	'order'   => 'ASC',
));


if ($loop->have_posts()) {
	while ( $loop->have_posts() ) : $loop->the_post();
		echo '<h2 class="entry-title">';
		echo '<a href="' . esc_url( get_permalink()) . '">';
		echo esc_html( get_the_title() );
		echo '</a></h2>'."\n";
		echo '<br>';
	endwhile;

	wp_reset_query();
} else {
	echo '<p>Keine Hochschulen in diesem Bundesland.</p>';
}
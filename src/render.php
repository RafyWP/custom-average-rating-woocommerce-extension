<?php
/**
 * Render callback for the Custom Average Rating WooCommerce Extension block.
 *
 * Retrieves the average rating data from a given API endpoint and displays it as a star rating.
 *
 * @since 1.0.0
 * @author Lit ✴ Code
 *
 * @param array $attributes Block attributes.
 * @return string HTML output of the block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure $attributes is defined.
if ( ! isset( $attributes ) ) {
	$attributes = array();
}

if ( empty( $attributes['apiEndpoint'] ) ) {
	echo '<p>' . esc_html__( 'No API endpoint provided.', 'custom-average-rating' ) . '</p>';
	return;
}

if ( isset( $attributes['apiEndpoint'] ) && str_contains( $attributes['apiEndpoint'], 'product_rating' ) ) {
	global $product;
	$product_id = $product ? $product->get_id() : get_the_ID();
	$endpoint 	= substr( $attributes['apiEndpoint'], 0, -1 ) . $product_id;
} else {
	$endpoint 	= $attributes['apiEndpoint'];
}

// Retrieve data from the API endpoint.
$env 	  = wp_get_environment_type() !== 'development';
$response = wp_remote_get( esc_url_raw( $endpoint ), ['sslverify' => $env] );
if ( is_wp_error( $response ) ) {
	echo '<p>' . esc_html__( 'Error retrieving rating data.', 'custom-average-rating' ) . '</p>';
	return;
}

$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );

if ( empty($data) || ( !isset( $data['average_rating'] ) && !isset( $data['star_rating'] ) ) ) {
    echo '<p>' . esc_html__('Rating data not found.', 'custom-average-rating') . '</p>';
    return;
}

$average_rating = isset($data['average_rating']) ? floatval($data['average_rating']) : null;
$star_rating 	= isset($data['star_rating']) ? floatval($data['star_rating']) : null;

$rating     = $star_rating ?? $average_rating;
$total     	= intval( $data['total_products'] );
$fullStars  = floor( $rating );
$emptyStars = 5 - $fullStars;

$total_reviews_txt = esc_html__( 'Rating: %d/5', 'custom-average-rating' );

$svg_full   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="gold" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
$svg_empty  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="lightgray" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';

$link   = esc_url_raw( $attributes['clickUrl'] );
$tag_op = $link ? 'a href="' . $link . '"' : 'div';
$tag_cl = $link ? 'a' : 'div';

$comments = $data['comments'];
$reviews  = '';
$z_index  = 5;
foreach ( $comments as $comment ) {
	$reviews .= '<a href="#comment-' . $comment['comment_id'] . '">';
	$reviews .= '<img src="' . $comment['avatar'] . '" width="38" height="38" style="z-index:' . $z_index . ';" />';
	$reviews .= '</a>';
	$z_index--;
}

$html  = '<div class="new-star-rating">';
/*
$html .= '<span class="number">';
$html .= number_format( esc_html( $rating ), 1, '.' );
$html .= '</span>';
*/
$html .= '<' . $tag_op . ' class="rating">';
$html .= '<div class="first-line">';
$html .= '<div class="stars">';
$html .= str_repeat( $svg_full, $fullStars );
$html .= str_repeat( $svg_empty, $emptyStars );
$html .= '</div>';
$html .= '</div>';
$html .= '<div class="sec-line">';
$html .= wp_sprintf( $total_reviews_txt, esc_html( $rating ) );
$html .= '</div>';
$html .= '</' . $tag_cl . '>';
$html .= '<div class="avatars">';
$html .= '<div class="first-line">';
$html .= $reviews;
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

echo $html;

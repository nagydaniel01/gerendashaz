<?php
$term        = $args['term'];

if (empty($term)) {
    return;
}

$term_id     = $term->term_id;
$taxonomy    = $term->taxonomy;
$term_link   = get_term_link($term);
$title       = $term->name;
//$description = term_description($term_id, $taxonomy);

$image_id  = '';
$alt_text  = __('', 'gerendashaz');

// If taxonomy is 'product_cat', get WooCommerce thumbnail
if ($taxonomy === 'product_cat') {
    $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
    if ($thumbnail_id) {
        $image_id = $thumbnail_id;
        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: $title;
    }
}

// Otherwise, use ACF gallery if available
if ($taxonomy !== 'product_cat') {
    $gallery = get_field('gallery', $taxonomy . '_' . $term_id);

    if ($gallery && is_array($gallery)) {
        $first_image = $gallery[0];

        if (is_numeric($first_image)) {
            $image_id = $first_image;
            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;
        } elseif (is_array($first_image) && !empty($first_image['ID'])) {
            $image_id = $first_image['ID'];
            $alt_text = !empty($first_image['alt']) ? $first_image['alt'] : $title;
        }
    }
}

$classes = 'card card--term';
if ($taxonomy) {
    $classes = ' card--' . $taxonomy;
}

do_action('theme_card_open', [
    'post_id' => $post_id,
    'classes' => $classes
]);

do_action('theme_card_link_open', [
    'term_id'  => $term_id,
    'taxonomy' => $taxonomy,
]);

do_action('theme_card_header', [
    'image_id' => $image_id,
    'alt_text' => $alt_text,
]);

do_action('theme_card_content_open');

do_action('theme_card_title', [
    'card_title' => $title,
]);

do_action('theme_card_content_close');

do_action('theme_card_link_close');

do_action('theme_card_close');

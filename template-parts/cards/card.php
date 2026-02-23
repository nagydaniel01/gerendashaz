<?php
$card_image       = $args['card_image'] ?? [];
$card_title       = $args['card_title'] ?? '';
$card_slug        = sanitize_title($card_title);
$card_description = $args['card_description'] ?? '';
$card_button      = $args['card_button'] ?? [];

$image_id      = $card_image['ID'] ?? 0;
$button_url    = $card_button['url'] ?? '';
$button_title  = $card_button['title'] ?? esc_url($button_url);
$button_target = isset($card_button['target']) && $card_button['target'] !== '' ? $card_button['target'] : '_self';

// Detect image-only card (empty title & description)
$is_image_card = empty($card_title) && empty($card_description);

// Open card wrapper
do_action('theme_card_open', [
    'post_id'  => $card_slug,
    'classes'  => 'card' . ($is_image_card ? ' card--image' : ''),
    'data_aos' => 'fade-up',
]);

// Render header image if exists
if ($image_id) {
    if ($is_image_card && $button_url) {
        // Wrap the image in a link if it's an image-only card with a button
        do_action('theme_card_link_open', [
            'url'    => esc_url($button_url),
            'target' => esc_attr($button_target),
            'classes' => '',
        ]);

        do_action('theme_card_header', [
            'image_id' => $image_id,
            'alt_text' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: esc_html($button_title),
        ]);
        
        do_action('theme_card_link_close');
    } else {
        do_action('theme_card_header', [
            'image_id' => $image_id,
            'alt_text' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        ]);
    }
}

// Render content only if not an image-only card
if (!$is_image_card) {
    do_action('theme_card_content_open');

    if (!empty($card_title)) {
        do_action('theme_card_title', ['card_title' => $card_title]);
    }

    if (!empty($card_description)) {
        do_action('theme_card_description', ['card_description' => $card_description]);
    }

    if ($button_url) {
        do_action('theme_card_button', ['card_button' => $card_button]);
    }

    do_action('theme_card_content_close');
}

// Close card wrapper
do_action('theme_card_close');

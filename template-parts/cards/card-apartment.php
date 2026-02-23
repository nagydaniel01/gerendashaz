<?php
    $post_type    = $args['post_type'] ?? '';

    if (empty($post_type)) {
        return;
    }

    $current_user_id = get_current_user_id();

    $post_id      = get_the_ID();
    $title        = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $image_id     = $thumbnail_id ?? null;
    $categories   = get_the_terms($post_id, 'category');
    $alt_text     = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;

    if (is_wp_error($categories)) {
        $categories = [];
    }

    $classes = 'card';
    if ($post_type) {
        $classes .= ' card--' . $post_type;
    }
?>

<?php do_action('theme_card_open', [
    'post_id' => $post_id,
    'classes' => $classes
]); ?>

    <?php do_action('theme_card_link_open', ['post_id' => $post_id]); ?>

        <?php
        do_action('theme_card_header', [
            'image_id' => $image_id,
            'alt_text' => $alt_text
        ]);
        ?>

        <?php do_action('theme_card_content_open'); ?>

            <?php
            do_action('theme_card_title', [
                'card_title' => $title
            ]);

            do_action('theme_card_description', [
                'card_description' => get_the_excerpt()
            ]);

            do_action('theme_card_meta', [
                'post_id' => $post_id,
                'show_category' => true,
                'show_date'     => false,
            ]);
            ?>

            <button type="button" class="btn btn-primary card__button"><?php echo esc_html__('Book now', 'gerendashaz'); ?></button>

        <?php do_action('theme_card_content_close'); ?>

    <?php do_action('theme_card_link_close'); ?>

<?php do_action('theme_card_close'); ?>

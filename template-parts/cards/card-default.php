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
                'show_date'     => true,
            ]);
            ?>

            <span class="card__button">
                <svg class="icon icon-arrow-right">
                    <use xlink:href="#icon-arrow-right"></use>
                </svg>
            </span>

        <?php do_action('theme_card_content_close'); ?>

    <?php do_action('theme_card_link_close'); ?>

    <?php
    // Bookmark button stays custom (since it’s logic-heavy)
    $current_user_id = get_current_user_id();

    if (!is_user_logged_in()) :
    ?>
        <a class="card__bookmark" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
            <svg class="icon icon-bookmark-empty">
                <use xlink:href="#icon-bookmark-empty"></use>
            </svg>
            <span class="visually-hidden">
                <?php echo esc_html__('Add to bookmarks', 'gerendashaz'); ?>
            </span>
        </a>
    <?php
    else :
        $bookmark_ids  = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [];
        $is_bookmarked = in_array($post_id, $bookmark_ids, true);
        $bookmark_icon = $is_bookmarked ? 'bookmark' : 'bookmark-empty';
        $bookmark_text = $is_bookmarked
            ? __('Remove from bookmarks', 'gerendashaz')
            : __('Add to bookmarks', 'gerendashaz');
    ?>
        <a class="card__bookmark" href="#" data-post-id="<?php echo esc_attr($post_id); ?>" data-bookmarked="<?php echo esc_attr($is_bookmarked ? 'true' : 'false'); ?>">
            <svg class="icon icon-<?php echo esc_attr($bookmark_icon); ?>">
                <use xlink:href="#icon-<?php echo esc_attr($bookmark_icon); ?>"></use>
            </svg>
            <span class="visually-hidden"><?php echo esc_html($bookmark_text); ?></span>
        </a>
    <?php endif; ?>

<?php do_action('theme_card_close'); ?>

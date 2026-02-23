<?php
    $section_classes = build_section_classes($section, 'comments');

    $section_title      = $section['comments_section_title'] ?: __('You may also like…', 'gerendashaz');
    $section_hide_title = $section['comments_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['comments_section_lead'] ?? '';
?>


<?php if (comments_open() || get_comments_number()) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--comments' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>

            <?php do_action('theme_section_content_open'); ?>

                <?php comments_template(); // Load comments template ?>
            
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

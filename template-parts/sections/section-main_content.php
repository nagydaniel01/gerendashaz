<?php
    $post_title = get_the_title();
    $post_slug  = sanitize_title($post_title);

    $section_classes = build_section_classes($section, 'main_content');

    $section_title       = $section['main_content_section_title'] ?: $post_title;
    $section_hide_title  = $section['main_content_section_hide_title'] ?? false;
    $section_slug        = sanitize_title($section_title);
    $section_lead        = $section['main_content_section_lead'] ?? '';
?>

<?php do_action('theme_section_open', [
    'id'      => $section_slug,
    'classes' => 'section section--main_content section--single' . esc_attr($section_classes),
]); ?>

    <?php do_action('theme_section_container_open'); ?>

        <?php 
        // Section header via hook
        do_action('theme_section_header', [
            'title'            => $section_title,
            'hide_title'       => $section_hide_title,
            'lead'             => $section_lead,
            'show_breadcrumbs' => true,
            'show_image'       => true,
        ]); 
        ?>

        <?php if (get_the_content()) : ?>
            <?php do_action('theme_section_content_open'); ?>

                <?php echo apply_filters('the_content', get_the_content()); ?>
            
            <?php do_action('theme_section_content_close'); ?>
        <?php endif; ?>

    <?php do_action('theme_section_container_close'); ?>

<?php do_action('theme_section_close'); ?>

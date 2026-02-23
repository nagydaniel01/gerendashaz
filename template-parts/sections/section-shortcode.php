<?php
    $section_classes = build_section_classes($section, 'shortcode');

    $section_title      = $section['shortcode_section_title'] ?? '';
    $section_hide_title = $section['shortcode_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['shortcode_section_lead'] ?? '';
    $shortcode          = $section['shortcode'] ?? '';

    $shortcode_tag = '';
    if (preg_match('/\[([a-zA-Z0-9_-]+)/', $shortcode, $matches)) {
        $shortcode_tag = $matches[1];
    }
?>

<?php if (!empty($shortcode) && shortcode_exists($shortcode_tag)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--shortcode' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>
        
            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>
            
            <?php do_action('theme_section_content_open'); ?>

                <?php echo do_shortcode($shortcode); ?>
                
            <?php do_action('theme_section_content_close'); ?>
            
        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

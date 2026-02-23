<?php
    $section_classes = build_section_classes($section, 'cta');

    $section_title      = $section['cta_section_title'] ?? '';
    $section_hide_title = $section['cta_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['cta_section_lead'] ?? '';
    $cta_text_align     = $section['cta_text_align'] ?? 'left';
    $cta_link           = $section['cta_link'] ?? [];

    $url    = !empty($cta_link['url']) ? esc_url($cta_link['url']) : '';
    $title  = !empty($cta_link['title']) ? esc_html($cta_link['title']) : '';
    $target = !empty($cta_link['target']) ? esc_attr($cta_link['target']) : '_self';
    $rel    = ($target === '_blank') ? 'noopener noreferrer' : '';

    // Filter out empty link
    $cta_link_valid = array_filter([$cta_link], function ($item) {
        $url   = trim($item['url'] ?? '');
        $title = trim($item['title'] ?? '');
        return $url !== '' && $title !== '';
    });

    $extra_classes = '';
    if ($cta_text_align) {
        $extra_classes .= ' text-' . $cta_text_align;
    }
?>

<?php if (!empty($cta_link_valid)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--cta' . esc_attr($section_classes) . esc_attr($extra_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php 
            // Section header
            do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); 
            ?>
            
            <?php do_action('theme_section_content_open'); ?>
            
                <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" class="btn btn-lg btn-outline-primary" <?php if ($rel) echo 'rel="' . esc_attr($rel) . '"'; ?>>
                    <span><?php echo esc_html($title); ?></span>
                    <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                </a>
            
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

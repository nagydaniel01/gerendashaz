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
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--cta<?php echo esc_attr($section_classes); ?><?php echo esc_attr($extra_classes); ?>">
        <div class="container">
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="section__content">
                <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" class="btn btn-lg btn-outline-primary" <?php if ($rel) echo 'rel="' . esc_attr($rel) . '"'; ?>>
                    <span><?php echo esc_html($title); ?></span>
                    <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
    $section_classes = build_section_classes($section, 'flipbook');

    $section_title      = $section['flipbook_section_title'] ?? '';
    $section_hide_title = $section['flipbook_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['flipbook_section_lead'] ?? '';
    $flipbook           = $section['flipbook'] ?? [];
    $file_url           = $flipbook['url'] ?? '';
    $file_title         = $flipbook['title'] ?? '';
    
    $aria_label = sprintf(
        /* translators: %s is the file title */
        __('Download "%s" ebook', 'gerendashaz'),
        $file_title
    );
?>

<?php if (!empty($file_url) && shortcode_exists('real3dflipbook')) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--flipbook' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>

            <?php do_action('theme_section_content_open'); ?>

                <?php echo do_shortcode('[real3dflipbook id="1" pdf="' . esc_url($file_url) . '"]'); ?>

                <a href="<?php echo esc_url($file_url); ?>" target="_self" aria-label="<?php echo esc_attr($aria_label); ?>" download class="section__button btn btn-primary">
                    <span><?php echo esc_html__('Download PDF', 'gerendashaz'); ?></span>
                    <svg class="icon icon-download"><use xlink:href="#icon-download"></use></svg>
                </a>
                
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

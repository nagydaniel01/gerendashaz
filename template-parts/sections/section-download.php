<?php
    $section_classes = build_section_classes($section, 'download');

    $section_title      = $section['download_section_title'] ?? '';
    $section_hide_title = $section['download_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['download_section_lead'] ?? '';
    $download_items     = $section['download_items'] ?: [];

    // Filter out items without a file
    $download_items = array_filter($download_items, function ($item) {
        if (empty($item['download_file'])) {
            return false;
        }

        if (is_array($item['download_file'])) {
            return !empty($item['download_file']['id']);
        }

        return true;
    });
?>

<?php if (!empty($download_items)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--download' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>
            
            <?php do_action('theme_section_content_open'); ?>

                <?php foreach ($download_items as $key => $item) : 
                    $title       = $item['download_title'] ?? '';
                    $description = $item['download_description'] ?? '';
                    $file        = $item['download_file'] ?? '';
                    
                    $file_id          = isset($file['id']) ? $file['id'] : '';
                    $file_title       = !empty($title) ? $title : $file['title'];
                    $file_description = !empty($description) ? $description : $file['description'];
                    $file_url         = isset($file['url']) ? $file['url'] : '';
                    $file_type        = isset($file['subtype']) ? $file['subtype'] : '';
                    $file_size        = isset($file['filesize']) ? wp_format_file_size($file['filesize']) : '';

                    $aria_label = sprintf(
                        /* translators: %1$s is the file title */
                        __('Download "%1$s"', 'gerendashaz'),
                        $file_title
                    );
                ?>

                <div class="card card--download">
                    <div class="card__header">
                        <svg class="card__icon icon icon-download"><use xlink:href="#icon-download"></use></svg>
                    </div>
                    
                    <div class="card__content">
                        <?php if ($file_title) : ?>
                            <h4 class="card__title"><?php echo esc_html($file_title); ?></h4>
                        <?php endif; ?>

                        <?php if ($file_description) : ?>
                            <div class="card__lead"><?php echo wp_kses_post($file_description); ?></div>
                        <?php endif; ?>

                        <?php if ($file_type || $file_size) : ?>
                            <div class="card__meta">
                                <?php printf( '(%s, %s)', esc_html($file_type), esc_html($file_size) ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($file_url) : ?>
                            <a href="<?php echo esc_url($file_url); ?>" target="_self" aria-label="<?php echo esc_attr($aria_label); ?>" download class="card__button btn btn-secondary">
                                <span><?php echo esc_html__('Download', 'gerendashaz'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php endforeach; ?>
            
            <?php do_action('theme_section_content_close'); ?>
            
        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

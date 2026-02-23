<?php
    $section_classes = build_section_classes($section, 'media');

    $section_title      = $section['media_section_title'] ?? '';
    $section_hide_title = $section['media_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['media_section_lead'] ?? '';
    $media              = $section['media'] ?? '';
    $poster_url         = $section['media_poster']['url'] ?? '';

    // ACF media fields
    $url       = $media['url'] ?? '';
    $mime_type = $media['mime_type'] ?? '';
    $width     = $media['width'] ?? '';
    $height    = $media['height'] ?? '';
    $is_video  = strpos($mime_type, 'video/') === 0;
    $is_audio  = strpos($mime_type, 'audio/') === 0;
?>

<?php if (!empty($media)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--media' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>

            <?php do_action('theme_section_content_open'); ?>
            
                <?php if ($is_video) : ?>
                    <div class="section__video-wrapper ratio ratio-16x9">
                        <video width="<?php echo esc_attr($width); ?>" height="<?php echo esc_attr($height); ?>" controls <?php echo $poster_url ? 'poster="' . esc_url($poster_url) . '"' : ''; ?> class="section__video">
                            <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                            <?php echo esc_html__('Your browser does not support the video tag.', 'gerendashaz'); ?>
                        </video>
                    </div>
                <?php elseif ($is_audio) : ?>
                    <audio controls class="section__audio">
                        <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                        <?php echo esc_html__('Your browser does not support the audio tag.', 'gerendashaz'); ?>
                    </audio>
                <?php else : ?>
                    <?php echo wpautop( esc_html__('Unsupported media type.', 'gerendashaz') ); ?>
                <?php endif; ?>

            <?php do_action('theme_section_content_close'); ?>
        
        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

<?php
    $section_classes = build_section_classes($section, 'oembed');

    $section_title      = $section['oembed_section_title'] ?? '';
    $section_hide_title = $section['oembed_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['oembed_section_lead'] ?? '';
    $oembed             = $section['oembed'] ?? '';

    // Extract iframe src
    preg_match('/src="([^"]+)"/', $oembed, $matches);
    $src = $matches[1] ?? '';

    // Update iframe with parameters and attributes
    if (!empty($src)) {
        $params   = ['controls' => 0, 'hd' => 1, 'autohide' => 1];
        $new_src  = add_query_arg($params, $src);
        $oembed   = str_replace($src, $new_src, $oembed);

        $oembed   = str_replace(
            '></iframe>',
            ' frameborder="0"></iframe>',
            $oembed
        );
    }

    // Safe declare of helper function
    if (!function_exists('detect_oembed_type')) {
        function detect_oembed_type($url) {
            $host = parse_url($url, PHP_URL_HOST);

            $map = [
                'youtube'    => ['youtube.com', 'youtu.be'],
                'vimeo'      => ['vimeo.com'],
                'spotify'    => ['spotify.com'],
                'soundcloud' => ['soundcloud.com'],
                'tiktok'     => ['tiktok.com'],
            ];

            foreach ($map as $type => $domains) {
                foreach ($domains as $key => $domain) {
                    if (strpos($host, $domain) !== false) {
                        return $type;
                    }
                }
            }
            return 'unknown';
        }
    }

    $oembed_type = !empty($src) ? detect_oembed_type($src) : 'unknown';
?>

<?php if (!empty($oembed)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--oembed section--' . esc_attr($oembed_type) . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>

            <?php do_action('theme_section_content_open'); ?>

                <?php if ($oembed_type === 'youtube') : ?>
                    <?php $video_id = get_youtube_video_id($src) ?? ''; ?>
                    <div class="section__video-wrapper"> 
                        <div class="youtube-player" data-id="<?php echo esc_attr($video_id); ?>"></div>
                    </div>
                <?php else : ?>
                    <?php echo $oembed; ?>
                <?php endif; ?>
                
            <?php do_action('theme_section_content_close'); ?>
            
        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

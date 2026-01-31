<?php
    $site_name         = get_bloginfo('name') ?: get_field('site_name', 'option') ?: '';
    $custom_logo_id    = get_theme_mod('custom_logo') ?? null;
    $acf_logo          = get_field('site_logo', 'option') ?? null;
    $site_phone        = get_field('site_phone', 'option') ?? '';
    $site_email        = get_field('site_email', 'option') ?? '';
    $social            = get_field('social_items', 'option') ?: [];
    $spotify_playlist  = get_field('spotify_playlist', 'option') ?? '';
    $copyright         = get_field('copyright', 'option') ?? '';
    $site_payment_logo = get_field('site_payment_logo', 'option') ?: [];

    $site_logo = null;

    switch ( true ) {
        case ! empty( $acf_logo ):
            switch ( true ) {
                // ACF returns ID
                case is_numeric( $acf_logo ):
                    $image_data = wp_get_attachment_image_src( (int)$acf_logo, 'full' );
                    $site_logo = [
                        'ID'     => (int)$acf_logo,
                        'url'    => $image_data[0] ?? '',
                        'width'  => $image_data[1] ?? '',
                        'height' => $image_data[2] ?? '',
                        'alt'    => get_post_meta( $acf_logo, '_wp_attachment_image_alt', true ) ?: $site_name,
                    ];
                    break;

                // ACF returns array
                case is_array( $acf_logo ):
                    $site_logo = $acf_logo;
                    break;
            }
            break;

        case ! empty( $custom_logo_id ):
            $image_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            $site_logo = [
                'ID'     => $custom_logo_id,
                'url'    => $image_data[0] ?? '',
                'width'  => $image_data[1] ?? '',
                'height' => $image_data[2] ?? '',
                'alt'    => get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true ) ?: $site_name,
            ];
            break;

        default:
            $site_logo = null;
            break;
    }

    $locations = get_nav_menu_locations();

    // Phone link
    $phone_link = '';
    if (!empty($site_phone)) {
        $phone_link = preg_replace('/[^0-9\+]/', '', $site_phone);
    }

    // Email obfuscation
    $email = $email_obfuscated = '';
    if (!empty($site_email)) {
        $clean_email      = sanitize_email($site_email);
        $email            = antispambot($clean_email);
        $email_obfuscated = antispambot($clean_email, 1);
    }

    // Footer menus
    $footer_menus = ['footer_menu_1', 'footer_menu_2', 'footer_menu_3', 'footer_menu_4'];
    $active_menus = [];

    // Count active menus
    foreach ($footer_menus as $theme_location) {
        if ($locations && has_nav_menu($theme_location)) {
            $active_menus[] = $theme_location;
        }
    }
    
    // Spotify playlist
    $spotify_embed = '';

    if (!empty($spotify_playlist)) {

        // If editor pasted full iframe — sanitize & allow
        if (strpos($spotify_playlist, '<iframe') !== false) {
            $allowed_iframe = [
                'iframe' => [
                    'src'             => true,
                    'width'           => true,
                    'height'          => true,
                    'frameborder'     => true,
                    'allowfullscreen' => true,
                    'allow'           => true,
                    'loading'         => true,
                    'style'           => true,
                ],
            ];
            $spotify_embed = wp_kses($spotify_playlist, $allowed_iframe);
        }

        // If URL pasted
        elseif (filter_var($spotify_playlist, FILTER_VALIDATE_URL)) {

            $parsed = wp_parse_url($spotify_playlist);
            $host   = $parsed['host'] ?? '';

            // Only allow spotify domains
            if (strpos($host, 'spotify.com') !== false) {

                global $wp_embed;

                // Try WP oEmbed first
                $spotify_embed = $wp_embed->autoembed(esc_url($spotify_playlist));

                // Fallback → manual embed (Spotify sometimes fails oEmbed)
                if (empty($spotify_embed)) {

                    // Convert open.spotify.com → embed.spotify.com format
                    $embed_url = preg_replace(
                        '#https?://open\.spotify\.com/#',
                        'https://open.spotify.com/embed/',
                        esc_url_raw($spotify_playlist)
                    );

                    $spotify_embed = sprintf(
                        '<iframe src="%s" width="100%%" height="152" frameborder="0" allowfullscreen="" loading="lazy" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>',
                        esc_url($embed_url)
                    );
                }
            }
        }
    }

    // Include contact block as one column
    $has_spotify   = !empty($spotify_embed);
    $columns_count = count($active_menus) + 1 + ($has_spotify ? 1 : 0);

    // Determine Bootstrap column class dynamically
    switch ($columns_count) {
        case 1:
            $col_class = 'col-12';
            break;
        case 2:
            $col_class = 'col-md-6 col-xl-6';
            break;
        case 3:
            $col_class = 'col-md-6 col-xl-4';
            break;
        default:
            $col_class = 'col-md-6 col-xl';
            break;
    }
?>

<footer class="footer<?php echo class_exists('WooCommerce') && is_product() ? ' footer--single-product' : ''; ?>">
    <div class="footer__top">
        <div class="container">
            <div class="row">
                <!-- Contact Block -->
                <div class="<?php echo esc_attr($col_class); ?>">
                    <div class="footer__block">
                        <h3 class="footer__title visually-hidden"><?php echo esc_html__('Contact us', 'gerendashaz'); ?></h3>
                        
                        <?php if ($site_logo) : ?>
                            <a href="<?php echo esc_url(trailingslashit(home_url())); ?>" class="logo logo--footer">
                                <?php echo wp_get_attachment_image(
                                    $site_logo['ID'],
                                    [$site_logo['width'], $site_logo['height']],
                                    false,
                                    ['class' => 'logo__image imgtosvg', 'alt' => esc_attr($site_logo['alt'] ?: $site_name)]
                                ); ?>
                                <span class="visually-hidden"><?php echo esc_html($site_name); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($social) && is_array($social)) : ?>
                            <?php
                                $custom_names = [
                                    'linkedin' => 'LinkedIn',
                                    'youtube'  => 'YouTube',
                                    'tiktok'   => 'TikTok'
                                ];
                            ?>
                            <nav class="footer__nav nav nav--footer">
                                <ul class="nav__list">
                                    <?php foreach ($social as $row) :
                                        $social_image  = $row['social_image'] ?? '';
                                        $social_url    = $row['social_link']['url'] ?? '';
                                        $social_title  = $row['social_link']['title'] ?? '';
                                        $social_target = $row['social_link']['target'] ?? '_self';
                                        
                                        $host = parse_url($social_url, PHP_URL_HOST);
                                        $base = '';
                                        if ($host) {
                                            $parts = explode('.', $host);
                                            $base  = ($parts[0] === 'www') ? ($parts[1] ?? '') : $parts[0];
                                        }
                                        $base = $base ?: 'link';
                                        $social_name = $social_title ?: ($custom_names[$base] ?? ucfirst($base));
                                    ?>
                                        <?php if ($social_url) : ?>
                                            <li class="nav__item">
                                                <a href="<?php echo esc_url($social_url); ?>" target="<?php echo esc_attr($social_target); ?>" class="nav__link">
                                                    <?php if (!empty($social_image)) : ?>
                                                        <?php echo wp_get_attachment_image(
                                                            $social_image['ID'], [24, 24], false, ['class' => 'icon', 'alt' => esc_attr($social_name)]
                                                        ); ?>
                                                    <?php else : ?>
                                                        <svg class="icon icon-<?php echo esc_attr($base); ?>">
                                                            <use xlink:href="#icon-<?php echo esc_attr($base); ?>"></use>
                                                        </svg>
                                                    <?php endif; ?>
                                                    <?php echo esc_html($social_name); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                        <?php if (!empty($site_phone) || !empty($site_email)) : ?>
                            <div class="footer__nav nav nav--footer">
                                <ul class="nav__list">

                                    <?php if ($site_phone) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo esc_attr('tel:' . $phone_link); ?>" class="nav__link">
                                                <svg class="icon icon-circle-phone"><use xlink:href="#icon-circle-phone"></use></svg>
                                                <?php echo esc_html($site_phone); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php if ($site_email) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo esc_url('mailto:' . $email_obfuscated); ?>" class="nav__link">
                                                <svg class="icon icon-circle-envelope"><use xlink:href="#icon-circle-envelope"></use></svg>
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php if (shortcode_exists('site_address')) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo get_location_link(do_shortcode('[site_address]'), 'route', false); ?>" target="_blank" class="nav__link">
                                                <svg class="icon icon-circle-location-arrow"><use xlink:href="#icon-circle-location-arrow"></use></svg>
                                                <span>
                                                    <?php echo do_shortcode('[site_address]'); ?><br>
                                                    <small><?php echo esc_html__('Go to location', 'gerendashaz'); ?></small>
                                                </span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer Menus -->
                <?php foreach ($active_menus as $theme_location) : ?>
                    <div class="<?php echo esc_attr($col_class); ?>">
                        <div class="footer__block footer__block--nav">
                            <?php 
                            $menu_id = $locations[$theme_location];
                            $menu = wp_get_nav_menu_object($menu_id);
                            if (is_object($menu) && isset($menu->name)) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                $walker = class_exists( 'Custom_Bootstrap_Nav_Walker' ) ? new Custom_Bootstrap_Nav_Walker() : false;

                                if ( $walker ) {
                                    wp_nav_menu([
                                        'theme_location' => $theme_location,
                                        'container'      => false,
                                        'menu_class'     => 'nav__list level0',
                                        'walker'         => new Custom_Bootstrap_Nav_Walker()
                                    ]);
                                } else {
                                    echo '<p class="no-menu-assigned">' . esc_html__( 'Please assign a menu in Appearance → Menus.', 'gerendashaz' ) . '</p>';
                                }
                                ?>
                            </nav>

                            <?php if ($theme_location === 'footer_menu_3' && $site_payment_logo) : ?>
                                <?php echo wp_get_attachment_image( $site_payment_logo['ID'], [$site_payment_logo['width'], $site_payment_logo['height']], false, ['class' => 'footer__image', 'alt' => esc_attr($site_payment_logo['alt'] ?? '')] ); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Spotify playlist -->
                <?php if (!empty($spotify_embed)) : ?>
                    <div class="<?php echo esc_attr($col_class); ?>">
                        <div class="footer__block footer__block--spotify">
                            <h3 class="footer__title"><?php esc_html_e('Playlist', 'gerendashaz'); ?></h3>
                            <div class="footer__spotify">
                                <?php echo $spotify_embed; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="copyright">
        <?php echo wpautop(esc_html($copyright)); ?>
    </div>
</footer>

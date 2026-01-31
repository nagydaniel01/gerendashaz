<?php
    $section_classes = build_section_classes($section, 'google_maps');

    $section_title         = $section['google_maps_section_title'] ?? '';
    $section_hide_title    = $section['google_maps_section_hide_title'] ?? false;
    $section_slug          = sanitize_title($section_title);
    $section_lead          = $section['google_maps_section_lead'] ?? '';
    $google_maps_items     = $section['google_maps_items'] ?: [];

    // Filter out empty items (WYSIWYG empty)
    $google_maps_items = array_filter($google_maps_items, function ($item) {
        $map = trim($item['google_maps_address'] ?? '');
        return $map !== '';
    });
?>

<?php if (!empty($google_maps_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--google-maps<?php echo esc_attr($section_classes); ?>">
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
                <div id="map" class="mb-4" style="height: 500px; width: 100%; border-radius: 0.625rem"></div>
                <div id="map-list" class="row gy-4">
                    <?php foreach ($google_maps_items as $index => $item) : ?>
                        <?php
                            $title        = $item['google_maps_title'] ?? '';
                            $description  = $item['google_maps_description'] ?? '';
                            $link         = $item['google_maps_link'] ?? [];
                            $address_text = $item['google_maps_address'] ?? '';
                            $location     = $item['google_maps_location'] ?? [];
                            $image        = $item['google_maps_image'] ?? null;
                            
                            $lat        = $location['lat'] ?? '';
                            $address    = $location['address'] ?? '';
                            $lng        = $location['lng'] ?? '';
                            $image_url  = is_array($image) ? ($image['url'] ?? '') : '';
                            $url        = !empty($link['url']) ? $link['url'] : '';
                            $link_title = !empty($link['title']) ? $link['title'] : $title;

                            $template_args = [
                                'index'        => $index,
                                'title'        => $title,
                                'description'  => $description,
                                'address_text' => $address_text,
                                'lat'          => $lat,
                                'lng'          => $lng,
                                'address'      => $address,
                                'image_url'    => $image_url,
                                'url'          => $url,
                                'link_title'   => $link_title,
                            ];

                            get_template_part('template-parts/cards/card', 'map', $template_args);
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

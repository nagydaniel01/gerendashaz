<?php
    $section_classes = build_section_classes($section, 'card');

    $section_title      = $section['card_section_title'] ?? '';
    $section_hide_title = $section['card_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['card_section_lead'] ?? '';

    $slider             = $section['card_slider'] ?? '';

    $card_items         = $section['card_items'] ?: [];
    $card_style         = $section['card_style'] ?? 'unordered';

    // Columns (ACF number field: 1–6)
    $columns = (int) ($section['card_columns'] ?: 3);
    $columns = max(1, min(6, $columns));

    // Calculate Bootstrap column size
    $col_size = 12 / $columns;

    // Valid Bootstrap column sizes
    $valid_cols = [1, 2, 3, 4, 6, 12];
    if (!in_array($col_size, $valid_cols, true)) {
        $col_size = 4; // fallback (3 columns)
    }

    // Responsive column classes
    $col_class = 'col-12 col-md-6 col-lg-' . $col_size;

    // Filter out items without images
    $card_items = array_filter($card_items, fn($item) => !empty($item['card_image']));

    // Detect if all items are image-only (no title & no description)
    $is_image_slider = !empty($card_items) && !array_filter($card_items, function ($item) {
        $title       = trim($item['card_title'] ?? '');
        $description = trim($item['card_description'] ?? '');
        return $title !== '' || $description !== '';
    });

    $template = locate_template("template-parts/cards/card.php");
?>

<?php if (!empty($card_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--card<?php echo esc_attr($section_classes); ?><?php echo ($slider != false) ? ' section--slider' : ''; ?>">
        <div class="container">
            <?php if (!$is_image_slider && (($section_title && $section_hide_title !== true) || $section_lead)) : ?>
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
                <?php if ($slider != false) : ?>
                    <div class="slider<?php echo !$is_image_slider ? ' slider--card' : ' slider--image'; ?>">
                        <div class="slider__list">
                            <?php foreach ($card_items as $key => $item) : ?>
                                <div class="slider__item">
                                    <?php
                                    if ($template) {
                                        $template_args = [
                                            'card_image'       => $item['card_image'],
                                            'card_title'       => !$is_image_slider ? $item['card_title'] : '' ,
                                            'card_description' => !$is_image_slider ? $item['card_description'] : '',
                                            'card_button'      => $item['card_button'] ?? '',
                                        ];
                                        get_template_part('template-parts/cards/card', '', $template_args);
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row">
                        <?php foreach ($card_items as $key => $item) : ?>
                            <div class="<?php echo esc_attr($col_class); ?> mb-3">
                                <?php
                                if ($template) {
                                    $template_args = [
                                        'card_image'       => $item['card_image'],
                                        'card_title'       => $item['card_title'],
                                        'card_description' => $item['card_description'],
                                        'card_button'      => $item['card_button'] ?? '',
                                    ];
                                    get_template_part('template-parts/cards/card', '', $template_args);
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
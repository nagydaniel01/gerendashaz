<?php
    $section_classes = build_section_classes($section, 'card');

    $section_title      = $section['card_section_title'] ?? '';
    $section_hide_title = $section['card_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['card_section_lead'] ?? '';

    $slider             = $section['card_slider'] ?? '';
    $card_items         = $section['card_items'] ?: [];

    // Columns (ACF number field: 1–6)
    $acf_columns = $section['card_columns_columns'] ?? [];
    $acf_columns = is_array($acf_columns) ? $acf_columns : [];

    $default_columns = [
        'xs'  => 1,
        'sm'  => 1,
        'md'  => 2,
        'lg'  => 2,
        'xl'  => 3,
        'xxl' => 3,
    ];

    // Merge user-defined columns with defaults, only if not empty
    foreach ($default_columns as $bp => $default) {
        if (isset($acf_columns[$bp]) && trim($acf_columns[$bp]) !== '') {
            $default_columns[$bp] = (int) $acf_columns[$bp];
        }
    }

    // Now $columns contains the final values
    $columns = $default_columns;

    // Map breakpoints to Bootstrap prefixes
    $breakpoints = [
        'xs'  => '',      
        'sm'  => 'sm',    
        'md'  => 'md',    
        'lg'  => 'lg',    
        'xl'  => 'xl',    
        'xxl' => 'xxl',   
    ];

    $col_classes = [];
    foreach ($breakpoints as $bp => $prefix) {
        $num_columns = $columns[$bp]; // already int and valid
        $num_columns = max(1, min(6, $num_columns)); // clamp to 1-6
        $col_size = (int) round(12 / $num_columns);
        $col_classes[] = 'col' . ($prefix ? '-' . $prefix : '') . '-' . $col_size;
    }

    $col_class = implode(' ', $col_classes);

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
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--card' . esc_attr($section_classes) . ($slider != false ? ' section--slider' : ''),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php 
            if (!$is_image_slider) {
                do_action('theme_section_header', [
                    'title'      => $section_title,
                    'hide_title' => $section_hide_title,
                    'lead'       => $section_lead,
                ]); 
            }
            ?>

            <?php do_action('theme_section_content_open'); ?>
            
                <?php if ($slider != false) : ?>
                    <div class="slider<?php echo !$is_image_slider ? ' slider--card' : ' slider--logo'; ?>">
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
                    <div class="row gy-4">
                        <?php foreach ($card_items as $key => $item) : ?>
                            <div class="<?php echo esc_attr($col_class); ?>">
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

            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

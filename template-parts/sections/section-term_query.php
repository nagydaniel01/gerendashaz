<?php
$section_classes = build_section_classes($section, 'term_query');

$section_title      = $section['term_query_section_title'] ?? '';
$section_hide_title = $section['term_query_section_hide_title'] ?? false;
$section_slug       = sanitize_title($section_title);
$section_lead       = $section['term_query_section_lead'] ?? '';

$link               = $section['term_query_link'] ?? '';
$slider             = $section['term_query_slider'] ?? '';

$url         = $link['url'] ?? '';
$title       = $link['title'] ?? esc_url($url);
$target      = isset($link['target']) && $link['target'] !== '' ? $link['target'] : '_self';
$is_external = is_external_url($url, get_home_url());

// Columns (ACF number field: 1–6)
$acf_columns = $section['post_query_columns_columns'] ?? [];
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

$query_args = [
    'taxonomy'   => $section['taxonomy_type'] ?? 'category',
    'orderby'    => $section['orderby'] ?? 'name',
    'order'      => strtoupper($section['order'] ?? 'ASC'),
    'hide_empty' => isset($section['hide_empty']) ? (bool) $section['hide_empty'] : true,
    'number'     => (int) ($section['terms_per_page'] ?? get_option('posts_per_page')),
];

// Manual selection
if (!empty($section['selection_type']) && $section['selection_type'] === 'manual') {
    if (!empty($section['category']) && is_array($section['category'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['category']);
    }

    if (!empty($section['product_cat']) && is_array($section['product_cat'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['product_cat']);
    }

    if (!empty($section['pa_boraszat']) && is_array($section['pa_boraszat'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['pa_boraszat']);
    }

    if (!empty($section['pa_orszag']) && is_array($section['pa_orszag'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['pa_orszag']);
    }

    if (!empty($section['pa_borvidek']) && is_array($section['pa_borvidek'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['pa_borvidek']);
    }

    /*
    if (!empty($section['event_cat']) && is_array($section['event_cat'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['event_cat']);
    }
    */
}

// Auto selection – parent/child terms
if (!empty($section['selection_type']) && $section['selection_type'] === 'auto') {
    if ($query_args['taxonomy'] === 'category' && !empty($section['parent_category']) && is_object($section['parent_category'])) {
        $query_args['parent'] = (int) $section['parent_category']->term_id;
    }

    if ($query_args['taxonomy'] === 'product_cat' && !empty($section['parent_product_cat']) && is_object($section['parent_product_cat'])) {
        $query_args['parent'] = (int) $section['parent_product_cat']->term_id;
    }

    /*
    if ($query_args['taxonomy'] === 'event_cat' && !empty($section['parent_event_cat']) && is_object($section['parent_event_cat'])) {
        $query_args['parent'] = (int) $section['parent_event_cat']->term_id;
    }
    */
}

/*
echo '<pre>';
var_dump($query_args);
echo '</pre>';
*/

$term_query = new WP_Term_Query($query_args);
?>

<?php if (!empty($term_query->terms)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--term_query' . esc_attr($section_classes) . ($slider != false ? ' section--slider' : ''),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php 
            do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); 
            ?>

            <?php do_action('theme_section_content_open'); ?>

                <?php if ($slider != false) : ?>
                    <div class="slider slider--term-query">
                        <div class="slider__list">
                            <?php foreach ( $term_query->terms as $key => $term ) : ?>
                                <div class="slider__item">
                                    <?php 
                                        $template_args = [
                                            'taxonomy' => esc_attr($term->taxonomy),
                                            'term'     => $term
                                        ];

                                        $template_slug = 'template-parts/cards/card-term-' . $template_args['taxonomy'] . '.php';

                                        if ( locate_template( $template_slug ) ) {
                                            // File exists, include it
                                            get_template_part( 'template-parts/cards/card-term', $template_args['taxonomy'], $template_args );
                                        } else {
                                            // File does not exist, handle accordingly
                                            get_template_part( 'template-parts/cards/card-term', 'default', $template_args );
                                        }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row gy-4">
                        <?php foreach ( $term_query->terms as $key => $term ) : ?>
                            <div class="<?php echo esc_attr($col_class); ?>">
                                <?php
                                    $template_args = [
                                        'taxonomy' => esc_attr($term->taxonomy),
                                        'term'     => $term
                                    ];

                                    $template_slug = 'template-parts/cards/card-term-' . $template_args['taxonomy'] . '.php';

                                    if ( locate_template( $template_slug ) ) {
                                        // File exists, include it
                                        get_template_part( 'template-parts/cards/card-term', $template_args['taxonomy'], $template_args );
                                    } else {
                                        // File does not exist, handle accordingly
                                        get_template_part( 'template-parts/cards/card-term', 'default', $template_args );
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

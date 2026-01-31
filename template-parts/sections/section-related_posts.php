<?php
    $section_classes = build_section_classes($section, 'related_posts');

    $section_title      = $section['related_posts_section_title'] ?: __('You may also like…', 'gerendashaz');
    $section_hide_title = $section['related_posts_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['related_posts_section_lead'] ?? '';

    $posts_per_page = get_option('posts_per_page');
    $post_id        = get_the_ID();
    $post_type      = get_post_type($post_id);
    $taxonomies     = get_object_taxonomies($post_type, 'names');

    $term_ids = [];

    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'ids']);
        if (!is_wp_error($terms) && !empty($terms)) {
            $term_ids[$taxonomy] = $terms;
        }
    }

    $related_args = [
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'post__not_in'   => [$post_id],
    ];

    if (!empty($term_ids)) {
        $tax_query = ['relation' => 'OR'];

        foreach ($term_ids as $taxonomy => $ids) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $ids,
            ];
        }

        $related_args['tax_query'] = $tax_query;
    }

    $related_posts = new WP_Query($related_args);
?>

<?php if ($related_posts->have_posts()) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--related_posts<?php echo esc_attr($section_classes); ?>">
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
                <div class="slider slider--related" id="related-posts-slider">
                    <div class="slider__list">
                        <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                            <div class="slider__item">
                                <?php
                                    $template_args = [
                                        'post_type' => esc_attr($post_type)
                                    ];
                                    
                                    $template_slug = 'template-parts/cards/card-related.php';

                                    if ( locate_template( $template_slug ) ) {
                                        // File exists, include it
                                        get_template_part( 'template-parts/cards/card', 'related', $template_args );
                                    } else {
                                        // File does not exist, handle accordingly
                                        get_template_part( 'template-parts/cards/card', 'default', $template_args );
                                    }
                                ?>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    <div class="slider__controls"></div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

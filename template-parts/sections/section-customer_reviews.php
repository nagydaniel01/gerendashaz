<?php
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $section_classes = build_section_classes($section, 'customer_reviews');

    $section_title      = $section['customer_reviews_section_title'] ?? '';
    $section_hide_title = $section['customer_reviews_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['customer_reviews_section_lead'] ?? '';

    $slider             = $section['customer_reviews_slider'] ?? '';

    // Query all orders that have the _poll_feedback meta key
    $orders = wc_get_orders( [
        'limit'         => -1,
        'meta_key'      => '_poll_feedback',
        'meta_compare'  => 'EXISTS',
        'return'        => 'objects',
        'status'        => ['wc-completed'],
    ] );
?>

<?php if (!empty($orders)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--customer_reviews' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>

            <?php do_action('theme_section_content_open'); ?>

                <?php if ( $slider != false ) : ?>
                    <div class="slider slider--post-query">
                        <div class="slider__list">
                            <?php foreach ($orders as $order) : ?>
                                <div class="slider__item">
                                    <?php
                                        $feedback_data = $order->get_meta('_poll_feedback');

                                        // Skip if feedback is missing or malformed
                                        if (empty($feedback_data) || !is_array($feedback_data)) {
                                            continue;
                                        }

                                        $template_args = ['order' => $order, 'feedback_data' => $feedback_data];
                                        get_template_part( 'template-parts/cards/card', 'review', $template_args );
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row gy-4">
                        <?php foreach ($orders as $order) : ?>
                            <div class="col-lg-6 col-xl-4">
                                <?php
                                    $feedback_data = $order->get_meta('_poll_feedback');

                                    // Skip if feedback is missing or malformed
                                    if (empty($feedback_data) || !is_array($feedback_data)) {
                                        continue;
                                    }

                                    $template_args = ['order' => $order, 'feedback_data' => $feedback_data];
                                    get_template_part( 'template-parts/cards/card', 'review', $template_args );
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

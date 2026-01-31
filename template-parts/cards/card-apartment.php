<?php
    $post_type    = $args['post_type'] ?? '';

    if (empty($post_type)) {
        return;
    }

    $current_user_id = get_current_user_id();

    $post_id      = get_the_ID();
    $title        = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $image_id     = $thumbnail_id ?? null;
    $categories   = get_the_terms($post_id, 'category');
    $alt_text     = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;

    if (is_wp_error($categories)) {
        $categories = [];
    }

    $extra_classes = '';
    if ($post_type) {
        $extra_classes = ' card--'.$post_type;
    }
?>

<article class="card<?php echo esc_attr($extra_classes); ?>" data-aos="fade-up">
    <a href="<?php the_permalink(); ?>" class="card__link">
        <?php if ($image_id) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <?php echo wp_get_attachment_image($image_id, 'medium_large', false, ['class' => 'card__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy']); ?>
                </div>
            </div>
        <?php elseif ( defined( 'PLACEHOLDER_IMG_SRC' ) && PLACEHOLDER_IMG_SRC ) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <img width="150" height="150" src="<?php echo esc_url( PLACEHOLDER_IMG_SRC ); ?>" alt="" class="card__image card__image--placeholder" loading="lazy">
                </div>
            </div>
        <?php endif; ?>

        <div class="card__content">
            <h3 class="card__title"><?php the_title(); ?></h3>
            
            <div class="card__lead"><?php the_excerpt(); ?></div>

            <?php if (!empty($categories) && is_array($categories)) : ?>
                <div class="card__meta">
                    <span class="card__categories">
                        <?php
                            $primary_category = '';

                            if (function_exists('get_rank_math_primary_term_name')) {
                                $primary_category = get_rank_math_primary_term_name(null, 'category');
                            }

                            if (empty($primary_category) && !empty($categories[0]) && isset($categories[0]->name)) {
                                $primary_category = $categories[0]->name;
                            }
                        ?>

                        <?php if (!empty($primary_category)) : ?>
                            <span class="card__category"><?php echo esc_html($primary_category); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            <button type="button" class="btn btn-primary card__button"><?php echo esc_html__('Book now', 'gerendashaz'); ?></button>
        </div>
    </a>
</article>

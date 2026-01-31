<?php
    $card_image       = $args['card_image'] ?? [];
    $card_title       = $args['card_title'] ?? '';
    $card_description = $args['card_description'] ?? '';
    $card_button      = $args['card_button'] ?? [];

    $image_id        = $card_image['ID'] ?? '';
    $image_mime_type = $card_image['mime_type'] ?? '';
    $button_url      = $card_button['url'] ?? '';
    $button_title    = $card_button['title'] ?? esc_url($button_url);
    $button_target   = isset($card_button['target']) && $card_button['target'] !== '' ? $card_button['target'] : '_self';

    // Detect image-only card (empty title & description)
    $is_image_card = empty($card_title) && empty($card_description);

    // Add special class if image is an SVG
    $image_class = 'card__image';
    $wrapper_class = 'card__image-wrapper'; // Default wrapper

    if ($image_mime_type === 'image/svg+xml') {
        $image_class = 'card__icon imgtosvg';
        $wrapper_class = 'card__icon-wrapper';
    }
?>

<article class="card<?php echo $is_image_card ? ' card--image' : ''; ?>" data-aos="fade-up">
    <?php if ($image_id) : ?>
        <div class="card__header">
            <div class="<?php echo esc_attr($wrapper_class); ?>">
                <?php if ($is_image_card && $button_url) : ?>
                    <a href="<?php echo esc_url($button_url); ?>" target="<?php echo esc_attr($button_target); ?>">
                        <?php echo wp_get_attachment_image($image_id, 'medium_large', false, [
                            'class' => $image_class,
                            'alt'   => esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
                            'title' => esc_html($card_button['title'] ?? ''),
                            'loading' => 'lazy'
                        ]); ?>
                        <span class="visually-hidden"><?php echo esc_html($card_button['title'] ?? ''); ?></span>
                    </a>
                <?php else : ?>
                    <?php echo wp_get_attachment_image($image_id, 'medium_large', false, [
                        'class' => $image_class,
                        'alt'   => esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
                        'loading' => 'lazy'
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$is_image_card) : ?>
        <div class="card__content">
            <?php if (!empty($card_title)) : ?>
                <h3 class="card__title"><?php echo esc_html($card_title); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($card_description)) : ?>
                <div class="card__lead"><?php echo wp_kses_post($card_description); ?></div>
            <?php endif; ?>

            <?php if ($button_url) : ?>
                <a href="<?php echo esc_attr($button_url); ?>" target="<?php echo esc_attr($button_target); ?>" class="card__link btn btn-primary"><?php echo esc_html($button_title); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
<?php
    $title        = $args['title'] ?? '';
    $description  = $args['description'] ?? '';
    $lat          = $args['lat'] ?? '';
    $lng          = $args['lng'] ?? '';
    $address      = $args['address'] ?? '';
    $address_text = $args['address_text'] ?? '';
    $image_url    = $args['image_url'] ?? '';
    $url          = $args['url'] ?? '';
    $link_title   = $args['link_title'] ?? '';
    $index        = $args['index'] ?? 0;
?>

<div class="card__wrapper col-12 col-md-6 col-lg-4">
    <div id="map-card-<?php echo esc_attr($index); ?>" class="card card--location" 
        data-lat="<?php echo esc_attr($lat); ?>" 
        data-lng="<?php echo esc_attr($lng); ?>" 
        data-address="<?php echo esc_attr($address); ?>" 
        data-title="<?php echo esc_attr($title); ?>" 
        data-content="<?php echo esc_attr($description); ?>"
        data-link="<?php echo esc_url($url); ?>"
        data-image="<?php echo esc_attr($image_url); ?>"
        data-aos="fade-up">

        <?php if (!empty($url)) : ?><a href="<?php echo esc_url($url); ?>" class="card__link"><?php endif; ?>
            
            <?php if ($image_url) : ?>
                <div class="card__header">
                    <div class="card__image-wrapper">
                        <img src="<?php echo esc_url($image_url); ?>" class="card__image" alt="" loading="lazy">
                    </div>
                </div>
            <?php endif; ?>

            <div class="card__content">
                <h2 class="card__title"><?php echo esc_html($title); ?></h2>

                <?php if ($address_text) : ?>
                    <div class="card__address"><?php echo esc_html($address_text); ?></div>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <div class="card__lead"><?php echo wp_kses_post($description); ?></div>
                <?php endif; ?>

                <?php if (!empty($url)) : ?>
                    <button type="button" class="btn btn-primary card__button"><?php echo esc_html($link_title); ?></button>
                <?php endif; ?>
            </div>
            
        <?php if (!empty($url)) : ?></a><?php endif; ?>

    </div>
</div>

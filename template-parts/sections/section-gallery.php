<?php
    $section_classes = build_section_classes($section, 'gallery');

    $section_title      = $section['gallery_section_title'] ?? '';
    $section_hide_title = $section['gallery_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['gallery_section_lead'] ?? '';
    $gallery            = $section['gallery'] ?? [];
    $gallery_type       = $section['gallery_type'] ?? 'masonry';
    $use_fancybox       = $section['gallery_use_fancybox'] ?? false;

    // Generate unique IDs
    $section_id  = !empty($section_slug) ? $section_slug : wp_rand();
    $slider_id   = 'slider-' . $section_id;
    $grid_id     = 'grid-' . $section_id;
    $fancybox_id = 'gallery-' . $section_id;
?>

<?php if (!empty($gallery)) : ?>
    <section id="<?php echo esc_attr($section_id); ?>" class="section section--gallery<?php echo esc_attr($section_classes); ?>">
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
                <?php if ($gallery_type === 'slider') : ?>
                    <div class="slider slider--gallery" id="<?php echo esc_attr($slider_id); ?>">
                        <div class="slider__list">
                            <?php foreach($gallery as $image) : ?>
                                <figure class="slider__item">
                                    <?php if($use_fancybox): ?>
                                        <a href="<?php echo esc_url($image['url']); ?>" class="slider__link" data-fancybox="<?php echo esc_attr($fancybox_id); ?>" <?php if(!empty($image['caption'])): ?>data-caption="<?php echo esc_attr($image['caption']); ?>"<?php endif; ?>>
                                    <?php endif; ?>
                                    
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="slider__image">

                                    <?php if($use_fancybox): ?>
                                        </a>
                                    <?php endif; ?>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : // masonry/grid ?>
                    <div class="row gy-4 grid" id="<?php echo esc_attr($grid_id); ?>" style="position: relative;">
                        <div class="grid-sizer col-12 col-md-6 col-xl-3"></div>
                        <?php foreach($gallery as $image) : ?>
                            <div class="grid-item col-12 col-md-6 col-xl-3">
                                <?php if($use_fancybox): ?>
                                    <a href="<?php echo esc_url($image['url']); ?>" class="slider__link" data-fancybox="<?php echo esc_attr($fancybox_id); ?>" <?php if(!empty($image['caption'])): ?>data-caption="<?php echo esc_attr($image['caption']); ?>"<?php endif; ?>>
                                <?php endif; ?>
                                
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="gallery-image">
                                
                                <?php if($use_fancybox): ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
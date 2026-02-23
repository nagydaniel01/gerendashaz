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
    <?php do_action('theme_section_open', [
        'id'      => $section_id,
        'classes' => 'section section--gallery' . esc_attr($section_classes),
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

                <?php if ($gallery_type === 'slider') : ?>
                    <div class="slider slider--gallery" id="<?php echo esc_attr($slider_id); ?>">
                        <div class="slider__list">
                            <?php foreach($gallery as $index => $image) : ?>
                                <figure class="slider__item">
                                    <?php if($use_fancybox): ?>
                                        <a href="<?php echo esc_url($image['url']); ?>" class="slider__link" data-fancybox="<?php echo esc_attr($fancybox_id); ?>" <?php if(!empty($image['caption'])): ?>data-caption="<?php echo esc_attr($image['caption']); ?>"<?php endif; ?>>
                                    <?php endif; ?>
                                    
                                    <img src="<?php echo esc_url($image['sizes']['medium']); ?>" alt="<?php echo esc_attr($image['alt'] ?: ($image['caption'] ?: (sprintf( __('Image of %s %d', 'gerendashaz'), $section_title ?: __('Gallery image', 'gerendashaz'), $index + 1)))); ?>" class="slider__image">

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
                        <?php foreach($gallery as $index => $image) : ?>
                            <div class="grid-item col-12 col-md-6 col-xl-3">
                                <?php if($use_fancybox): ?>
                                    <a href="<?php echo esc_url($image['url']); ?>" class="slider__link" data-fancybox="<?php echo esc_attr($fancybox_id); ?>" <?php if(!empty($image['caption'])): ?>data-caption="<?php echo esc_attr($image['caption']); ?>"<?php endif; ?>>
                                <?php endif; ?>
                                
                                <img src="<?php echo esc_url($image['sizes']['medium']); ?>" alt="<?php echo esc_attr($image['alt'] ?: ($image['caption'] ?: (sprintf( __('Image of %s %d', 'gerendashaz'), $section_title ?: __('Gallery image', 'gerendashaz'), $index + 1)))); ?>" class="gallery-image">
                                
                                <?php if($use_fancybox): ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
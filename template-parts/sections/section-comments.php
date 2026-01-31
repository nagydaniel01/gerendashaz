<?php
    $section_classes = build_section_classes($section, 'comments');

    $section_title      = $section['comments_section_title'] ?: __('You may also like…', 'gerendashaz');
    $section_hide_title = $section['comments_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['comments_section_lead'] ?? '';
?>

<?php if ( comments_open() || get_comments_number() ) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--comments<?php echo esc_attr($section_classes); ?>">
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
                <?php comments_template(); // Load comments template ?>
            </div>
        </div>
    </section>
<?php endif; ?>

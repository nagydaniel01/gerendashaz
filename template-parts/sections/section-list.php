<?php
    $section_classes = build_section_classes($section, 'list');

    $section_title      = $section['list_section_title'] ?? '';
    $section_hide_title = $section['list_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['list_section_lead'] ?? '';
    $list_items         = $section['list_items'] ?: [];
    $list_style         = $section['list_style'] ?? 'unordered';

    // Filter out empty items (description empty)
    $list_items = array_filter($list_items, function ($item) {
        $description = trim($item['list_description'] ?? '');
        return $description !== '';
    });

    // Determine list tag
    $list_tag = $list_style === 'ordered' ? 'ol' : 'ul';
?>

<?php if (!empty($list_items)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--list' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>
            
            <?php do_action('theme_section_content_open'); ?>
            
                <?php if ($list_tag === 'ol') : ?><ol class="section__list section__list--ordered list-unstyled"><?php else : ?><ul class="section__list section__list--unordered list-unstyled"><?php endif; ?>

                <?php foreach ($list_items as $key => $item) : ?>
                    <?php $description = trim($item['list_description'] ?? ''); ?>
                    <?php if ($description) : ?>
                        <li class="section__listitem">
                            <div class="section__listitem-description"><?php echo wp_kses_post($description); ?></div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if ($list_tag === 'ol') : ?></ol><?php else : ?></ul><?php endif; ?>

            <?php do_action('theme_section_content_close'); ?>
            
        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

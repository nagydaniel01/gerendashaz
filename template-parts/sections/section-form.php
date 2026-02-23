<?php
    $section_classes = build_section_classes($section, 'form');

    $section_title      = $section['form_section_title'] ?? '';
    $section_hide_title = $section['form_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['form_section_lead'] ?? '';
    $form_id            = $section['form'] ?? '';
?>

<?php if (!empty($form_id)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--form' . esc_attr($section_classes),
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

                <?php
                    $template_args = [];
                    get_template_part('template-parts/forms/form', $form_id, $template_args);
                ?>
            
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
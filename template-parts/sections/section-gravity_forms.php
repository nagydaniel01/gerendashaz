<?php
    $section_classes = build_section_classes($section, 'gravity_forms');

    $section_title      = $section['gravity_forms_section_title'] ?? '';
    $section_hide_title = $section['gravity_forms_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['gravity_forms_section_lead'] ?? '';
    $form_id            = $section['gform'] ?? '';
?>

<?php if (!empty($form_id)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--gravity_forms' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>
            
            <?php do_action('theme_section_content_open'); ?>

                <?php
                // Ensure Gravity Forms is loaded
                if (!class_exists('GFAPI')) {
                    echo esc_html__('Gravity Forms is not installed or activated', 'gerendashaz');
                    return;
                }

                $form = GFAPI::get_form((int) $form_id);

                if (!$form) {
                    echo esc_html__('Form not found', 'gerendashaz');
                    return;
                }

                // Retrieve form display settings
                $title_enabled       = 'false';
                $description_enabled = !empty($form['description']) ? 'true' : 'false';
                $is_ajax             = 'true';
                $tabindex            = '4';

                // Render the form
                echo do_shortcode("[gravityform id=\"$form_id\" title=\"$title_enabled\" description=\"$description_enabled\" ajax=\"$is_ajax\" tabindex=\"$tabindex\" theme=\"gravity\"]");
                ?>
                
            <?php do_action('theme_section_content_close'); ?>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

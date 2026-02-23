<?php
    $section_classes = build_section_classes($section, 'contact');

    $section_title      = $section['contact_section_title'] ?? '';
    $section_hide_title = $section['contact_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['contact_section_lead'] ?? '';
    $contact            = $section['contact'] ?? '';
    $form_id            = $section['contact_form'] ?? '';

    // Determine column classes dynamically
    $contact_col_class = !empty($contact) ? 'col-lg-6' : '';
    $form_col_class    = !empty($contact) ? 'col-lg-6' : 'col-lg-12';
?>

<?php if (!empty($contact) || !empty($form_id)) : ?>
    <?php do_action('theme_section_open', [
        'id'      => $section_slug,
        'classes' => 'section section--contact' . esc_attr($section_classes),
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <?php do_action('theme_section_header', [
                'title'      => $section_title,
                'hide_title' => $section_hide_title,
                'lead'       => $section_lead,
            ]); ?>
            
            <div class="section__content row flex-lg-row flex-column-reverse">
                <?php if (!empty($contact)) : ?>
                    <div class="<?php echo esc_attr($contact_col_class); ?>">
                        <?php echo $contact; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($form_id)) : ?>
                    <div class="<?php echo esc_attr($form_col_class); ?>">
                        <?php
                            $template_args = [];
                            get_template_part('template-parts/forms/form', $form_id, $template_args);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php do_action('theme_section_container_close'); ?>

    <?php do_action('theme_section_close'); ?>
<?php endif; ?>

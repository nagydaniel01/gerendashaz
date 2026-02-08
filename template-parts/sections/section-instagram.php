<?php
    $section_classes = build_section_classes($section, 'gravity_forms');

    $section_title      = $section['gravity_forms_section_title'] ?? '';
    $section_hide_title = $section['gravity_forms_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['gravity_forms_section_lead'] ?? '';
?>

<section id="<?php echo esc_attr($section_slug); ?>" class="section section--instagram">
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
            <?php
            /*
            Instagram app name
            GerendásVendégház-IG
            
            Instagram app ID
            1424311469247678
            
            Instagram app secret
            51c094b24e89306b3b8ea22365ab4a86

            Instagram Business Account ID
            17841470970836622

            Access token
            IGAAUPZA1ssjL5BZAFlsdzdYc1o1bU1CNF9oSVdZAcGs4ZA2s3RUl1S2ZApSE10UElnOUd3bUxxUUVPaWx1aWxGVTNrc3p1OC1pd3IybEJSOXdZATlRlekRnMjRkVVhXb0FtdE1jVFJDMDd1OFZAuY0JpZAVVOUGpzSVBVWmVUOXdqUFhEOAZDZD
            */
            ?>
        </div>
    </div>
</section>

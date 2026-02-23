<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /**
     * Open a section wrapper.
     *
     * This function outputs the opening <section> tag with optional ID and classes.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $id      The ID of the section.
     *     @type string $classes Additional classes to add to the section. Default 'section'.
     * }
     */
    add_action('theme_section_open', function ($args = []) {
        $id      = !empty($args['id']) ? esc_attr($args['id']) : '';
        $classes = !empty($args['classes']) ? esc_attr($args['classes']) : 'section';

        echo "<section id=\"{$id}\" class=\"{$classes}\">";
    }, 10, 1);

    /**
     * Close a section wrapper.
     *
     * Outputs the closing </section> tag.
     */
    add_action('theme_section_close', function () {
        echo '</section>';
    });

    /**
     * Open a section container.
     *
     * Outputs the opening <div class="container"> tag.
     */
    add_action('theme_section_container_open', function ($args = []) {
        $classes = !empty($args['classes']) ? esc_attr($args['classes']) : 'container';
        echo "<div class=\"{$classes}\">";
    }, 10, 1);

    /**
     * Close a section container.
     *
     * Outputs the closing </div> tag.
     */
    add_action('theme_section_container_close', function () {
        echo '</div>';
    });

    /**
     * Render the section header.
     *
     * Displays a title and/or lead paragraph within a section.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string  $title      The title of the section.
     *     @type bool    $hide_title Whether to hide the title. Default false.
     *     @type string  $lead       Optional lead text/description for the section.
     * }
     */
    add_action('theme_section_header', function (array $args = []): void {
        $title            = isset($args['title']) ? trim((string) $args['title']) : '';
        $lead             = isset($args['lead']) ? trim((string) $args['lead']) : '';
        $hide_title       = !empty($args['hide_title']);
        $show_breadcrumbs = !empty($args['show_breadcrumbs']);
        $show_image       = !empty($args['show_image']);

        $output = '';

        // Breadcrumbs
        if ($show_breadcrumbs && function_exists('rank_math_the_breadcrumbs')) {
            ob_start();
            rank_math_the_breadcrumbs();
            $breadcrumbs = ob_get_clean();

            if ($breadcrumbs !== '') {
                $output .= $breadcrumbs;
            }
        }

        // Section title
        if (!$hide_title && $title !== '') {
            $output .= '<h1 class="section__title">' . esc_html($title) . '</h1>';
        }

        // Section lead
        if (!empty($lead)) {
            $output .= '<div class="section__lead">' . wp_kses_post($lead) . '</div>';
        }

        // Post thumbnail
        if ($show_image && has_post_thumbnail()) {
            $thumbnail_id = get_post_thumbnail_id();
            $alt_text     = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: get_the_title();

            ob_start();
            echo '<div class="section__image-wrapper">';

            the_post_thumbnail('large', [
                'class'         => 'section__image',
                'alt'           => esc_attr($alt_text),
                'loading'       => 'eager',
                'fetchpriority' => 'high',
                'decoding'      => 'async',
            ]);
            
            echo '</div>';
            
            $image_html = ob_get_clean();
            
            if ($image_html !== '') {
                $output .= $image_html;
            }
        }

        // Only print wrapper if there’s something
        if ($output !== '') {
            echo '<div class="section__header">' . $output . '</div>';
        }
    }, 10, 1);

    /**
     * Render the section title.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $section_title Section title.
     * }
     */
    add_action('theme_section_title', function ($args = []) {
        $section_title = $args['section_title'] ?? '';
        if (!empty($section_title)) {
            echo '<h2 class="section__title">' . esc_html($section_title) . '</h2>';
        }
    }, 10, 1);

    /**
     * Render the section lead/description.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $section_lead Section lead text.
     * }
     */
    add_action('theme_section_lead', function ($args = []) {
        $section_lead = $args['section_lead'] ?? '';
        if (!empty($section_lead)) {
            echo '<div class="section__lead">' . wp_kses_post($section_lead) . '</div>';
        }
    }, 10, 1);

    /**
     * Render the section featured image.
     *
     * Displays the post thumbnail for a given post ID.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int $post_id The post ID. Default current post ID.
     * }
     */
    add_action('theme_section_featured_image', function ($args = []) {
        $post_id = $args['post_id'] ?? get_the_ID();

        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (!$thumbnail_id) {
            return;
        }

        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: get_the_title($post_id);

        the_post_thumbnail(
            $post_id,
            'large',
            [
                'class' => 'section__image',
                'alt'   => esc_attr($alt_text),
            ]
        );
    }, 10, 1);

    /**
     * Open the section content wrapper.
     *
     * Outputs the opening <div class="section__content"> tag.
     */
    add_action('theme_section_content_open', function ($args = []) {
        $classes = !empty($args['classes']) ? esc_attr($args['classes']) : 'section__content';
        echo "<div class=\"{$classes}\">";
    }, 10, 1);

    /**
     * Close the section content wrapper.
     *
     * Outputs the closing </div> tag.
     */
    add_action('theme_section_content_close', function () {
        echo '</div>';
    });

    /**
     * Open a card wrapper.
     *
     * Outputs the opening <article> tag for a card with optional classes.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int    $post_id Post ID.
     *     @type string $classes Additional classes for the card.
     * }
     */
    add_action('theme_card_open', function ($args = []) {
        $post_id  = !empty($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
        $classes  = !empty($args['classes']) ? esc_attr($args['classes']) : 'card';
        $data_aos = !empty($args['data_aos']) ? esc_attr($args['data_aos']) : 'fade-up';

        echo "<article id=\"{$post_id}\" class=\"{$classes}\" data-aos=\"{$data_aos}\">";
    }, 10, 1);

    /**
     * Close the card wrapper.
     */
    add_action('theme_card_close', function () {
        echo '</article>';
    });

    /**
     * Open the card link wrapper.
     *
     * Outputs the <a> tag linking to the post.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int $post_id Post ID.
     * }
     */
    add_action('theme_card_link_open', function ($args = []) {
        $url    = '';
        $target = !empty($args['target']) ? esc_attr($args['target']) : '_self';
        
        // Allow empty classes intentionally
        $classes = isset($args['classes']) ? esc_attr($args['classes']) : 'card__link';

        // If $classes is an empty string, omit it entirely from the markup
        $class_attr = $classes !== '' ? ' class="' . $classes . '"' : '';

        /**
         * Priority:
         * 1. Custom URL
         * 2. Term link
         * 3. Post link (default)
         */

        // Custom URL (highest priority)
        if (!empty($args['url'])) {
            $url = $args['url'];
        }

        // Term object support
        elseif (!empty($args['term']) && $args['term'] instanceof WP_Term) {
            $url = get_term_link($args['term']);
        }

        // Term ID + taxonomy support
        elseif (!empty($args['term_id']) && !empty($args['taxonomy'])) {
            $url = get_term_link((int) $args['term_id'], $args['taxonomy']);
        }

        // Post ID support (default fallback)
        else {
            $post_id = $args['post_id'] ?? get_the_ID();
            $url     = get_permalink($post_id);
        }

        if (!$url || is_wp_error($url)) {
            return;
        }

        echo '<a href="' . esc_url($url) . '" target="' . $target . '"' . $class_attr . '>';
    }, 10, 1);

    /**
     * Close the card link wrapper.
     */
    add_action('theme_card_link_close', function () {
        echo '</a>';
    });

    /**
     * Render the card header image (featured or placeholder).
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int    $image_id Attachment ID.
     *     @type string $alt_text Alt text for the image.
     * }
     */
    add_action('theme_card_header', function ($args = []) {
        $image_id = $args['image_id'] ?? 0;
        $alt_text = $args['alt_text'] ?? '';

        // Nothing to show
        if (!$image_id && (!defined('PLACEHOLDER_IMG_SRC') || !PLACEHOLDER_IMG_SRC)) {
            return;
        }

        // Detect if image is SVG
        $mime_type = $image_id ? get_post_mime_type($image_id) : '';
        $is_svg    = $mime_type === 'image/svg+xml';

        $wrapper_class = $is_svg ? 'card__icon-wrapper' : 'card__image-wrapper';
        $image_class   = $is_svg ? 'card__icon imgtosvg' : 'card__image';

        echo '<div class="card__header">';
        echo '<div class="' . esc_attr($wrapper_class) . '">';

        if ($image_id) {
            echo wp_get_attachment_image(
                $image_id,
                'medium_large',
                false,
                [
                    'class'   => $image_class,
                    'alt'     => esc_attr($alt_text),
                    'loading' => 'lazy',
                ]
            );
        } else {
            // Placeholder image
            echo '<img width="150" height="150" src="' . esc_url(PLACEHOLDER_IMG_SRC) . '" alt="" class="card__image card__image--placeholder" loading="lazy">';
        }

        echo '</div>';
        echo '</div>';
    }, 10, 1);

    /**
     * Open the card content wrapper.
     */
    add_action('theme_card_content_open', function () {
        echo '<div class="card__content">';
    });

    /**
     * Close the card content wrapper.
     */
    add_action('theme_card_content_close', function () {
        echo '</div>';
    });

    /**
     * Render the card title.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $card_title Card title.
     * }
     */
    add_action('theme_card_title', function ($args = []) {
        $card_title = $args['card_title'] ?? '';
        if (!empty($card_title)) {
            echo '<h3 class="card__title">' . esc_html($card_title) . '</h3>';
        }
    }, 10, 1);

    /**
     * Render the card description/lead.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $card_description Card description.
     * }
     */
    add_action('theme_card_description', function ($args = []) {
        $card_description = $args['card_description'] ?? '';
        if (!empty($card_description)) {
            echo '<div class="card__lead">' . wp_kses_post($card_description) . '</div>';
        }
    }, 10, 1);

    /**
     * Render the card button.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type array $card_button Card button array with keys: url, title, target.
     * }
     */
    add_action('theme_card_button', function ($args = []) {
        $card_button   = $args['card_button'] ?? [];
        $button_url    = $card_button['url'] ?? '';
        $button_title  = $card_button['title'] ?? esc_url($button_url);
        $button_target = !empty($card_button['target']) ? $card_button['target'] : '_self';

        if ($button_url) {
            echo '<a href="' . esc_url($button_url) . '" target="' . esc_attr($button_target) . '" class="card__link btn btn-primary">' . esc_html($button_title) . '</a>';
        }
    }, 10, 1);

    /**
     * Render card post meta (category + date).
     */
    add_action('theme_card_meta', function ($args = []) {
        $post_id       = $args['post_id'] ?? get_the_ID();
        $show_category = isset($args['show_category']) ? (bool) $args['show_category'] : true;
        $show_date     = isset($args['show_date']) ? (bool) $args['show_date'] : true;

        $output = '';

        // Category
        if ($show_category) {
            $categories = get_the_terms($post_id, 'category');
            if (!is_wp_error($categories) && !empty($categories)) {
                $primary_category = '';

                if (function_exists('get_rank_math_primary_term_name')) {
                    $primary_category = get_rank_math_primary_term_name(null, 'category');
                }

                if (!$primary_category && !empty($categories[0]->name)) {
                    $primary_category = $categories[0]->name;
                }

                if ($primary_category) {
                    $output .= '<span class="card__category">' . esc_html($primary_category) . '</span>';
                }
            }
        }

        // Date
        if ($show_date) {
            $output .= '<time datetime="' . esc_attr(get_the_date('c', $post_id)) . '" class="card__date">' . esc_html(get_the_date('', $post_id)) . '</time>';
        }

        // Only print wrapper if there's content
        if ($output !== '') {
            echo '<div class="card__meta">' . $output . '</div>';
        }
    }, 10, 1);
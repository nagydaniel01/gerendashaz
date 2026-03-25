<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    if ( ! function_exists( 'get_current_url' ) ) {
        /**
         * Get the current URL of the page.
         * 
         * @return string Current URL.
         */
        function get_current_url() {
            global $wp;

            return esc_url( trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) ) );
        }
    }

    if ( ! function_exists( 'get_current_slug' ) ) {
        /**
         * Get the current page slug.
         * 
         * @return string Current page slug.
         */
        function get_current_slug() {
            global $wp;

            return add_query_arg( array(), $wp->request );
        }
    }

    if ( ! function_exists( 'get_template_id' ) ) {
        /**
         * Retrieves the ID of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return int|null ID of the page if found, null otherwise.
         */
        function get_template_id( $template_name ) {
            $page = get_pages( array(
                'hierarchical' => false,
                'meta_key'     => '_wp_page_template',
                'meta_value'   => $template_name,
                'number'       => 1,
            ) );
            
            if ( ! empty( $page ) && isset( $page[0]->ID ) ) {
                $page_id = $page[0]->ID;
                return $page_id;
            }
            return null;
        }
    }

    if ( ! function_exists( 'get_template_url' ) ) {
        /**
         * Retrieves the permalink of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return string|null Permalink of the page if found, null otherwise.
         */
        function get_template_url( $template_name ) {
            $page = get_pages( array(
                'hierarchical' => false,
                'meta_key'     => '_wp_page_template',
                'meta_value'   => $template_name,
                'number'       => 1,
            ) );

            if ( ! empty( $page ) && isset( $page[0]->ID ) ) {
                $permalink = get_permalink( $page[0]->ID );
                return $permalink ? $permalink : null; // ensure null instead of false
            }

            return null;
        }
    }

    if ( ! function_exists( 'get_template_name' ) ) {
        /**
         * Get the human-readable template name for a given post ID.
         *
         * @param int $post_id The ID of the post.
         * @return string The template name or 'Default template' if not custom.
         */
        function get_template_name( $post_id ) {
            if (!get_post($post_id)) {
                return '';
            }

            $template = get_post_meta($post_id, '_wp_page_template', true);

            if ($template === 'default' || empty($template)) {
                return __('Default template');
            }

            $template_path = locate_template($template);

            if (file_exists($template_path)) {
                $template_data = get_file_data($template_path, array('name' => 'Template Name'));
                return !empty($template_data['name']) ? $template_data['name'] : basename($template);
            }

            return basename($template); // fallback if file not found
        }
    }

    if ( ! function_exists( 'load_template_part' ) ) {
        /**
         * Loads a template part into a variable instead of displaying it.
         *
         * @param string $template_name Template slug.
         * @param string|null $part_name Optional. Template part name.
         * @return string Template part contents.
         */
        function load_template_part( $template_name, $part_name = null ) {
            ob_start();
            get_template_part( $template_name, $part_name );
            $var = ob_get_contents();
            ob_end_clean();
            return $var;
        }
    }

    if ( ! function_exists( 'get_post_id_by_meta' ) ) {
        /**
         * Get a post ID by a specific post meta key and value.
         *
         * @param string $key   The meta key to search for.
         * @param string $value The meta value to match.
         *
         * @return int|null Post ID if found, null otherwise.
         */
        function get_post_id_by_meta( $key, $value ) {
            global $wpdb;

            $query = $wpdb->prepare(
                "SELECT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = %s 
                AND meta_value = %s 
                LIMIT 1",
                $key,
                $value
            );

            $post_id = $wpdb->get_var( $query );

            return $post_id ? (int) $post_id : null;
        }
    }
    
    if ( ! function_exists( 'get_svg' ) ) {
        /**
         * Generate an SVG icon markup string.
         *
         * Builds an accessible SVG icon with optional title, description,
         * and fallback markup. Safely handles missing data and avoids runtime errors.
         *
         * @param string $icon The icon identifier (used as SVG symbol ID and class).
         * @param array  $args Optional arguments:
         *   - title (string) Accessible title.
         *   - desc (string) Accessible description.
         *   - fallback (bool) Whether to include fallback markup.
         *
         * @return string SVG markup or empty string on failure.
         */
        function get_svg( string $icon, array $args = array() ): string {

            // Fail early if icon is invalid.
            if ( empty( $icon ) || ! is_string( $icon ) ) {
                return '';
            }

            // Ensure WordPress helper exists.
            if ( ! function_exists( 'wp_parse_args' ) ) {
                return '';
            }

            $defaults = array(
                'title'    => '',
                'desc'     => '',
                'fallback' => false,
            );

            $args = wp_parse_args( $args, $defaults );

            // Initialize variables safely.
            $aria_hidden     = ' aria-hidden="true"';
            $aria_labelledby = '';
            $title_markup    = '';
            $fallback_markup = '';

            /**
             * Handle accessibility attributes.
             */
            if ( ! empty( $args['title'] ) && is_string( $args['title'] ) ) {

                $aria_hidden = '';

                // Generate unique ID safely.
                $unique_id = function_exists( 'uniqid' ) ? uniqid( 'svg-' ) : 'svg-' . mt_rand();

                $labelledby_ids = array( 'title-' . $unique_id );

                $title_markup = sprintf(
                    '<title id="title-%1$s">%2$s</title>',
                    esc_attr( $unique_id ),
                    esc_html( $args['title'] )
                );

                if ( ! empty( $args['desc'] ) && is_string( $args['desc'] ) ) {
                    $labelledby_ids[] = 'desc-' . $unique_id;

                    $title_markup .= sprintf(
                        '<desc id="desc-%1$s">%2$s</desc>',
                        esc_attr( $unique_id ),
                        esc_html( $args['desc'] )
                    );
                }

                $aria_labelledby = sprintf(
                    ' aria-labelledby="%s"',
                    esc_attr( implode( ' ', array_map( 'sanitize_html_class', $labelledby_ids ) ) )
                );
            }

            /**
             * Fallback markup.
             */
            if ( ! empty( $args['fallback'] ) ) {
                $fallback_markup = sprintf(
                    '<span class="svg-fallback icon-%s"></span>',
                    esc_attr( sanitize_html_class( $icon ) )
                );
            }

            /**
             * Build SVG output.
             * Wrapped in try/catch-like safety via checks to avoid warnings.
             */
            $icon_class = esc_attr( sanitize_html_class( $icon ) );

            if ( empty( $icon_class ) ) {
                return '';
            }

            return sprintf(
                '<svg class="icon %1$s" role="img"%2$s%3$s>%4$s<use xlink:href="#%1$s"></use>%5$s</svg>',
                $icon_class,
                $aria_hidden,
                $aria_labelledby,
                $title_markup,
                $fallback_markup
            );
        }
    }

    if ( ! function_exists( 'wp_kses_allowed_svg' ) ) {
        /**
         * Allow SVG elements in wp_kses_post output.
         *
         * Safely extends allowed HTML only when context is 'post'.
         *
         * @param array  $allowed Allowed HTML tags and attributes.
         * @param string $context  Context name.
         *
         * @return array Modified allowed HTML tags.
         */
        function wp_kses_allowed_svg( array $allowed, string $context ): array {

            if ( 'post' !== $context ) {
                return $allowed;
            }

            if ( ! is_array( $allowed ) ) {
                return $allowed;
            }

            // Ensure keys exist before modifying.
            if ( ! isset( $allowed['svg'] ) || ! is_array( $allowed['svg'] ) ) {
                $allowed['svg'] = array();
            }

            if ( ! isset( $allowed['use'] ) || ! is_array( $allowed['use'] ) ) {
                $allowed['use'] = array();
            }

            $allowed['svg'] = array_merge(
                $allowed['svg'],
                array(
                    'class' => true,
                    'role'  => true,
                    'aria-hidden' => true,
                    'aria-labelledby' => true,
                )
            );

            $allowed['use'] = array_merge(
                $allowed['use'],
                array(
                    'xlink:href' => true,
                )
            );

            return $allowed;
        }
        add_filter( 'wp_kses_allowed_html', 'wp_kses_allowed_svg', 10, 2 );
    }

    if ( ! function_exists( 'wp_safe_format_date' ) ) {
        /**
         * Safely format a date string into WordPress date format.
         *
         * @param mixed  $date_str       The input date string.
         * @param string $input_format   The format of the input date string. Default is 'd/m/Y'.
         * @param string $output_format  The desired output format. Default is the WordPress date format option.
         * @return string Formatted date or fallback message.
         */
        function wp_safe_format_date( $date_str, $input_format = 'd/m/Y', $output_format = '' ) {
            // Define fallback message
            $fallback = 'Invalid date.';

            // Use WordPress date format if no output format is provided
            if ( empty( $output_format ) ) {
                $output_format = get_option('date_format');
            }

            // Check if input is empty or not a string
            if ( empty($date_str) || !is_string($date_str) ) {
                return $fallback;
            }

            // Try to create a DateTime object from the input string
            try {
                $date = DateTime::createFromFormat( $input_format, $date_str );

                // Check for parsing errors
                $errors = DateTime::getLastErrors();
                if ( $date === false || ( $errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0) ) ) {
                    return $fallback;
                }

                // Format date according to the specified output format
                return date_i18n( $output_format, $date->getTimestamp() );

            } catch ( Exception $e ) {
                // Catch any unexpected exceptions
                return $fallback;
            }
        }
    }

    if ( ! function_exists( 'wp_safe_format_time' ) ) {
        /**
         * Safely format a time string into WordPress time format.
         * Handles multiple languages for AM/PM notation.
         *
         * @param mixed  $time_str       The input time string.
         * @param string $input_format   The format of the input time string. Default is 'h:i A'.
         * @param string $output_format  The desired output format. Default is the WordPress time format option.
         * @return string Formatted time or fallback message.
         */
        function wp_safe_format_time( $time_str, $input_format = 'H:i', $output_format = '' ) {
            $fallback = 'Invalid time.';

            if ( empty( $output_format ) ) {
                $output_format = get_option('time_format');
            }

            if ( empty($time_str) || !is_string($time_str) ) {
                return $fallback;
            }

            // Map common AM/PM notations in different languages to English
            $am_pm_map = [
                'am' => ['am', 'a.m.', 'vorm.', 'de.'], // English, German (vorm.), Hungarian (de.)
                'pm' => ['pm', 'p.m.', 'nachm.', 'du.'] // English, German (nachm.), Hungarian (du.)
            ];

            foreach ( $am_pm_map as $eng => $variants ) {
                $time_str = str_ireplace( $variants, $eng, $time_str );
            }

            try {
                $time = DateTime::createFromFormat( $input_format, $time_str );
                $errors = DateTime::getLastErrors();

                if ( $time === false || ( $errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0) ) ) {
                    return $fallback;
                }
                
                return date_i18n( $output_format, $time->getTimestamp() );
            } catch ( Exception $e ) {
                return $fallback;
            }
        }
    }

    if ( ! function_exists( 'wp_format_file_size' ) ) {
        /**
         * Format bytes into a human-readable file size string.
         *
         * @param int $bytes    The file size in bytes.
         * @param int $decimals The number of decimal places to include (default is 0).
         * @return string       The formatted file size string (e.g., "2 MB").
         */
        function wp_format_file_size($bytes, $decimals = 0) {
            $size = ['B','KB','MB','GB','TB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
        }
    }

    if ( ! function_exists( 'is_external_url' ) ) {
        /**
         * Check if a given URL is external.
         *
         * @param string      $url      The URL to check.
         * @param string|null $site_url Optional. The base site URL. Defaults to get_home_url().
         * @return bool True if the URL is external, false otherwise.
         */
        function is_external_url( $url, $site_url = null ) {
            if ( ! $site_url ) {
                $site_url = get_home_url();
            }

            $url = trim( $url );
            $site_url = rtrim( trim( $site_url ), '/' );

            // Ensure the URL is absolute
            if ( ! $url || strpos( $url, 'http' ) !== 0 ) {
                return false;
            }

            $url_host      = parse_url( $url, PHP_URL_HOST );
            $site_url_host = parse_url( $site_url, PHP_URL_HOST );

            return $url_host && $site_url_host && strcasecmp( $url_host, $site_url_host ) !== 0;
        }
    }

    if ( ! function_exists( 'get_estimated_reading_time' ) ) {
        /**
         * Estimate the reading time for content.
         *
         * @param string $content Content to analyze.
         * @param int    $wpm     Words per minute reading speed. Default is 300.
         *
         * @return int Estimated reading time in minutes.
         */
        function get_estimated_reading_time( $content = '', $wpm = 300 ) {
            $clean_content = strip_tags( strip_shortcodes( $content ) );
            $word_count    = str_word_count( $clean_content );

            return ceil( $word_count / $wpm );
        }
    }

    if ( ! function_exists( 'get_youtube_video_id' ) ) {
        /**
         * Extracts the YouTube video ID from a string containing a YouTube URL or iframe.
         *
         * Supports the following URL formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $input A YouTube iframe HTML string or URL.
         * @return string|false The extracted YouTube video ID, or false if not found.
         */
        function get_youtube_video_id( $input ) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];
    
            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $input, $matches ) ) {
                    return $matches[1] ?? '';
                }
            }
    
            return false;
        }
    }

    if ( ! function_exists( 'normalize_youtube_url' ) ) {
        /**
         * Normalizes any valid YouTube URL (embed, shortlink, watch, etc.)
         * into a standard YouTube watch URL.
         *
         * Supported formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $url The YouTube URL in any supported format.
         * @return string|false The normalized watch URL, or false if no video ID found.
         */
        function normalize_youtube_url($url) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&\?\/]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\&\?\/]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\&\?\/]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    return 'https://www.youtube.com/watch?v=' . $matches[1] ?? '';
                }
            }

            return false; // No match found
        }
    }

    if ( ! function_exists( 'get_youtube_thumbnail_url' ) ) {
        /**
         * Extract YouTube video ID from various URL formats and return a thumbnail URL.
         *
         * @param string $url     The YouTube video URL (can be embed, share, watch, etc.)
         * @param string $quality The desired thumbnail quality: default, mqdefault, hqdefault, sddefault, maxresdefault, 0, 1, 2, 3
         *
         * @return string|null    The full URL to the thumbnail image, or null if ID not found.
         */
        function get_youtube_thumbnail_url($url, $quality = 'maxresdefault') {
            // Define acceptable quality levels
            $valid_qualities = ['default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault', '0', '1', '2', '3'];
            $quality = in_array($quality, $valid_qualities) ? $quality : 'maxresdefault';

            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $url, $matches ) ) {
                    $video_id = $matches[1];
                    return "//img.youtube.com/vi/{$video_id}/{$quality}.jpg";
                }
            }

            return null;
        }
    }

    if ( ! function_exists( 'get_google_calendar_url' ) ) {
        /**
         * Generate a Google Calendar event URL.
         *
         * @param array $data Manual event data.
         * @return string
         */
        function get_google_calendar_url( $data = [] ) {

            // Ensure required fields exist
            if ( empty( $data['start'] ) || empty( $data['title'] ) ) {
                return '';
            }

            $summary     = $data['title'] ?? '';
            $description = $data['description'] ?? '';
            $location    = $data['location'] ?? '';
            $timezone    = wp_timezone_string();

            $start = ( new DateTime( $data['start'], new DateTimeZone( $timezone ) ) )
                ->format( 'Ymd\THis' );

            if ( ! empty( $data['end'] ) ) {
                $end = ( new DateTime( $data['end'], new DateTimeZone( $timezone ) ) )
                    ->format( 'Ymd\THis' );
            } else {
                $end = ( new DateTime( $data['start'], new DateTimeZone( $timezone ) ) )
                    ->modify( '+1 hour' )
                    ->format( 'Ymd\THis' );
            }

            $calendar_url  = 'https://www.google.com/calendar/render?action=TEMPLATE';
            $calendar_url .= '&text=' . rawurlencode( $summary );
            $calendar_url .= "&dates={$start}/{$end}";
            $calendar_url .= '&details=' . rawurlencode( $description );
            $calendar_url .= '&location=' . rawurlencode( $location );
            $calendar_url .= '&ctz=' . rawurlencode( $timezone );

            return esc_url( $calendar_url );
        }
    }

    if ( ! function_exists( 'generate_ics_content' ) ) {
        /**
         * Generate ICS calendar file content from event data.
         *
         * Converts event start/end times to UTC and formats them
         * according to the iCalendar (.ics) specification.
         *
         * @param array $event {
         *     Event data.
         *
         *     @type string $title       Event title.
         *     @type string $description  Event description.
         *     @type string $location     Event location.
         *     @type string $start       Event start datetime (Y-m-d H:i:s).
         *     @type string $end         Optional. Event end datetime (Y-m-d H:i:s).
         * }
         *
         * @return string ICS file content. Empty string if invalid.
         */
        function generate_ics_content( $event ) {
            $timezone = wp_timezone_string();

            if ( empty( $event['start'] ) ) {
                return '';
            }

            // Convert to DateTime objects in site timezone
            $start_dt = new DateTime( $event['start'], new DateTimeZone( $timezone ) );

            $end_dt = ! empty( $event['end'] )
                ? new DateTime( $event['end'], new DateTimeZone( $timezone ) )
                : ( clone $start_dt )->modify( '+1 hour' );

            // Convert to UTC (recommended for ICS compatibility)
            $start = $start_dt->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Ymd\THis\Z' );
            $end   = $end_dt->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Ymd\THis\Z' );

            // Escape line breaks for ICS format
            $summary     = str_replace( ["\r\n", "\n"], "\\n", $event['title'] ?? '' );
            $description = str_replace( ["\r\n", "\n"], "\\n", $event['description'] ?? '' );
            $location    = str_replace( ["\r\n", "\n"], "\\n", $event['location'] ?? '' );

            // Generate stable UID
            $uid = md5( $summary . $start ) . '@yoursite.com';

            // Build ICS content
            $ics  = "BEGIN:VCALENDAR\r\n";
            $ics .= "VERSION:2.0\r\n";
            $ics .= "PRODID:-//YourSite//NONSGML v1.0//EN\r\n";
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:{$uid}\r\n";
            $ics .= "DTSTAMP:" . gmdate( 'Ymd\THis\Z' ) . "\r\n";
            $ics .= "DTSTART:{$start}\r\n";
            $ics .= "DTEND:{$end}\r\n";
            $ics .= "SUMMARY:{$summary}\r\n";
            $ics .= "DESCRIPTION:{$description}\r\n";
            $ics .= "LOCATION:{$location}\r\n";
            $ics .= "END:VEVENT\r\n";
            $ics .= "END:VCALENDAR\r\n";

            return $ics;
        }
    }

    if ( ! function_exists( 'handle_ics_download_request' ) ) {
        /**
         * Handle ICS file download via query parameter.
         *
         * Example URL:
         * ?download_ics=1&title=...&start=...&end=...
         *
         * @return void
         */
        function handle_ics_download_request() {

            if ( isset( $_GET['download_ics'] ) ) {

                $event = [
                    'title'       => sanitize_text_field( $_GET['title'] ?? '' ),
                    'description' => sanitize_textarea_field( $_GET['description'] ?? '' ),
                    'location'    => sanitize_text_field( $_GET['location'] ?? '' ),
                    'start'       => sanitize_text_field( $_GET['start'] ?? '' ),
                    'end'         => sanitize_text_field( $_GET['end'] ?? '' ),
                ];

                $ics = generate_ics_content( $event );

                if ( empty( $ics ) ) {
                    wp_die( 'Invalid event data' );
                }

                header( 'Content-Type: text/calendar; charset=utf-8' );
                header( 'Content-Disposition: attachment; filename=event.ics' );

                echo $ics;
                exit;
            }
        }
        add_action( 'init', 'handle_ics_download_request' );
    }

    if ( ! function_exists( 'get_ics_download_url' ) ) {
        /**
         * Generate a URL that triggers ICS file download.
         *
         * @param array $data {
         *     Event data.
         *
         *     @type string $title       Event title.
         *     @type string $description  Event description.
         *     @type string $location     Event location.
         *     @type string $start       Event start datetime.
         *     @type string $end         Optional event end datetime.
         * }
         *
         * @return string Fully escaped URL for ICS download.
         */
        function get_ics_download_url( $data ) {

            return add_query_arg( [
                'download_ics' => 1,
                'title'        => rawurlencode( $data['title'] ?? '' ),
                'description'  => rawurlencode( $data['description'] ?? '' ),
                'location'     => rawurlencode( $data['location'] ?? '' ),
                'start'        => $data['start'] ?? '',
                'end'          => $data['end'] ?? '',
            ], home_url() );
        }
    }

    if ( ! function_exists( 'get_address_url' ) ) {
        /**
         * Generates a map or route link.
         *
         * @param string $address The address or destination.
         * @param string $type 'map' or 'route'. Default 'map'.
         * @param string $provider 'google' or 'waze'. Default 'google'.
         * @return string URL only.
         */
        function get_address_url($address, $type = 'map', $provider = 'google') {
            $encodedAddress = urlencode($address);

            if ($provider === 'waze') {
                // Waze links
                if ($type === 'route') {
                    return "https://waze.com/ul?q={$encodedAddress}&navigate=yes";
                }
                return "https://waze.com/ul?q={$encodedAddress}";
            }

            // Default: Google Maps
            if ($type === 'route') {
                return "https://www.google.com/maps/dir/?api=1&destination={$encodedAddress}&travelmode=driving";
            }

            return "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
        }
    }

    if ( ! function_exists( 'has_acf_section' ) ) {
        /**
         * Check if the current post has any ACF flexible sections
         *
         * @param int|null    $post_id     Optional. Post ID to check. Defaults to current post.
         * @param string|null $fallback_field Optional. Fallback ACF field to check if main sections are empty.
         * @return bool                    True if there is at least one section, false otherwise.
         */
        function has_acf_section( $post_id = null, $fallback_field = null ) {
            if ( ! function_exists( 'get_field' ) ) {
                return false; // ACF not active
            }

            $post_id  = $post_id ?: get_the_ID();
            $sections = get_field( 'sections', $post_id );

            // Use fallback field if provided and main sections are empty
            if ( ( empty( $sections ) || ! is_array( $sections ) ) && $fallback_field ) {
                $sections = get_field( $fallback_field, $post_id );
            }

            // Return true if sections is a non-empty array
            return ! empty( $sections ) && is_array( $sections );
        }
    }

    if ( ! function_exists( 'build_section_classes' ) ) {
        /**
         * Build CSS classes for a section based on its configuration.
         *
         * @param array  $section The section configuration array.
         * @param string $prefix  The prefix used for section keys (e.g., 'term_query').
         *
         * @return string A string of CSS classes for the section.
         */
        function build_section_classes(array $section, string $prefix = ''): string {
            $classes = '';

            $padding_top    = $section["{$prefix}_section_padding_top"] ?? '';
            $padding_bottom = $section["{$prefix}_section_padding_bottom"] ?? '';
            $bg_color       = $section["{$prefix}_section_bg_color"] ?? '';

            if ($padding_top) {
                $classes .= ' section--padding-top-' . $padding_top;
            }

            if ($padding_bottom) {
                $classes .= ' section--padding-bottom-' . $padding_bottom;
            }

            if ($bg_color) {
                $classes .= ' section--color-' . $bg_color;
            }

            return $classes;
        }
        // $section_classes = build_section_classes($section, 'post_query');
    }

    if ( ! function_exists( 'get_opening_hours' ) ) {
        /**
         * Retrieve formatted opening hours.
         *
         * This function converts opening hours from either:
         *  - ACF field structure (`$acf_mode = true`)
         *  - A simple multidimensional array (`$acf_mode = false`)
         *
         * The function returns a structured array containing translated day labels
         * along with open and close times as integers (hours).
         *
         * @param array $opening_hours Opening hours data from ACF fields or simple array.
         * @param bool  $acf_mode      Whether to parse as ACF fields (true) or simple array (false).
         *
         * @return array Returns an associative array of days with translated labels and open/close hours.
         */
        function get_opening_hours($opening_hours, $acf_mode = true) {

            // Day labels with translation support
            $days = [
                'monday'    => __('Monday', 'nagyigen2026'),
                'tuesday'   => __('Tuesday', 'nagyigen2026'),
                'wednesday' => __('Wednesday', 'nagyigen2026'),
                'thursday'  => __('Thursday', 'nagyigen2026'),
                'friday'    => __('Friday', 'nagyigen2026'),
                'saturday'  => __('Saturday', 'nagyigen2026'),
                'sunday'    => __('Sunday', 'nagyigen2026'),
            ];

            $result = [];

            foreach ($days as $key => $label) {

                if ($acf_mode) {
                    // ACF FIELD MODE
                    $status = $opening_hours[$key . '_status'] ?? 0;

                    if ($status) {
                        $open  = $opening_hours[$key . '_open'] ?? '';
                        $close = $opening_hours[$key . '_close'] ?? '';

                        if ($open && $close) {
                            $open_fmt  = (int) wp_safe_format_time($open, 'g:i a', 'G');
                            $close_fmt = (int) wp_safe_format_time($close, 'g:i a', 'G');

                            $result[$key] = ['label' => $label, 'open'  => $open_fmt, 'close' => $close_fmt];
                        } else {
                            $result[$key] = ['label' => $label, 'open'  => 0, 'close' => 0];
                        }
                    } else {
                        $result[$key] = ['label' => $label, 'open'  => 0, 'close' => 0];
                    }

                } else {
                    // SIMPLE ARRAY MODE
                    $open  = $opening_hours[$key]['open'] ?? 0;
                    $close = $opening_hours[$key]['close'] ?? 0;

                    if ($open == 0 && $close == 0) {
                        $result[$key] = ['label' => $label, 'open'  => 0, 'close' => 0];
                    } else {
                        $open_fmt  = (int) wp_safe_format_time(sprintf('%02d:00', $open));
                        $close_fmt = (int) wp_safe_format_time(sprintf('%02d:00', $close));

                        $result[$key] = ['label' => $label, 'open'  => $open_fmt, 'close' => $close_fmt];
                    }
                }
            }

            return $result;
        }
    }

    /*
    $opening_hours = get_field('opening_hours', 'option');
    $opening_hours = get_opening_hours($opening_hours, $acf_mode = true);

    // Debug
    echo '<pre>';
    var_dump($opening_hours);
    echo '</pre>';
    */

    if ( ! function_exists( 'get_shop_status' ) ) {
        /**
         * Get the current shop status (open, closing soon, closed).
         *
         * @param array       $opening_hours The array returned from get_opening_hours().
         * @param string|null $day Optional. Specific day key (monday, tuesday, etc.) to check.
         *
         * @return string One of 'open', 'closing_soon', or 'closed'.
         */
        function get_shop_status($opening_hours, $day = null) {

            if ( empty($day) ) {
                $day = strtolower( date('l') ); // e.g. 'monday'
            }

            if ( ! isset( $opening_hours[$day] ) ) {
                return 'closed';
            }

            $today = $opening_hours[$day];
            $open  = (int) $today['open'];
            $close = (int) $today['close'];

            if ( $open === 0 && $close === 0 ) {
                return 'closed';
            }

            $current_hour = (int) current_time('G'); // respects WP timezone

            if ( $current_hour < $open || $current_hour >= $close ) {
                return 'closed';
            }

            if ( ($close - $current_hour) <= 1 ) {
                return 'closing_soon';
            }

            return 'open';
        }
    }


    if ( ! function_exists( 'shop_status_shortcode' ) ) {
        /**
         * Shortcode to display current shop status.
         *
         * Usage: [shop_status]
         * Optional attributes:
         *   - label="true"   (shows text like "We are open!" instead of raw status)
         *   - day="monday"   (check another day manually)
         *
         * Example:
         *   [shop_status]
         *   [shop_status label="true"]
         *   [shop_status day="sunday"]
         */
        function shop_status_shortcode($atts) {
            $atts = shortcode_atts([
                'label' => 'false',
                'day'   => '',
            ], $atts, 'shop_status');

            // Get opening hours from ACF (adjust if stored elsewhere)
            $hours = get_opening_hours( get_field('opening_hours', 'option') );

            $status = get_shop_status($hours, $atts['day'] ?: null);

            if ( $atts['label'] === 'true' ) {
                switch ( $status ) {
                    case 'open':
                        return '<span class="shop-status open">' . __('We are open! 🟢', 'nagyigen2026') . '</span>';
                    case 'closing_soon':
                        return '<span class="shop-status closing-soon">' . __('Closing soon 🕒', 'nagyigen2026') . '</span>';
                    default:
                        return '<span class="shop-status closed">' . __('Closed 🔴', 'nagyigen2026') . '</span>';
                }
            }

            // Raw output (just "open", "closing_soon", "closed")
            return esc_html($status);
        }
        add_shortcode('shop_status', 'shop_status_shortcode');
    }

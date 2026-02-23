<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /**
     * ===============================================
     * COMPLETE WORDPRESS CLEANUP & PERFORMANCE OPTIMIZATION
     * ===============================================
     * This file removes unnecessary <head> metadata, emojis, REST API endpoints,
     * XML-RPC, pingbacks, HTTP headers, and disables or redirects feeds.
     * It also removes wp-embed for performance and blocks wp-json from robots.
     *
     * Safe to include in a theme's functions.php or as a custom plugin.
     */

    // ------------------------
    // 1. Clean <head> metadata
    // ------------------------
    if ( ! function_exists( 'wp_cleanup_head' ) ) {
        /**
         * Remove default WordPress <head> actions that inject metadata, scripts, and links.
         * Includes version info, RSS links, REST API links, oEmbed scripts, and emoji scripts.
         *
         * This function helps reduce page weight, improve privacy, and remove unnecessary output.
         */
        function wp_cleanup_head() {
            // Remove WordPress version number
            remove_action( 'wp_head', 'wp_generator' );

            // Remove Really Simple Discovery (RSD) link (used for remote publishing tools)
            remove_action( 'wp_head', 'rsd_link' );

            // Remove Windows Live Writer manifest link
            remove_action( 'wp_head', 'wlwmanifest_link' );

            // Remove index and adjacent posts links
            remove_action( 'wp_head', 'index_rel_link' );
            remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

            // Remove RSS feeds, shortlinks, REST API links
            remove_action( 'wp_head', 'feed_links', 2 );
            remove_action( 'wp_head', 'feed_links_extra', 3 );
            remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
            remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
            remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

            // Remove oEmbed discovery links and scripts
            remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
            remove_action( 'wp_head', 'wp_oembed_add_host_js' );

            // Remove emoji scripts and styles
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );

            // Remove emoji conversions in feeds and emails
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

            // Remove TinyMCE emoji plugin
            add_filter( 'tiny_mce_plugins', 'wp_disable_emojis_tinymce' );

            // Remove emoji DNS prefetch
            add_filter( 'wp_resource_hints', 'wp_disable_emojis_remove_dns_prefetch', 10, 2 );

            // Disable emoji SVG URL
            add_filter( 'emoji_svg_url', '__return_false' );
        }
        add_action( 'init', 'wp_cleanup_head' );
    }

    // ------------------------
    // 2. Emoji helpers
    // ------------------------
    if ( ! function_exists( 'wp_disable_emojis_tinymce' ) ) {
        /**
         * Remove the wpemoji plugin from TinyMCE.
         *
         * @param array $plugins Array of TinyMCE plugins.
         * @return array Filtered plugins without 'wpemoji'.
         */
        function wp_disable_emojis_tinymce( $plugins ) {
            if ( is_array( $plugins ) ) {
                return array_diff( $plugins, array( 'wpemoji' ) );
            }
            return array();
        }
    }

    if ( ! function_exists( 'wp_disable_emojis_remove_dns_prefetch' ) ) {
        /**
         * Remove the emoji CDN hostname from DNS prefetch hints.
         *
         * @param array  $urls          URLs to print for resource hints.
         * @param string $relation_type The relation type (e.g., 'dns-prefetch').
         * @return array Filtered URLs without the emoji CDN.
         */
        function wp_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
            if ( 'dns-prefetch' === $relation_type ) {
                $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
                $urls = array_diff( $urls, array( $emoji_svg_url ) );
            }
            return $urls;
        }
    }

    // ------------------------
    // 3. Disable self-pingbacks
    // ------------------------
    if ( ! function_exists( 'disable_self_pingbacks' ) ) {
        /**
         * Prevent WordPress from sending pingbacks to your own site when linking to internal posts.
         *
         * @param array $links Array of URLs to ping.
         */
        function disable_self_pingbacks( &$links ) {
            $home = get_option( 'home' );
            foreach ( $links as $l => $link ) {
                if ( strpos( $link, $home ) === 0 ) {
                    unset( $links[$l] );
                }
            }
        }
        add_action( 'pre_ping', 'disable_self_pingbacks' );
    }

    // ------------------------
    // 4. Disable XML-RPC
    // ------------------------
    /**
     * Completely disable XML-RPC to prevent remote login attempts or pingbacks.
     */
    add_filter( 'xmlrpc_enabled', '__return_false' );

    // ------------------------
    // 5. Disable REST API
    // ------------------------
    /**
     * Disable REST API endpoints for non-logged-in users.
     */
    add_filter( 'rest_enabled', '__return_false' );
    add_filter( 'rest_jsonp_enabled', '__return_false' );

    // ------------------------
    // 6. Clean HTTP headers
    // ------------------------
    if ( ! function_exists( 'clean_wp_headers' ) ) {
        /**
         * Remove unwanted HTTP headers such as X-Pingback and X-Powered-By.
         *
         * @param array $headers Array of HTTP headers.
         * @return array Filtered headers.
         */
        function clean_wp_headers( $headers ) {
            if ( isset( $headers['X-Pingback'] ) ) unset( $headers['X-Pingback'] );
            if ( isset( $headers['X-Powered-By'] ) ) unset( $headers['X-Powered-By'] );
            return $headers;
        }
        add_filter( 'wp_headers', 'clean_wp_headers' );
    }

    // ------------------------
    // 7. Redirect all feeds to homepage
    // ------------------------
    if ( ! function_exists( 'redirect_feeds_to_homepage' ) ) {
        /**
         * Redirect any feed request to the homepage with a 301 permanent redirect.
         */
        function redirect_feeds_to_homepage() {
            if ( is_feed() ) {
                wp_redirect( home_url(), 301 );
                exit;
            }
        }
        add_action( 'template_redirect', 'redirect_feeds_to_homepage' );
    }

    // ------------------------
    // 8. Block WP-JSON API from robots
    // ------------------------
    if ( ! function_exists( 'block_wp_json_from_robots' ) ) {
        /**
         * Add robots.txt rules to block WordPress JSON API endpoints.
         *
         * @param string $output  Existing robots.txt output.
         * @param bool   $public  Whether the site is publicly visible.
         * @return string Updated robots.txt output.
         */
        function block_wp_json_from_robots( $output, $public ) {
            if ( $public ) {
                $output .= "Disallow: /wp-json/\n";
                $output .= "Disallow: /?rest_route=/\n";
            }
            return $output;
        }
        add_filter( 'robots_txt', 'block_wp_json_from_robots', 10, 2 );
    }

    // ------------------------
    // 9. Remove wp-embed script
    // ------------------------
    if ( ! function_exists( 'remove_embed_script' ) ) {
        /**
         * Remove the default WordPress wp-embed script to save page weight and resources.
         */
        function remove_embed_script() {
            wp_deregister_script( 'wp-embed' );
        }
        add_action( 'wp_footer', 'remove_embed_script' );
    }
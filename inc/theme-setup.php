<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'theme_setup' ) ) {
        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which runs
         * before the init hook. The init hook is too late for some features, such as indicating
         * support post thumbnails.
         * 
         * @return void
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/
         */
        function theme_setup() {
            /** 
             * Post thumbnail
             */
            add_theme_support( 'post-thumbnails' );
            //set_post_thumbnail_size( 150, 150 ); // 50 pixels wide by 50 pixels tall, resize mode

            // Add image sizes
            //add_image_size( 'product-sticky-thumbnail', 0, 64, false );

            /**
             * Supported formats
             * @link https://developer.wordpress.org/themes/functionality/post-formats/#supported-formats
             */
            add_theme_support( 
                'post-formats', 
                array(
                    'aside',
                    'gallery',
                    'link',
                    'image',
                    'quote',
                    'status',
                    'video',
                    'audio',
                    'chat'
                ) 
            );
            /* Supported formats END */

            /**
             * Feed Links
             */
            add_theme_support( 'automatic-feed-links' );
            
            /**
             * Title Tag
             */
            add_theme_support( 'title-tag' );

            /**
             * HTML5
             */
            add_theme_support(
                'html5', 
                array( 
                    'comment-list', 
                    'comment-form', 
                    'search-form', 
                    'gallery', 
                    'caption', 
                    'style', 
                    'script'
                )
            );

            /**
             *  Custom Logo
             */
            add_theme_support( 
                'custom-logo', 
                array(
                    'height'               => 100,
                    'width'                => 400,
                    'flex-height'          => true,
                    'flex-width'           => true,
                    'header-text'          => array( 'site-title', 'site-description' ),
                    'unlink-homepage-logo' => true,
                ) 
            );

            /**
             * Editor Style.
             */
            add_editor_style( 'classic-editor.css' );

            /**
             * Block Editor Theme Support
             */
            add_theme_support( 'align-wide' );
            add_theme_support( 'wp-block-styles' );
            add_theme_support( 'responsive-embeds' );

            /**
             * Register theme support for Rank Math breadcrumbs
             */
            add_theme_support( 'rank-math-breadcrumbs' );

            /**
             * WooCommerce.
             */
            // WooCommerce in general.
            add_theme_support( 'woocommerce' );

            // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
            if ( class_exists( 'WooCommerce' ) ) {
                //add_theme_support( 'wc-product-gallery-zoom' );       // zoom
                //add_theme_support( 'wc-product-gallery-lightbox' );   // lightbox
                //add_theme_support( 'wc-product-gallery-slider' );     // swipe
            }

            // Add support for LearnDash features
            add_theme_support( 'learndash' );

            // Restoring the classic Widgets Editor
            remove_theme_support( 'widgets-block-editor' );

            // Register menu
            register_nav_menus(
                array(
                    'primary_menu'      => __( 'Header menu', 'gerendashaz' ),
                    'footer_menu_1'     => __( 'Footer menu 1', 'gerendashaz' ),
                    'footer_menu_2'     => __( 'Footer menu 2', 'gerendashaz' ),
                    'footer_menu_3'     => __( 'Footer menu 3', 'gerendashaz' ),
                    'footer_menu_4'     => __( 'Footer menu 4', 'gerendashaz' )
                )
            );

        }
        add_action( 'after_setup_theme', 'theme_setup' );
    }

    if ( ! function_exists( 'theme_textdomain_init' ) ) {
        /**
         * Loads the theme textdomain for translations.
         *
         * @return void
         */
        function theme_textdomain_init() {
            load_theme_textdomain( 'gerendashaz', get_template_directory() . '/languages' );
            //load_theme_textdomain( 'gerendashaz', WP_LANG_DIR . '/themes' );
        }
        add_action( 'init', 'theme_textdomain_init' );
    }

    if ( ! function_exists( 'theme_init' ) ) {
        /**
         * Initializes additional features like taxonomy support for custom post types.
         *
         * @return void
         */
        function theme_init() {
            //add_post_type_support( 'post', 'post-formats' );
            //add_post_type_support( 'page', 'excerpt' );

            //remove_post_type_support( 'post', 'post-formats' );
            //remove_post_type_support( 'page', 'excerpt' );

            //register_taxonomy_for_object_type( 'post_tag', 'page' );
        }
        add_action( 'init', 'theme_init' );
    }

    if ( ! function_exists( 'custom_set_image_sizes' ) ) {
        /**
         * Set custom WordPress image sizes.
         *
         * Updates the default thumbnail, medium, medium large and large image sizes
         * and optionally sets hard cropping for thumbnails.
         * Runs on theme setup.
         *
         * @return void
         */
        function custom_set_image_sizes() {
            // Thumbnail (square avatars/cards)
            update_option( 'thumbnail_size_w', 220 );
            update_option( 'thumbnail_size_h', 220 );
            update_option( 'thumbnail_crop', 1 );

            // Medium (2× thumbnail for retina)
            update_option( 'medium_size_w', 440 );
            update_option( 'medium_size_h', 440 );

            // Medium Large (card images)
            update_option( 'medium_large_size_w', 660 );
            update_option( 'medium_large_size_h', 660 );

            // Large (full-width content images)
            update_option( 'large_size_w', 1320 );
            update_option( 'large_size_h', 1320 );
        }
        add_action( 'after_switch_theme', 'custom_set_image_sizes' );
    }

    if ( ! function_exists( 'wp_remove_default_image_sizes' ) ) {
        /**
         * Removes unused default WordPress image sizes.
         *
         * This prevents WordPress from generating unnecessary thumbnails
         * during image uploads, reducing storage usage and processing time.
         *
         * @return void
         */
        function wp_remove_default_image_sizes() {
            // Default WP sizes
            remove_image_size( '1536x1536' );
            remove_image_size( '2048x2048' );
            
            // If not needed, also remove these (optional)
            // remove_image_size( 'thumbnail' );
            // remove_image_size( 'medium' );
            // remove_image_size( 'medium_large' );
            // remove_image_size( 'large' );
        }
        add_action( 'init', 'wp_remove_default_image_sizes' );
    }

    if ( ! function_exists( 'wp_unset_intermediate_image_sizes' ) ) {
        /**
         * Filters out unwanted intermediate image sizes.
         *
         * @param array $sizes Registered image sizes.
         * @return array Cleaned image sizes.
         */
        function wp_unset_intermediate_image_sizes( $sizes ) {

            unset( $sizes['thumbnail'] );
            unset( $sizes['medium'] );
            unset( $sizes['medium_large'] );
            unset( $sizes['large'] );
            unset( $sizes['1536x1536'] );
            unset( $sizes['2048x2048'] );

            // WooCommerce examples (remove if not used)
            /*
            unset( $sizes['woocommerce_thumbnail'] );
            unset( $sizes['woocommerce_single'] );
            unset( $sizes['woocommerce_gallery_thumbnail'] );
            */

            return $sizes;
        }
        //add_filter( 'intermediate_image_sizes_advanced', 'wp_unset_intermediate_image_sizes' );
    }

    // Enable native lazy loading for images
    add_filter( 'wp_lazy_loading_enabled', '__return_true' );

    /*
    if ( wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ) {
        echo 'Lazy loading is enabled';
    } else {
        echo 'Lazy loading is not enabled';
    }
    */

    if ( ! function_exists( 'force_lazyload_wp_block_images' ) ) {
        /**
         * Force lazy loading on all <img> tags inside wp-block-image blocks in post content.
         *
         * This function scans the post content and ensures that every <img> tag within
         * `wp-block-image` blocks has the `loading="lazy"` attribute. If the attribute
         * is missing, it will be added.
         *
         * @param string $content The post content.
         *
         * @return string Modified post content with enforced lazy loading on images.
         */
        function force_lazyload_wp_block_images( $content ) {
            // Add lazyload to <img> inside wp-block-image if missing
            $content = preg_replace(
                '/(<img[^>]+)(?<!loading=["\']lazy["\'])/',
                '$1 loading="lazy"',
                $content
            );
            return $content;
        }
        add_filter( 'the_content', 'force_lazyload_wp_block_images' );
    }

    if ( ! function_exists( 'theme_body_classes' ) ) {
        /**
         * Modifies the body_class output to remove unwanted classes.
         *
         * @param array $classes The current body classes.
         * @return array Modified body classes.
         */
        function theme_body_classes( $classes ) {
            // Remove the 'page' class
            if ( ( $key = array_search( 'page', $classes ) ) !== false ) {
                unset( $classes[ $key ] );
            }
        
            // Add a custom class
            //$classes[] = 'my-custom-class';

            $is_woocommerce_page = false;

            // Only check WooCommerce conditions if plugin is active
            if ( class_exists( 'WooCommerce' ) ) {
                if ( is_woocommerce() ) {
                    $is_woocommerce_page = true;
                }
            }

            // If not a WooCommerce page, add the 'woocommerce' class
            if ( ! $is_woocommerce_page && ! in_array( 'woocommerce', $classes, true ) ) {
                $classes[] = 'woocommerce';
            }
        
            return $classes;
        }
        add_filter( 'body_class', 'theme_body_classes' );
    }

    if ( ! function_exists( 'theme_mime_types' ) ) {
        /**
         * Adds SVG file support to the list of allowed mime types for uploads.
         *
         * @param array $mimes Existing allowed mime types.
         * @return array Modified mime types.
         */
        function theme_mime_types( $mimes ) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }
        add_filter( 'upload_mimes', 'theme_mime_types' );
    }

    // Flush rewrite rules on theme activation to register the endpoint
    add_action( 'after_switch_theme', 'flush_rewrite_rules' );

    if ( ! function_exists( 'add_environment_pill' ) ) {
        /**
         * Displays a small floating environment indicator (pill) on the frontend.
         *
         * Only displays for non-production environments. The pill appears in the
         * bottom-left corner of the page with a color corresponding to the environment.
         *
         * @since 1.0.0
         */
        function add_environment_pill() {
            // Check if environment constant exists
            if ( defined('WP_ENVIRONMENT_TYPE') ) {
                $env = strtolower( trim( WP_ENVIRONMENT_TYPE ) );

                // Skip display for production
                if ( $env === 'production' ) {
                    return;
                }

                // Map environment to color, use a fallback for unknown environments
                $colors = [
                    'development' => 'rgb(231 76 60 / 50%)', // red
                    'staging'     => 'rgb(243 156 18 / 50%)', // orange
                ];

                $color = $colors[$env] ?? '#3498db'; // default blue

                // Sanitize environment name for safe output
                $env_safe = esc_html( ucfirst($env) );

                // Output the pill
                echo "<div style='position:fixed;bottom:10px;left:10px;padding:4px 8px 4px 8px;font-size:12px;background-color:$color;color:#fff;z-index:9999;pointer-events:none;'>$env_safe</div>";
            }
        }
        add_action( 'wp_footer', 'add_environment_pill' );
    }

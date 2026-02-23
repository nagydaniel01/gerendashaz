<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'force_resize_and_overwrite_images' ) ) {
        /**
         * Force resize of uploaded images and overwrite the original file.
         *
         * Hooks into `wp_handle_upload` and resizes images that exceed the defined
         * maximum dimensions. The original uploaded file is replaced with the resized
         * version to save storage and bandwidth.
         *
         * @param array $upload {
         *     Array of upload data.
         *
         *     @type string $file Path to the uploaded file.
         *     @type string $url  URL to the uploaded file.
         *     @type string $type MIME type of the uploaded file.
         * }
         *
         * @return array Modified (or unmodified) upload data array.
         */
        function force_resize_and_overwrite_images( $upload ) {
            // Ensure required keys exist
            if ( empty( $upload['file'] ) || empty( $upload['type'] ) ) {
                return $upload;
            }

            $file = $upload['file'];
            $type = $upload['type'];

            // Only process image MIME types
            if ( strpos( $type, 'image/' ) !== 0 ) {
                return $upload;
            }

            $max_width  = 1920;
            $max_height = 1920;

            $editor = wp_get_image_editor( $file );

            if ( is_wp_error( $editor ) ) {
                return $upload;
            }

            $size = $editor->get_size();

            // Bail if size data is missing
            if ( empty( $size['width'] ) || empty( $size['height'] ) ) {
                return $upload;
            }

            // Resize only if the image exceeds max dimensions
            if ( $size['width'] > $max_width || $size['height'] > $max_height ) {

                // Maintain aspect ratio (no crop)
                $editor->resize( $max_width, $max_height, false );

                // Set JPEG compression quality (ignored by some formats)
                $editor->set_quality( 82 );

                /**
                 * Overwrite the original uploaded file with the resized version.
                 * This prevents large originals from being stored on the server.
                 */
                $editor->save( $file );

                // Clear PHP file status cache
                clearstatcache( true, $file );
            }

            return $upload;
        }
        add_filter( 'wp_handle_upload', 'force_resize_and_overwrite_images' );
    }

    if ( ! function_exists( 'clean_uploaded_filename' ) ) {
        /**
         * Clean uploaded filenames by removing accents and all special characters.
         *
         * Only letters, numbers, dashes, and underscores are allowed.
         * Handles errors and empty filenames safely.
         *
         * @param string $filename Original filename.
         * @return string Sanitized filename.
         */
        function clean_uploaded_filename( $filename ) {
            if ( empty( $filename ) || ! is_string( $filename ) ) {
                return 'file-' . time(); // Unique fallback
            }

            // Separate filename and extension
            $info = pathinfo( $filename );
            $name = isset( $info['filename'] ) ? $info['filename'] : 'file';
            $ext  = isset( $info['extension'] ) ? '.' . $info['extension'] : '';

            // Remove accents
            $name = function_exists('remove_accents') ? remove_accents( $name ) : $name;

            // Remove all special characters except letters, numbers, dash, and underscore
            $name = preg_replace( '/[^A-Za-z0-9\-_]/', '-', $name );

            // Replace multiple consecutive dashes with single dash
            $name = preg_replace( '/-+/', '-', $name );

            // Trim dashes from beginning and end
            $name = trim( $name, '-' );

            // Ensure name is not empty after cleaning
            if ( empty( $name ) ) {
                $name = 'file-' . time();
            }

            // Convert entire filename to lowercase using multibyte-safe function
            $name = mb_strtolower( $name, 'UTF-8' );
            $ext  = mb_strtolower( $ext, 'UTF-8' );

            return $name . $ext;
        }
        add_filter( 'sanitize_file_name', 'clean_uploaded_filename', 10 );
    }
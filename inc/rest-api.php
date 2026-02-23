<?php
    if ( ! function_exists( 'turn_off_rest_api_not_logged_in' ) ) {
        /**
         * Restrict REST API access to logged-in users only.
         *
         * This function checks if the current user is logged in. 
         * If not, it returns a WP_Error object to block REST API access.
         *
         * @param null|WP_Error $errors Existing authentication errors, if any.
         * @return null|WP_Error WP_Error if the user is not logged in, otherwise returns $errors.
         */
        function turn_off_rest_api_not_logged_in( $errors ) {

            // If there is already an authentication error, return it
            if ( is_wp_error( $errors ) ) {
                return $errors;
            }

            // Check if the user is logged in
            if ( ! is_user_logged_in() ) {
                return new WP_Error(
                    'no_rest_api_sorry',
                    'REST API access is not allowed for non-logged-in users.',
                    array( 'status' => 401 )
                );
            }

            // No errors, allow REST API access
            return $errors;
        }
        //add_filter( 'rest_authentication_errors', 'turn_off_rest_api_not_logged_in' );
    }

    if ( ! function_exists( 'remove_rest_api_users_endpoint' ) ) {
        /**
         * Remove REST API endpoints for users.
         *
         * This function removes the default WordPress REST API endpoints
         * that allow access to user data, helping to prevent exposure of user info.
         *
         * @param array $rest_endpoints List of registered REST API endpoints.
         * @return array Modified list of REST API endpoints.
         */
        function remove_rest_api_users_endpoint( $rest_endpoints ) {

            // Remove endpoint that lists all users
            if ( isset( $rest_endpoints['/wp/v2/users'] ) ) {
                unset( $rest_endpoints['/wp/v2/users'] );
            }

            // Remove endpoint for individual user by ID
            if ( isset( $rest_endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
                unset( $rest_endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
            }

            return $rest_endpoints;
        }
        //add_filter( 'rest_endpoints', 'remove_rest_api_users_endpoint' );
    }

    if ( ! function_exists( 'remove_rest_api_posts_endpoint' ) ) {
        /**
         * Remove REST API endpoints for posts.
         *
         * This function removes the default WordPress REST API endpoints
         * that allow access to posts, helping to restrict REST API exposure.
         *
         * @param array $rest_endpoints List of registered REST API endpoints.
         * @return array Modified list of REST API endpoints.
         */
        function remove_rest_api_posts_endpoint( $rest_endpoints ) {

            // Remove endpoint that lists all posts
            if ( isset( $rest_endpoints['/wp/v2/posts'] ) ) {
                unset( $rest_endpoints['/wp/v2/posts'] );
            }

            // Remove endpoint for individual post by ID
            if ( isset( $rest_endpoints['/wp/v2/posts/(?P<id>[\d]+)'] ) ) {
                unset( $rest_endpoints['/wp/v2/posts/(?P<id>[\d]+)'] );
            }

            return $rest_endpoints;
        }
        //add_filter( 'rest_endpoints', 'remove_rest_api_posts_endpoint' );
    }

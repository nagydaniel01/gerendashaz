<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    if ( ! function_exists( 'register_faq_post_type' ) ) {
        /**
         * Registers the "Gyakori kérdések" custom post type.
         *
         * Slug: faq
         * Icon: dashicons-editor-help
         *
         * This post type is used for Frequently Asked Questions.
         * It is private (not publicly queryable), but available in the admin UI.
         *
         * @return void
         */
        function register_faq_post_type() {
            $labels = array(
                'name'                  => _x( 'Frequently Asked Questions', 'Post Type General Name', 'gerendashaz' ),
                'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'gerendashaz' ),
                'menu_name'             => __( 'Frequently Asked Questions', 'gerendashaz' ),
                'name_admin_bar'        => __( 'FAQ', 'gerendashaz' ),
                'archives'              => __( 'FAQ Archives', 'gerendashaz' ),
                'attributes'            => __( 'FAQ Attributes', 'gerendashaz' ),
                'parent_item_colon'     => __( 'Parent FAQ:', 'gerendashaz' ),
                'all_items'             => __( 'All FAQs', 'gerendashaz' ),
                'add_new_item'          => __( 'Add New FAQ', 'gerendashaz' ),
                'add_new'               => __( 'Add New FAQ', 'gerendashaz' ),
                'new_item'              => __( 'New FAQ', 'gerendashaz' ),
                'edit_item'             => __( 'Edit FAQ', 'gerendashaz' ),
                'update_item'           => __( 'Update FAQ', 'gerendashaz' ),
                'view_item'             => __( 'View FAQ', 'gerendashaz' ),
                'view_items'            => __( 'View FAQs', 'gerendashaz' ),
                'search_items'          => __( 'Search FAQs', 'gerendashaz' ),
                'not_found'             => __( 'No FAQs found', 'gerendashaz' ),
                'not_found_in_trash'    => __( 'No FAQs found in Trash', 'gerendashaz' ),
                'featured_image'        => __( 'Featured Image', 'gerendashaz' ),
                'set_featured_image'    => __( 'Set featured image', 'gerendashaz' ),
                'remove_featured_image' => __( 'Remove featured image', 'gerendashaz' ),
                'use_featured_image'    => __( 'Use as featured image', 'gerendashaz' ),
                'insert_into_item'      => __( 'Insert into FAQ', 'gerendashaz' ),
                'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'gerendashaz' ),
                'items_list'            => __( 'FAQ list', 'gerendashaz' ),
                'items_list_navigation' => __( 'FAQ list navigation', 'gerendashaz' ),
                'filter_items_list'     => __( 'Filter FAQ list', 'gerendashaz' ),
            );

            $args = array(
                'label'                 => __( 'FAQ', 'gerendashaz' ),
                'description'           => __( '', 'gerendashaz' ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor' ),
                'taxonomies'            => array(),
                'hierarchical'          => false,
                'public'                => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 15,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'capability_type'       => 'post',
                'rewrite'               => array(),
                'menu_icon'             => 'dashicons-editor-help',
            );

            register_post_type( 'faq', $args );
        }
        add_action( 'init', 'register_faq_post_type', 0 );
    }

    if ( ! function_exists( 'register_apartment_post_type' ) ) {
        /**
         * Registers the "Apartment" custom post type.
         *
         * Slug: apartment
         * Icon: dashicons-building
         *
         * This post type is used to list apartments.
         *
         * @return void
         */
        function register_apartment_post_type() {
            $labels = array(
                'name'                  => _x( 'Apartments', 'Post Type General Name', 'gerendashaz' ),
                'singular_name'         => _x( 'Apartment', 'Post Type Singular Name', 'gerendashaz' ),
                'menu_name'             => __( 'Apartments', 'gerendashaz' ),
                'name_admin_bar'        => __( 'Apartment', 'gerendashaz' ),
                'archives'              => __( 'Apartment Archives', 'gerendashaz' ),
                'attributes'            => __( 'Apartment Attributes', 'gerendashaz' ),
                'parent_item_colon'     => __( 'Parent Apartment:', 'gerendashaz' ),
                'all_items'             => __( 'All Apartments', 'gerendashaz' ),
                'add_new_item'          => __( 'Add New Apartment', 'gerendashaz' ),
                'add_new'               => __( 'Add New Apartment', 'gerendashaz' ),
                'new_item'              => __( 'New Apartment', 'gerendashaz' ),
                'edit_item'             => __( 'Edit Apartment', 'gerendashaz' ),
                'update_item'           => __( 'Update Apartment', 'gerendashaz' ),
                'view_item'             => __( 'View Apartment', 'gerendashaz' ),
                'view_items'            => __( 'View Apartments', 'gerendashaz' ),
                'search_items'          => __( 'Search Apartments', 'gerendashaz' ),
                'not_found'             => __( 'No apartments found', 'gerendashaz' ),
                'not_found_in_trash'    => __( 'No apartments found in Trash', 'gerendashaz' ),
                'featured_image'        => __( 'Featured Image', 'gerendashaz' ),
                'set_featured_image'    => __( 'Set featured image', 'gerendashaz' ),
                'remove_featured_image' => __( 'Remove featured image', 'gerendashaz' ),
                'use_featured_image'    => __( 'Use as featured image', 'gerendashaz' ),
                'insert_into_item'      => __( 'Insert into Apartment', 'gerendashaz' ),
                'uploaded_to_this_item' => __( 'Uploaded to this Apartment', 'gerendashaz' ),
                'items_list'            => __( 'Apartment list', 'gerendashaz' ),
                'items_list_navigation' => __( 'Apartment list navigation', 'gerendashaz' ),
                'filter_items_list'     => __( 'Filter Apartment list', 'gerendashaz' ),
            );

            $args = array(
                'label'                 => __( 'Apartment', 'gerendashaz' ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor', 'thumbnail' ),
                'taxonomies'            => array(),
                'hierarchical'          => false,
                'public'                => true,
                'show_in_rest'          => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'post',
                'rewrite'               => array(
                    'slug' => _x( 'apartments', 'URL slug for apartments', 'gerendashaz' ),
                    'with_front' => false
                ),
                'menu_icon'             => 'dashicons-building',
            );

            register_post_type( 'apartment', $args );
        }
        add_action( 'init', 'register_apartment_post_type', 0 );
    }
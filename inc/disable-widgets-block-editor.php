<?php 
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    // Disable block editor for widgets
    add_filter( 'use_widgets_block_editor', '__return_false' );